<?php
define('PROXY_URL', 'tcp://www-cache:3128');

//Géolocalisation
define('API_GEOLOCALISATION_KEY', '22d6ddda1b5d49d9a3ab3f4d33624e0a'); // pas propre du tout :)
define('API_GEOLOCALISATION_URL', 'https://api.ipgeolocation.io/ipgeo?');

//Météo
define('API_METEO_AUTH', 'ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2');
define('API_METEO_URL', 'https://www.infoclimat.fr/public-api/gfs/xml');

//Qualité de l'air
define('API_AIR_QUALITY_URL', 'https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson');

//Trafic
define('API_TRAFFIC_URL', 'https://carto.g-ny.org/data/cifs/cifs_waze_v2.json');


/**
 * Pour activer le proxy
 * false : actif
 * true : non actif
 */
define('IS_LOCAL', true);
