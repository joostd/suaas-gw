<xsl:stylesheet version="1.0"
 xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>

  <xsl:param name="loa" select="'urn:oasis:names:tc:SAML:2.0:ac:classes:Password '"/>

  <xsl:template match="saml:AuthnContextClassRef">
    <saml:AuthnContextClassRef><xsl:value-of select="$loa"/></saml:AuthnContextClassRef>
  </xsl:template>

</xsl:stylesheet>
