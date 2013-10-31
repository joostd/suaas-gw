<?php
include('../config.php');
include_once(dirname(dirname(__FILE__)) . '/vendor/xmlseclibs/xmlseclibs.php');

$entityID = $config['entity_id'];
$certfile = $config['certfile'];

$cert = '';
if( file_exists($certfile)) {
    $cert = file_get_contents($certfile);
}
$cert = XMLSecurityDSig::staticGet509XCerts($cert);
$keydescriptor = '';
if( isset($cert[0]) ) $keydescriptor = <<<XML
    <KeyDescriptor xmlns:ds="http://www.w3.org/2000/09/xmldsig#" use="signing">
      <ds:KeyInfo>
        <ds:X509Data>
          <ds:X509Certificate>$cert[0]</ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </KeyDescriptor>
XML;

$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/metadata';
$base = dirname($uri);
$acs_location = "$proto://$host$base/acs.php";
$sso_location = "$proto://$host$base/sso.php";

$xml = <<<XML
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="$entityID">
  <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    $keydescriptor
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="$sso_location"/>
  </IDPSSODescriptor>
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <AssertionConsumerService index="0" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="$acs_location"/>
  </SPSSODescriptor>
</EntityDescriptor>
XML;
header("Content-type: application/xml");
echo $xml;