<xsl:stylesheet version="1.0"
 xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
  <xsl:import href="copy.xslt"/>
  <xsl:output omit-xml-declaration="yes"/>
  <xsl:template match="ds:Signature"/>
</xsl:stylesheet>
