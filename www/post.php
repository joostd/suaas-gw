<?php

include('../config.php');

session_start();
$response = $_SESSION['response'];

$sp_entityID = $_SESSION['requestor']; // TODO support multiple simultaneous requests
error_log("Retrieved request from $sp_entityID");

$acs = $config['sp'][$sp_entityID]['acs'];
$destination = htmlspecialchars($acs);
error_log("Setting ACS location to $acs");


// forward response to SP using POST binding:

$params = array();
$params['SAMLResponse']	= base64_encode($response);

$relaystate = array_key_exists('RelayState', $_SESSION) ? $_SESSION['RelayState'] : NULL;
if ($relaystate !== NULL) {
    $params['RelayState'] = $relaystate;
    error_log("restored RelayState from session ($relaystate)");
}

?>
<body onload="document.forms[0].submit()">
<script type="text/javascript">
    document.write("Please wait while you are being redirected to the application (this may take up to 30 seconds)...");
</script>
<form
    method="post"
    action="<?php echo htmlspecialchars($destination)?>"
    >
    <?php foreach ($params as $key => $value) { ?>
        <input
            type="hidden"
            name="<?php echo htmlspecialchars($key)?>"
            value="<?php echo htmlspecialchars($value)?>"
            />
    <?php } ?>
    <input type="submit" value="Login" />
</form>
</body>
