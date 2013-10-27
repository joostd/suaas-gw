<xsl:stylesheet version="1.0"
 xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
 xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>

  <xsl:param name="destination" select="'http://idp.com/'"/>
  <xsl:param name="acs" select="'http://idp.com/acs'"/>
  <xsl:param name="issuer" select="'http://gw.com'"/>

  <xsl:template match="samlp:AuthnRequest/@AssertionConsumerServiceURL">
    <xsl:attribute name="AssertionConsumerServiceURL">
      <xsl:value-of select="$acs"/>
    </xsl:attribute>
  </xsl:template>

  <xsl:template match="samlp:AuthnRequest/@Destination">
    <xsl:attribute name="Destination">
      <xsl:value-of select="$destination"/>
    </xsl:attribute>
  </xsl:template>

  <xsl:template match="saml:Issuer">
  <saml:Issuer><xsl:value-of select="$issuer"/></saml:Issuer>
  </xsl:template>

    <!-- ignore RequestedAuthnContext completely -->
  <xsl:template match="samlp:RequestedAuthnContext">
  </xsl:template>

    <!--
        <samlp:Scoping ProxyCount="10">
            <samlp:RequesterID>XXX</samlp:RequesterID>
        </samlp:Scoping>
    -->

</xsl:stylesheet>
