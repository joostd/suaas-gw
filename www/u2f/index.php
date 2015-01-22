<?php

//include_once('../host.php');
include_once('../../config.php');

session_start();

$userId = $_SESSION['nameID'];

$handle = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);
$sth = $handle->prepare("SELECT authentication_method.owner_id FROM authentication_method, user WHERE user.id = authentication_method.owner_id and user.name_id=? and authentication_method.type = 'u2f'");
$sth->execute(array($userId));
$owner_id = $sth->fetchColumn();

#$_SESSION['owner_id'] = $owner_id;
error_log("owner ID for user $userId is $owner_id");

header('Location: ' . 'verify.php');
