<?php
include('../vendor/xmlseclibs/xmlseclibs.php');

$cert = 'verify.pem';
if (!file_exists($cert)) {
  throw new Exception('Could not find verification certificate file: ' . $cert);
}

$response = base64_decode($_POST['SAMLResponse']);

if( array_key_exists('debug',$_GET) ) {
	header("Content-type: text/xml");
	echo $response;
}

$dom = new DOMDocument();
$dom->loadXML($response);

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

# show attributes
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/saml20.xslt");
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($response);
#$xmlDoc->load('test/response.xml');
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
echo $proc->transformToXML($xmlDoc);
?>
