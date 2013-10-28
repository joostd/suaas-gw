<?php

include('../config.php');
include('./saml.php');

session_start();

$response = $_SESSION['response'];

$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($response);
$dom->formatOutput = TRUE;

$response = utils_xml_sign($dom, $config['keyfile'], $config['certfile']);

$_SESSION['response'] = $response->saveXML();
header('Location: post.php');
