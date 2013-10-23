<xsl:stylesheet version="1.0"
 xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>

  <xsl:template match="saml:AuthnContextClassRef">
    <saml:AuthnContextClassRef>http://suaas.example.com/assurance/loa2</saml:AuthnContextClassRef>
  </xsl:template>

</xsl:stylesheet>
