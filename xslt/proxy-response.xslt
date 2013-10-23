<xsl:stylesheet version="1.0"
 xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
 xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>

  <xsl:param name="destination" select="'http://sp.com/acs'"/>
  <xsl:param name="issuer" select="'http://gw.com'"/>
  <xsl:param name="audience" select="'http://sp.com'"/>
  <xsl:param name="recipient" select="'http://sp.com/acs'"/>

  <xsl:template match="samlp:Response/@Destination">
    <xsl:attribute name="Destination">
      <xsl:value-of select="$destination"/>
    </xsl:attribute>
  </xsl:template>

  <xsl:template match="saml:SubjectConfirmationData/@Recipient">
    <xsl:attribute name="Recipient">
      <xsl:value-of select="$recipient"/>
    </xsl:attribute>
  </xsl:template>

  <xsl:template match="saml:Audience">
  <saml:Audience><xsl:value-of select="$audience"/></saml:Audience>
  </xsl:template>

  <xsl:template match="saml:Issuer">
  <saml:Issuer><xsl:value-of select="$issuer"/></saml:Issuer>
  </xsl:template>

<!--
<saml:AuthenticatingAuthority>XXX</saml:AuthenticatingAuthority>
-->

</xsl:stylesheet>
