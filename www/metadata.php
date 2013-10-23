<?php
include_once('./host.php');
include('./config.php');

$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/metadata';

// convention: use metadata URL as entity ID
$entityID = "$proto://$host$uri";
$base = dirname($uri);
$acs_location = "$proto://$host$base/acs.php";
$sso_location = "$proto://$host$base/sso.php";

$xml = <<<XML
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="$entityID">
  <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="$sso_location"/>
  </IDPSSODescriptor>
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <AssertionConsumerService index="0" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="$acs_location"/>
  </SPSSODescriptor>
</EntityDescriptor>
XML;
header("Content-type: application/xml");
echo $xml;