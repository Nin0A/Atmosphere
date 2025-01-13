<?php
define('API_TRAFFIC_URL', 'https://carto.g-ny.org/data/cifs/cifs_waze_v2.json');
define('API_AIR_QUALITY_URL', 'https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token=');



function getIpAddress()
{
    // IPv6
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // IPv4
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // Si aucune adresse n'est disponible
    return 'Aucune adresse IP disponible';
}


function safe_file_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Cette option peut aussi être supprimée si vous souhaitez la réactiver.

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);
    return $result !== false ? $result : null;
}



// Adresse IP à géolocaliser

$ip = "89.158.149.134";

$apiKey = '22d6ddda1b5d49d9a3ab3f4d33624e0a'; //key

// $apiUrl = "http://api.ipapi.com/api/{$ip}?access_key={$apiKey}";

$apiUrl = "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}";

$loc = file_get_contents($apiUrl);

$latitude = null;

$longitude = null;

if ($loc) {
    $data = json_decode($loc);

    $latitude = $data->latitude;
    $longitude = $data->longitude;
    // Vérification si les données existent
    if (isset($data->city) && isset($data->country_name)) {
        echo "Ville: " . $data->city . "<br>";
        echo "Pays: " . $data->country_name . "<br>";
        echo "Latitude: " . $data->latitude . "<br>";
        echo "Longitude: " . $data->longitude . "<br>";
    } else {
        echo "Impossible de récupérer les données de géolocalisation.";
    }
} else {
    echo "Erreur lors de la récupération des données.";
}


// Météo
$meteoData = safe_file_get_contents("https://www.infoclimat.fr/public-api/gfs/xml?_ll=" . $latitude . "," . $longitude . "&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2");

if ($meteoData) {
    $xmlMeteo = new SimpleXMLElement($meteoData);

    $xsl = new DOMDocument();
    $xsl->load('style_meteo.xsl');
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    $meteoHtml = $proc->transformToXML($xmlMeteo);
} else {
    echo '<p>Les données météo ne sont pas disponibles pour le moment.</p>';
}












$traficData = safe_file_get_contents(API_TRAFFIC_URL);
if ($traficData) {
    $traficJson = json_decode($traficData, true); // Décodage JSON en tableau associatif

    // Vérifiez si la clé 'incidents' existe dans la réponse JSON
    $traficFeatures = isset($traficJson['incidents']) ? $traficJson['incidents'] : [];


    // Vous pouvez ensuite travailler avec $traficIncidents comme bon vous semble
    // Par exemple, pour afficher le premier incident :
    if (!empty($traficIncidents)) {
        $firstIncident = $traficIncidents[0];
        echo "Type de l'incident: " . $firstIncident['type'] . "\n";
        echo "Description: " . $firstIncident['description'] . "\n";
        echo "Localisation: " . $firstIncident['location']['location_description'] . "\n";
    }
} else {
    echo "Erreur lors de la récupération des données de trafic.";
}




// Qualité de l'air
$airData = safe_file_get_contents("https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson");

if ($airData) {
    // Décoder les données JSON
    $jsonAirData = json_decode($airData, true);

    if ($jsonAirData) {
        $uniqueZones = []; // Stockage pour les zones uniques
        foreach ($jsonAirData['features'] as $feature) {
            $attributes = $feature['attributes'];

            $zone = $attributes['lib_zone'] ?? 'Zone inconnue';

            // Si la zone n'a pas encore été ajoutée, l'ajouter
            if (!isset($uniqueZones[$zone])) {
                $qualite = $attributes['lib_qual'] ?? 'Qualité inconnue';
                $color_qualite = $attributes['coul_qual'];
                $codeQual = $attributes['code_qual'] ?? 'Code inconnu';
                $dateEch = !empty($attributes['date_ech']) ? date('Y-m-d', $attributes['date_ech'] / 1000) : 'Date inconnue';

                // Ajouter à la liste des zones uniques
                $uniqueZones[$zone] = true;

                // Construire l'élément HTML
                $airHtml = "<div style='background-color:$color_qualite'>
                    Zone : $zone<br>
                    Qualité : $qualite (Code : $codeQual)<br>
                    Date : $dateEch<br>";
            }
        }
        $airHtml .= "</div>";
    } else {
        echo "Données sur la qualité de l'air non disponibles.";
    }
} else {
    echo '<p>Les données sur la qualité de l\'air ne sont pas disponibles pour le moment.</p>';
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Atmos'fair</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

</head>

<body>
    <h1>Informations Atmosphériques</h1>

    <section>
        <h2>Météo</h2>
        <?= $meteoHtml ?>
    </section>

    <section>
        <h2>Carte du Trafic : Problèmes de circulation dans le Grand Nancy</h2>
        <div id="map" style="height: 400px;"></div>
    </section>

    <section>
        <h2>Qualité de l'air</h2>
        <?= $airHtml ?>
    </section>
</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
<script>
    proj4.defs("EPSG:2154", "+proj=lcc +lat_0=43 +lon_0=3 +k_0=1 +x_0=700000 +y_0=6600000 +datum=WGS84 +units=m +no_defs");

    var wgs84 = new proj4.Proj('EPSG:4326');
    var epsg2154 = new proj4.Proj('EPSG:2154');

    var data = [{
        "geometry": {
            "x": 525098.2712151778, // coordonnée projetée X (en mètres)
            "y": 6407403.6550085 // coordonnée projetée Y (en mètres)
        }
    }];

    data.forEach(function(point) {
        var projectedCoordinates = proj4(epsg2154, wgs84, [point.geometry.x, point.geometry.y]);
        var latitude = projectedCoordinates[1];
        var longitude = projectedCoordinates[0];

        console.log('Latitude:', latitude, 'Longitude:', longitude);
    });
</script>

<script>
    window.traficData = <?php echo json_encode($traficFeatures, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
</script>



<script>
    console.log('Incidents de trafic:', window.traficData);

    // Initialiser la carte avec Leaflet
    var map = L.map('map').setView([48.693722, 6.184417], 13); // Coordonnées pour centrer la carte sur Nancy

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Vérification de la structure des données d'incidents
    if (window.traficData && Array.isArray(window.traficData) && window.traficData.length > 0) {
        window.traficData.forEach(function(incident) {
            try {
                // Affichage pour vérifier la structure des données
                console.log('Incident:', incident);

                // Vérifier si 'incident.location.polyline' existe
                if (incident.location && incident.location.polyline) {
                    let coordinates = incident.location.polyline.split(' '); // Diviser la polyline en coordonnées

                    if (coordinates.length >= 2) {
                        let lat = parseFloat(coordinates[0]); // Latitude
                        let lon = parseFloat(coordinates[1]); // Longitude
                        let description = incident.description || 'Pas de description disponible';

                        // Vérifier la validité des coordonnées
                        if (!isNaN(lat) && !isNaN(lon)) {
                            // Ajouter un marqueur à la carte
                            L.marker([lat, lon])
                                .addTo(map)
                                .bindPopup(description); // Afficher la description dans le popup
                        } else {
                            console.warn('Coordonnées invalides pour un incident de trafic:', incident.location.polyline);
                        }
                    } else {
                        console.warn('Les coordonnées polyline sont mal formatées:', incident.location.polyline);
                    }
                } else {
                    console.warn('Aucune donnée polyline pour un incident de trafic:', incident);
                }
            } catch (error) {
                console.error('Erreur lors du traitement d’un incident de trafic :', error, incident);
            }
        });
    } else {
        console.warn('Aucun incident valide reçu.');
    }
</script>