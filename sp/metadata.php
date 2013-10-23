<?php

include("../host.php");

$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/metadata';

// convention: use metadata URL as entity ID
$entityID = "http://$host$uri";
$base = dirname($uri);
$acs_location = "http://$host$base/acs.php";

$xml = <<<XML
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="$entityID">
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <AssertionConsumerService index="0" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="$acs_location"/>
  </SPSSODescriptor>
</EntityDescriptor>
XML;
header("Content-type: application/xml");
echo $xml;
