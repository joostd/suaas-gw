<?php

session_start();

$response = $_SESSION['response'];
$dom = new DOMDocument();
$dom->loadXML($response);
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/noauthncontext.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$response = $proc->transformToXML($dom);
$_SESSION['response'] = $response;

header("Location: sign-response.php");
