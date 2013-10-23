<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
	xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
	version='1.0'
>

<xsl:output method="html"/>

<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
   <xsl:apply-templates/>
<a href="/sp/">restart</a>
</html>
</xsl:template>

<!-- -->
<xsl:template match="saml:NameID" >
  <b><xsl:text>Name ID:</xsl:text></b>
  <xsl:value-of select="."/><br/>
</xsl:template>

<!-- -->
<xsl:template match="saml:Audience" >
</xsl:template>

<!--
-->
<xsl:template match="saml:AuthnContext" >
  <xsl:text>LoA: </xsl:text>
  <xsl:value-of select="saml:AuthnContextClassRef"/>
</xsl:template>

<!-- -->
<xsl:template match="saml:AttributeStatement">
 <table border="1">
   <xsl:apply-templates/>
 </table>
</xsl:template>

<!-- -->
<xsl:template match="saml:Attribute" >
 <tr>
  <td><xsl:value-of select="@Name"/></td>
  <td><xsl:apply-templates/></td>
 </tr>
</xsl:template>

<!-- -->
<xsl:template match="saml:AttributeValue" >
  <xsl:value-of select="."/><br/>
</xsl:template>

<!-- -->
<xsl:template match="saml:Issuer" >
</xsl:template>

<xsl:template match="ds:Signature"/>

</xsl:stylesheet>

