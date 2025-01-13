<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <!-- Template principal -->
    <xsl:template match="/">
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        text-align: center;
                    }
                    .meteo-container {
                        display: flex;
                        justify-content: center;
                        gap: 10px;
                        flex-wrap: wrap;
                    }
                    .meteo-item {
                        border: 1px solid #ccc;
                        border-radius: 10px;
                        padding: 10px;
                        width: 100px;
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                    }
                    .icon {
                        font-size: 32px;
                        margin-bottom: 10px;
                    }
                    .temp {
                        font-size: 20px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="meteo-container">
                    <!-- Boucle sur les 10 premières échéances -->
                    <xsl:for-each select="previsions/echeance[position() &lt;= 10]">
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
                                <!-- Température à 2m -->
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
