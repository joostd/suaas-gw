<?php

include("../../config.php");

$loa = NULL;
if( isset( $_GET['loa'] ) && preg_match('/^[1-4]$/', $_GET['loa']) ) {
  $loa = $_GET['loa'];
}

if( $loa == NULL ) {
    ?>
    <form>
        <select name='loa' onchange='this.form.submit()'>
            <option value=''>Select the desired LoA</option>
            <option value='1'>LoA 1</option>
            <option value='2'>LoA 2</option>
            <option value='3'>LoA 3</option>
            <option value='4'>LoA 4</option>
        </select>
    </form>

    <?php
    exit(1);
}

$loa = "http://suaas.example.com/assurance/loa$loa";
error_log("requested LoA: $loa");

# local SP
$issuer = "$proto://$host/sp/metadata.php";
$acs_url = "$proto://$host/sp/acs.php";

# remote IDP
$sso_url = "$proto://$host/sso.php";

$now = gmdate("Y-m-d\TH:i:s\Z", time());
$id = "_"; for ($i = 0; $i < 42; $i++ ) $id .= dechex( rand(0,15) );

$request = <<<XML
<samlp:AuthnRequest
  xmlns:samlp='urn:oasis:names:tc:SAML:2.0:protocol'
  xmlns:saml='urn:oasis:names:tc:SAML:2.0:assertion'
  ID='$id'
  Version='2.0'
  IssueInstant='$now'
  Destination='$sso_url'
  AssertionConsumerServiceURL='$acs_url'
  ProtocolBinding='urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
>
  <saml:Issuer>$issuer</saml:Issuer>
  <samlp:RequestedAuthnContext>
    <saml:AuthnContextClassRef>$loa</saml:AuthnContextClassRef>
  </samlp:RequestedAuthnContext>
</samlp:AuthnRequest>
XML;

# use HTTP-Redirect binding
$query  = 'SAMLRequest=' . urlencode(base64_encode(gzdeflate($request)));
$location = "$sso_url?$query";

header('Location: ' . $location);
