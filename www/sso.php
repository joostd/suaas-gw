<?php

//include_once('./host.php');
include('../config.php');

/*
 * Gateway SSO endpoint: bridge to IDP
 */

# gateway SP
$issuer = $config['entity_id'];
$acs_url = "$proto://$host/acs.php"; // TODO https

# remote IDP
$idp_entityID = array_keys( $config['idp'] )[0];   // no IDP discovery: take first one
$idp = $config['idp'][$idp_entityID];
$sso_url = $idp['sso'];

session_start();

/*
 * Process incoming request
 */

$request = $_GET['SAMLRequest'];
$xml = str_replace ("\r", "", gzinflate(base64_decode($request)));
$dom = new DOMDocument();
$dom->loadXML($xml);
if ($dom->getElementsByTagName('AuthnRequest')->length == 0) {
    throw new Exception('Expecting AuthnRequest using HTTP-Redirect binding');
}
$authnrequest = $dom->getElementsByTagName('AuthnRequest')->item(0);
$requestor = $dom->getElementsByTagName('Issuer')->item(0)->textContent;

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('samlp', "urn:oasis:names:tc:SAML:2.0:protocol");
$xpath->registerNamespace('saml', "urn:oasis:names:tc:SAML:2.0:assertion");
$query = "string(//samlp:RequestedAuthnContext/saml:AuthnContextClassRef)";
$requested_loa = $xpath->evaluate($query, $dom);
if (!$requested_loa) {
    throw new Exception('Could not determine requested LoA'); // todo - default to loa1
}
error_log("requested LoA is $requested_loa");
$_SESSION['req_loa'] = $requested_loa;

// TODO store requests decently such that multiple simultaneous requests are supported
$_SESSION['requestor'] = $requestor;
$sprequestid = $authnrequest->getAttribute('ID');
error_log("Incoming request from $requestor with ID $sprequestid");
$_SESSION['sprequestid'] = $sprequestid;

$relaystate = array_key_exists('RelayState', $_GET) ? $_GET['RelayState'] : NULL;
$_SESSION['RelayState'] = $relaystate;
error_log("saving RelayState in session ($relaystate)");

// simply copy the request, updating only issuer, ACS URL and Destination attributes
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/proxy-request.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$proc->setParameter('', 'acs', $acs_url);
$proc->setParameter('', 'destination', $sso_url);
$proc->setParameter('', 'issuer', $issuer);
$request = $proc->transformToXML($dom);
//error_log($request);

# use HTTP-Redirect binding
$query  = 'SAMLRequest=' . urlencode(base64_encode(gzdeflate($request)));
$location = "$sso_url?$query";

header('Location: ' . $location);
