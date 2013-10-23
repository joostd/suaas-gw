install: composer.phar cert.pem key.pem idp/cert.pem idp/key.pem verify.pem sp/verify.pem
	./composer.phar install

cert.pem key.pem:
	openssl req -x509 -newkey rsa:2048 -keyout key.pem -out cert.pem -nodes -subj '/CN=SAML Gateway Signing Key'

sp/verify.pem: cert.pem
	cp cert.pem sp/verify.pem

idp/cert.pem idp/key.pem:
	openssl req -x509 -newkey rsa:2048 -keyout idp/key.pem -out idp/cert.pem -nodes -subj '/CN=SAML IDP Signing Key'

verify.pem: idp/cert.pem
	cp idp/cert.pem verify.pem

clean:
	rm suaas-gw.tar

realclean:
	rm idp/key.pem idp/cert.pem key.pem cert.pem verify.pem sp/verify.pem

composer.phar:
	curl -sS https://getcomposer.org/installer | php

suaas-gw.tar:
	tar cf suaas-gw.tar idp sp xslt *.php composer.json README.md Makefile
