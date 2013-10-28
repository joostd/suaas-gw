<?php

include('../vendor/xmlseclibs/xmlseclibs.php');
include('./saml.php');
include('../config.php');

/*
 * Gateway ACS endpoint: bridge to SP
 */

$response = base64_decode($_POST['SAMLResponse']);
$dom = new DOMDocument();
$dom->loadXML($response);

// TODO support multiple IDPs
$idp_entityID = array_keys( $config['idp'] )[0];   // TODO: look up using issuer. For now, take first one
$idp = $config['idp'][$idp_entityID];

// First, check the response from the IDP...

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('samlp', "urn:oasis:names:tc:SAML:2.0:protocol");
$query = "string(//samlp:Response/samlp:Status/samlp:StatusCode/@Value)";
$statusCode = $xpath->evaluate($query, $dom);
if (!$statusCode) {
    throw new Exception('Could not locate StatusCode value');
}
error_log("StatusCode is $statusCode");
// TODO handle statuscodes other than urn:oasis:names:tc:SAML:2.0:status:Success

// verify signature

$cert = $idp['certfile'];
if (!file_exists($cert)) {
    throw new Exception('Could not find verification certificate file: ' . $cert);
}

$objXMLSecDSig = new XMLSecurityDSig();
$objXMLSecDSig->idKeys[] = 'ID';

$signatureElement = $objXMLSecDSig->locateSignature($dom);
if  (!$signatureElement) {
    throw new Exception('Could not locate XML Signature element.');
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('secdsig', XMLSecurityDSig::XMLDSIGNS);
$query = "string(./secdsig:SignedInfo/secdsig:SignatureMethod/@Algorithm)";
$algorithm = $xpath->evaluate($query, $signatureElement);
if (!$algorithm) {
    throw new Exception('Could not locate Signature algorithm attribute.');
}

$objXMLSecDSig->canonicalizeSignedInfo();
if (!$objXMLSecDSig->validateReference()) {
    throw new Exception('XMLsec: digest validation failed');
}

$objKey = new XMLSecurityKey($algorithm, array('type'=>'public'));
$objKey->loadKey($cert, TRUE, TRUE);

$result = $objXMLSecDSig->verify($objKey);
if (($result !== 1)) {
    throw new Exception('Unable to validate Signature');
}

// signature is fine, now get rid of it...

$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/strip-signature.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$unsigned_response = $proc->transformToXML($dom);

session_start();
$requested_loa = $_SESSION['req_loa'];
error_log("requested LoA was $requested_loa");

// retrieve NameID

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('saml', "urn:oasis:names:tc:SAML:2.0:assertion");
$query = "string(//saml:Assertion/saml:Subject/saml:NameID/text())";
$nameID = $xpath->evaluate($query, $dom);
if (!$nameID) {
    throw new Exception('Could not locate nameID attribute.');
}
error_log("NameID is $nameID");

// save response for after step-up authentication
$_SESSION['response'] = $unsigned_response;
$_SESSION['nameID'] = $nameID;

if( "http://suaas.example.com/assurance/loa1" == $requested_loa) {
    $_SESSION['loa'] = 1;
    header('Location: response.php');
}
else
    header('Location: ' . 'stepup.php');
