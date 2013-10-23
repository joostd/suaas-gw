<?php
include_once(dirname(__FILE__) . '/host.php');

$config = array(
    'sp' => array(
        "http://$host/sp/metadata.php" => array(
            'acs' =>  "http://$host/sp/acs.php",
        )
    ),
    'idp' => array(
        "http://$host/idp/metadata.php" => array(
            'sso' =>  "http://$host/idp/sso.php",
            'certfile' => "verify.pem",
        )
    ),
    'entity_id' => "http://$host/metadata.php",
    'keyfile' => "key.pem",
    'certfile' => "cert.pem",
    'userstore' => array(
    	'dsn' => 'mysql:host=127.0.0.1;dbname=suaas',
    	'username' => 'suaas',
    	'password' => 'changeit',
    ),
);

// override config locally
if( file_exists(dirname(__FILE__) . "/local_config.php") ) {
    include(dirname(__FILE__) . "/local_config.php");
} else {
error_log("no local config found");
}
