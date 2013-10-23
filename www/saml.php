<?php

include_once(dirname(dirname(__FILE__)) . '/vendor/xmlseclibs/xmlseclibs.php');

function utils_xml_create($xml, $preserveWhiteSpace = FALSE) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->loadXML($xml);
        $dom->formatOutput = TRUE;
        return $dom;
}

function utils_xml_sign($dom, $keyfile, $certfile) {
        // remove whitespace without breaking signature
        $dom = utils_xml_create($dom->saveXML(), TRUE);
        $dsig = new XMLSecurityDSig();
        $dsig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $root = $dom->getElementsByTagName('Assertion')->item(0);
        assert('$root instanceof DOMElement');
        $insert_into = $dom->getElementsByTagName('Assertion')->item(0);
        $insert_before = $insert_into->getElementsByTagName('Subject')->item(0);
        $dsig->addReferenceList(array($root), XMLSecurityDSig::SHA1,
                        array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N),
                        array('id_name' => 'ID'));
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
        $objKey->loadKey($keyfile, TRUE);
        $dsig->sign($objKey);
        $cert = $certfile;
        $contents = file_get_contents($cert);
        $dsig->add509Cert($contents, TRUE);
        $dsig->insertSignature($insert_into, $insert_before);
        return $dom;
}
