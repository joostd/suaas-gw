<?php

include_once('../config.php');

$loas = array(
    'mollie' => 2,
    'yubi' => 3,
);

// todo validate
session_start();
$req_loa = $_SESSION['req_loa'];
unset( $_SESSION['req_loa'] );

$userId = $_SESSION['nameID'];
error_log("looking up $userId");

$handle = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);

$sth = $handle->prepare("SELECT authentication_method.type FROM authentication_method, user WHERE user.id = authentication_method.owner_id and user.name_id= ?");
$sth->execute(array($userId));
$type = $sth->fetchColumn();
// TODO; check
error_log("selected authentication method '$type' for user $userId");

if( !isset($type) || !array_key_exists($type, $loas) ) {
	$type = 'none';
} else {
    $loa = $loas[$type];
    if( "http://suaas.example.com/assurance/loa$loa" < $req_loa ) { // rely on lexicographical ordering here
        error_log("loa insufficient: requested $req_loa (selected token has LoA-$loa)");
        header("Location: noauthncontext.php");
        exit(0);
    }
}

$location = "$type/";
header("Location: $location");
