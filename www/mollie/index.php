<?php

//include_once('../host.php');
include_once('../../config.php');

function generateOTP() {
    $alphabet = '123456789bcdfghjkmnpqrstvwxyz';
    $length = strlen($alphabet) - 1;
    $otp = '';

    do {
        $otp .= $alphabet[mt_rand(0, $length)];
    } while (strlen($otp) < 6);

    return $otp;
}

function sms_send_mollie($recipient, $msg) {
    global $config;
    $req  = '?username=' . urlencode($config['mollie']['username']);
    $req .= '&password=' . urlencode($config['mollie']['password']);
    $req .= '&originator=' . urlencode("SURFnet");
    $req .= '&gateway=' . urlencode("1");
    $req .= '&recipients=' . urlencode($recipient);
    $req .= '&reference=' . urlencode(time());      # used for reporting, see mollie admin interface for URL reported to
    $req .= '&message=' . urlencode("OTP: " . $msg);
    $url = "https://secure.mollie.nl/xml/sms/" . $req;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    if ($result !== FALSE) {
        if ( ($error != '') or ($info['http_code'] != 200) ) $result = FALSE;
    }
    curl_close($ch);
    $data = simplexml_load_string($result);
    if ($data->item->resultcode != 10) {
        throw new Exception('Sending SMS failed: ' . $data->item->resultmessage);
    }
    return $result;

}

session_start();

$userId = $_SESSION['nameID'];

$handle = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);
$sth = $handle->prepare("SELECT authentication_method.phone_number FROM authentication_method, user WHERE user.id = authentication_method.owner_id and user.name_id=? and authentication_method.type = 'mollie'");
$sth->execute(array($userId));
$phone_number = $sth->fetchColumn();
error_log("retrieved phone number ($phone_number) for user $userId");

// todo send sms only once
$otp = generateOTP();
$result = sms_send_mollie($phone_number, $otp);
if( $result == FALSE ) {
    error_log("failed to send otp to $phone_number");
}
$_SESSION['otp'] = $otp;

header('Location: ' . 'verify.php');
