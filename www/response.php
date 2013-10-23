<?php

//include_once('./host.php');
include('../config.php');
include('./saml.php');

session_start();

$issuer = $config['entity_id'];

$sprequestid = $_SESSION['sprequestid'];
error_log("retrieving request with ID $sprequestid from session");

// TODO properly determine the intended SP
//$sp_entityID = array_keys( $config['sp'] )[0];   // for now: take first one
$sp_entityID = $_SESSION['requestor']; // TODO support multiple simultaneous requests
error_log("Retrieved request from $sp_entityID");

$acs = $config['sp'][$sp_entityID]['acs'];
$destination = htmlspecialchars($acs);

error_log("Setting ACS location to $acs");

// TODO audience, etc.


/*
* For now, we completely ignore the request that corresponds to the response (if any)
* Proceed as if the response is unsolicited (i.e. IDP first)
*/

$response = $_SESSION['response'];
$dom = new DOMDocument();
$dom->loadXML($response);

// proxy response
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/proxy-response.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$proc->setParameter('', 'destination', $acs);
$proc->setParameter('', 'recipient', $acs);
$proc->setParameter('', 'audience', $sp_entityID);
$proc->setParameter('', 'issuer', $issuer);
$dom = $proc->transformToDoc($dom);


// TODO how long does a higher LoA last?
$loa = $_SESSION['loa'];

// TODO check loa
$xslDoc = new DOMDocument();
$xslDoc->load("../xslt/set-authncontext.xslt");
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$response = $proc->transformToXML($dom);

### response

// TODO issuer entity ID etc.
$xml = $response;

$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($xml);
$dom->formatOutput = TRUE;

$response = $dom;
$response = utils_xml_sign($response, $config['keyfile'], $config['certfile']);

$params = array();
$params['SAMLResponse']	= base64_encode($response->saveXML());

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
