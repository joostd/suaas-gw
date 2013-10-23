<?php

include("../host.php");

// convention: use metadata URL as entity ID
$entityID = "http://$host" . $_SERVER['PHP_SELF'];
$sso_location = "http://$host/sso.php";

$xml = <<<XML
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="$entityID">
  <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="$sso_location"/>
  </IDPSSODescriptor>
</EntityDescriptor>
XML;
header("Content-type: application/xml");
echo $xml;
