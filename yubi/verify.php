<?php

include_once('../host.php');

function verify($otp) {
    $nonce = ""; for ($i = 0; $i < 40; $i++ ) $nonce .= dechex( rand(0,15) );

    $req  = '?id=1';
    $req .= '&nonce=' . $nonce;
    $req .= '&otp=' . $otp;
    $url = "https://api2.yubico.com/wsapi/2.0/verify" . $req;
    // NOTE: no need to supply client ID or check signature when using HTTPS

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
    return $result;
}

function isValid($yubikey_id, $otp) {

    $result = verify($otp);
    if( $result === FALSE ) {
        return false;
    }

    $lines = explode("\r\n", $result);
    foreach ($lines as $line) {
        $position = strpos($line, '=');
        list($key, $value) = explode('#', substr_replace($line, '#', $position, 1));
        switch( $key ) {
            case 'status':
                switch( $value ) {
                    case 'OK':
                        return true;
                        break;
                    case 'REPLAYED_REQUEST':
                    case 'REPLAYED_OTP':
                    case 'BAD_OTP':
                    default:
                        return false;
                }
                break;
            default:
                break;
        }
    }
    return false;
}

// check stepup credentials

session_start();

$yubikey_id = $_SESSION['yubikey_id'];

$stepup = array_key_exists('stepup', $_POST) ? $_POST['stepup'] : NULL;
$prefix = substr($stepup, 0, -32);
$length = strlen($stepup);

$msg = "";
if( $stepup == NULL ) {
    $msg = "Please enter a one-time password using your Yubikey";
} elseif( $prefix != $yubikey_id ) {
    $msg = "Yubikey ID mismatch";
} elseif (!is_string($stepup)) {
    $msg = "otp must be a string value";
} elseif (!$length >= 32 && $length <= 48) {
    $msg = "otp length should be between 32 and 48 characters";
} elseif (!preg_match('~^[cbdefghijklnrtuv]{32,48}$~i', $stepup)) {
    $msg = "illegal character in otp string";
} elseif( isValid($yubikey_id, $stepup) ) {	// TODO
    // OTP is correct, proceed
    $_SESSION['yubikey_id'] = NULL;
    $_SESSION['loa'] = 2;
    header('Location: ' . '/response.php');
} else {
    $msg = "Invalid otp";
}
echo $msg;
?>
<form
        method="post">
            <input type="text" name="stepup" autofocus="autofocus"/>
            <input type="submit" value="Login"/>
</form>
