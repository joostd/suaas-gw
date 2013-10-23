Minimal SAML 2.0 implementation
===============================

WARNING
-------

This is example code, useful for debugging and experimentation. DO NOT USE IN A PRODUCTION ENVIRONMENT. This code is insecure!

Limitations
-----------
- only WebSSO profile
- only HTTP-Redirect Binding for authentication requests
- only HTTP-POST Binding for authentication responses
- no signing/verification of authentication requests
- only signing of assertions (not responses)
- static user directory - no external user stores

Furthermore, there is hardly any error checking/handling.

requirements
------------

- php 5.4, if you'd like to use the built-in web server
- php-xsl, for rendering attributes (acs.php)
- php-mysql, when using mysql
- xmlseclibs, available from https://code.google.com/p/xmlseclibs/

install
-------

    make install

configure
---------

create a file local_config.php overriding the configuration in config.php

	<?php
	$config['sp']["https://localhost/module.php/saml/sp/metadata.php/default-sp"] = array(
		'acs' =>  "https://localhost/module.php/saml/sp/saml2-acs.php/default-sp",
	);

	$config['idp'] = array(
		'https://localhost/saml2/idp/metadata.php' => array(
	    	'sso' => 'https://localhost/saml2/idp/SSOService.php',
	    	'certfile' => "ssp.pem",
		),
	);

run
---

Using php 5.4, you can use the built-in web server:

	php -S localhost:8080 -t www

When accessing the SP, a request will be made to the IDP, e.g.

	$ curl -I http://localhost:8080/sp/
	HTTP/1.1 302 Found
	Host: localhost:8080
	Connection: close
	X-Powered-By: PHP/5.4.20
	Location: http://localhost:8080/idp/sso.php?SAMLRequest=...
	Content-type: text/html

TODO
----
