<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "../dtd/meteo.dtd">
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/">
        <html>
            <body>
                <div class="meteo-container">
                    <!-- Boucle sur les 10 premières échéances -->
                    <xsl:for-each select="previsions/echeance[position() &lt;= 7]">
                        <div class="meteo-item">
                            <div class="icon">
                                <xsl:choose>
                                    <xsl:when test="pluie > 0">🌧️</xsl:when>
                                    <xsl:when test="risque_neige = 'oui'">❄️</xsl:when>
                                    <xsl:otherwise>☀️</xsl:otherwise>
                                </xsl:choose>
                            </div>
                            <div class="time">
                                <xsl:value-of select="substring-before(substring-after(@timestamp, ' '), ':')" />h
                            </div>
                            <div class="temp">
                                <xsl:value-of select="format-number(temperature/level[@val='2m'] - 273.15, '0.0')"/>
                                <xsl:text>°C</xsl:text>
                            </div>
                        </div>
                    </xsl:for-each>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
