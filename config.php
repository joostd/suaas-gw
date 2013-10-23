<?php
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8080';

$config = array(
    'sp' => array(
        "http://$host/sp/metadata.php" => array(
            'acs' =>  "http://$host/sp/acs.php",
        )
    ),
    'idp' => array(
        "http://$host/idp/metadata.php" => array(
            'sso' =>  "http://$host/idp/sso.php",
            'certfile' => dirname(__FILE__) . "/www/idp/cert.pem",
        )
    ),
    'entity_id' => "http://$host/metadata.php",
    'keyfile' => dirname(__FILE__) . "/key.pem",
    'certfile' => dirname(__FILE__) . "/cert.pem",
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
