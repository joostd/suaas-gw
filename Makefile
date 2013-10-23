install: composer.phar cert.pem key.pem www/idp/cert.pem www/idp/key.pem
	./composer.phar install

composer.phar:
	curl -sS https://getcomposer.org/installer | php

cert.pem key.pem:
	openssl req -x509 -newkey rsa:2048 -keyout key.pem -out cert.pem -nodes -subj '/CN=SAML Gateway Signing Key'

www/idp/cert.pem www/idp/key.pem:
	openssl req -x509 -newkey rsa -keyout www/idp/key.pem -out www/idp/cert.pem -nodes -subj '/CN=SAML IDP Signing Key'

suaas-gw.tar:
	tar cf suaas-gw.tar www/idp www/sp xslt *.php composer.json README.md Makefile

clean:
	rm suaas-gw.tar

realclean:
	rm www/idp/key.pem www/idp/cert.pem key.pem cert.pem
