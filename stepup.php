<?php

include_once('./host.php');
include_once('./config.php');

// todo validate
session_start();
$userId = $_SESSION['nameID'];
error_log("looking up $userId");

$handle = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);

$sth = $handle->prepare("SELECT authentication_method.type FROM authentication_method, user WHERE user.id = authentication_method.owner_id and user.name_id= ?");
$sth->execute(array($userId));
$type = $sth->fetchColumn();
// TODO; check
error_log("selected authentication method '$type' for user $userId");

$location = "$type/";

header("Location: $location");