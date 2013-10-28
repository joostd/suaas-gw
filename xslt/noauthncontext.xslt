<xsl:stylesheet version="1.0"
 xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
 xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>

  <!-- strip assertion: abort authN transaction -->
  <xsl:template match="saml:Assertion">
  </xsl:template>

  <xsl:template match="samlp:Status">
  <samlp:Status>
    <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Responder">
      <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:NoAuthnContext"/>
    </samlp:StatusCode>
  </samlp:Status>
  </xsl:template>

</xsl:stylesheet>
