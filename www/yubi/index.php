<?php

//include_once('../host.php');
include_once('../../config.php');

session_start();

$userId = $_SESSION['nameID'];

$handle = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);
$sth = $handle->prepare("SELECT authentication_method.yubikey_id FROM authentication_method, user WHERE user.id = authentication_method.owner_id and user.name_id=? and authentication_method.type = 'yubi'");
$sth->execute(array($userId));
$yubikey_id = $sth->fetchColumn();

$_SESSION['yubikey_id'] = $yubikey_id;
error_log("Yubikey ID for user $userId is $yubikey_id");

header('Location: ' . 'verify.php');
