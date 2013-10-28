<?php

include('../config.php');

session_start();

$response = $_SESSION['response'];
$dom = new DOMDocument();
$dom->loadXML($response);

// TODO how long does a higher LoA last?
$loa = $_SESSION['loa'];

$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/set-authncontext.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$proc->setParameter('','loa',"http://suaas.example.com/assurance/loa$loa");
$response = $proc->transformToXML($dom);
$_SESSION['response'] = $response;
header('Location: response.php');
