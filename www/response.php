<?php

include('../config.php');

session_start();

$issuer = $config['entity_id'];

$sprequestid = $_SESSION['sprequestid'];
error_log("retrieving request with ID $sprequestid from session");

$sp_entityID = $_SESSION['requestor']; // TODO support multiple simultaneous requests
error_log("Retrieved request from $sp_entityID");

if( !array_key_exists($sp_entityID, $config['sp'])) {
    throw new Exception("Unknown SP: $sp_entityID");
}

$acs = $config['sp'][$sp_entityID]['acs'];
$destination = htmlspecialchars($acs);
error_log("Setting ACS location to $acs");

// TODO audience, etc.

/*
* For now, we completely ignore the request that corresponds to the response (if any)
* Proceed as if the response is unsolicited (i.e. IDP first)
*/

$response = $_SESSION['response'];
$dom = new DOMDocument();
$dom->loadXML($response);

// proxy response
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/proxy-response.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$proc->setParameter('', 'destination', $acs);
$proc->setParameter('', 'recipient', $acs);
$proc->setParameter('', 'audience', $sp_entityID);
$proc->setParameter('', 'issuer', $issuer);
$response = $proc->transformToXML($dom);

$_SESSION['response'] = $response;
header('Location: sign-assertion.php');
