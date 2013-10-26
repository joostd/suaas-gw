<?php

include_once('../../config.php');

session_start();

$userId = $_SESSION['nameID'];

echo "You do not have a token registered. Please register a token first.";
