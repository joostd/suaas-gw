<?php

//include_once('../host.php');

session_start();
// todo send sms only once

// TODO: check if not both otp and stepup are null

$otp = $_SESSION['otp'];

$stepup = array_key_exists('stepup', $_POST) ? $_POST['stepup'] : NULL;


if( $stepup == $otp ) {
    // OTP is correct, proceed
    $_SESSION['otp'] = NULL;
    $_SESSION['loa'] = 2;
    header('Location: ' . '/set-authncontext.php');
}


if( $stepup == NULL ) {
    $msg = "Please enter the one-time password sent to your phone";
} else {
    $msg = "Password is incorrect, please try again";
}

echo $msg;
?>
<form
        method="post">
            <input type="text" name="stepup" autofocus="autofocus"/>
            <input type="submit" value="Login"/>
</form>
