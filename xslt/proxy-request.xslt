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

<!--
copy AuthnRequest element, apply transforms on attributes and add scoping element
NOTE: Scoping is the last child of an AuthnRequest element:
      AuthnRequest -> saml:Subject, samlp:NameIDPolicy, saml:Conditions, samlp:RequestedAuthnContext, samlp:Scoping
TODO: check if Scoping already exists, add RequesterID to the element if so
  -->

  <xsl:template match="samlp:AuthnRequest">
    <xsl:copy>
      <xsl:apply-templates select="@*"/>
      <xsl:apply-templates />
    <samlp:Scoping ProxyCount="10">
      <samlp:RequesterID><xsl:value-of select="saml:Issuer"/></samlp:RequesterID>
    </samlp:Scoping>
    </xsl:copy>
  </xsl:template>

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

  <xsl:template match="saml:Issuer/text()">
    <xsl:value-of select="$issuer"/>
  </xsl:template>

  <!-- ignore RequestedAuthnContext completely: we'll accept anything, and see if we need to step up based on what is returned -->
  <!-- TODO: leave RAC alone in case the IDP is capable itself of authenticating the subject with the desired LoA.  -->
  <xsl:template match="samlp:RequestedAuthnContext">
  </xsl:template>

</xsl:stylesheet>
