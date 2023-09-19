<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:opml="http://www.w3.org/2005/Atom"
>
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
        <title>OPML Feed • <xsl:value-of select="opml/head/title"/></title>
        <style type="text/css">
          body{max-width:768px;margin:0 auto;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:16px;line-height:1.5em}section{margin:30px 15px}h1{font-size:2em;margin:.67em 0;line-height:1.125em}h2{border-bottom:1px solid #eaecef;padding-bottom:.3em}.alert{background:#fff5b1;padding:4px 12px;margin:0 -12px}a{text-decoration:none}.entry h3{margin-bottom:0}.entry p{margin:4px 0}
        </style>
      </head>
      <body>
        <section>
          <div class="alert">
            <p><strong>This is an OPML containing feed subscriptions</strong>. <strong>Subscribe to each on of them</strong> by copying the URL from the address bar into your newsreader app.</p>
          </div>
        </section>
        <section>
          <h2>Feed subscriptions</h2>
          <xsl:apply-templates select="//outline"/>
        </section>
      </body>
    </html>
  </xsl:template>

  <xsl:template match="outline">
    <div class="entry">
      <h3>
        <a>
          <xsl:attribute name="href">
            <xsl:value-of select="@xmlUrl"/>
          </xsl:attribute>
          <xsl:value-of select="@text"/>
        </a>
      </h3>
      <small>
        <a>
            <xsl:attribute name="href">
              <xsl:value-of select="@htmlUrl"/>
            </xsl:attribute>
            visit website
          </a>
      </small>
    </div>
  </xsl:template>

</xsl:stylesheet>