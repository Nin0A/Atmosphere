<?php

include('src/config/config.php');
include('src/config/utils.php');


// #########################################################################################################
// Section Géolocalisation
// #########################################################################################################

$ip = IS_LOCAL ?  '89.158.149.134' : getIpAddress(); // Obtenir l'adresse IP du client

$apiUrl = API_GEOLOCALISATION_URL . "apiKey=" . API_GEOLOCALISATION_KEY . "&ip={$ip}"; // URL de l'API

// Utiliser cURL pour récupérer les données
if (!IS_LOCAL) {
    $loc = safe_curl_get($apiUrl);
} else {
    $loc = file_get_contents($apiUrl);
}

$latitude = null;
$longitude = null;

if ($loc) {
    $data = json_decode($loc); // Décoder les données JSON

    if (isset($data->latitude) && isset($data->longitude)) {
        $latitude = $data->latitude;
        $longitude = $data->longitude;
    } else {
        echo "Données géographiques manquantes.<br>";
    }
} else {
    echo "Erreur lors de la récupération des données.<br>";
}


// #########################################################################################################
// Section Météo
// #########################################################################################################

$meteoData = safe_file_get_contents(API_METEO_URL . '?_ll=' . $latitude . ',' . $longitude . '&_auth=' . API_METEO_AUTH);

$meteoHtml = "";
if ($meteoData) {
    $xmlMeteo = new SimpleXMLElement($meteoData);

    $xsl = new DOMDocument();
    $xsl->load('xsl/style_meteo.xsl');
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    $meteoHtml = $proc->transformToXML($xmlMeteo);
} else {
    echo '<p>Les données météo ne sont pas disponibles pour le moment.</p>';
}

// #########################################################################################################
// Section trafic routier
// #########################################################################################################

$traficData = safe_file_get_contents(API_TRAFFIC_URL);
if ($traficData) {
    $traficJson = json_decode($traficData, true); // Décodage JSON en tableau associatif

    // Vérifiez si la clé 'incidents' existe dans la réponse JSON
    $traficFeatures = isset($traficJson['incidents']) ? $traficJson['incidents'] : [];


    // Vous pouvez ensuite travailler avec $traficIncidents comme bon vous semble
    if (!empty($traficIncidents)) {
        $firstIncident = $traficIncidents[0];
        echo "Type de l'incident: " . $firstIncident['type'] . "\n";
        echo "Description: " . $firstIncident['description'] . "\n";
        echo "Localisation: " . $firstIncident['location']['location_description'] . "\n";
    }
} else {
    echo "Erreur lors de la récupération des données de trafic.";
}


// #########################################################################################################
// Section qualité de l'air
// #########################################################################################################

// Qualité de l'air
$airData = safe_file_get_contents(API_AIR_QUALITY_URL);

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
                $airHtml = "<div class='section-2-infos'>
                    Zone : $zone<br>
                    Qualité : $qualite (Code : $codeQual)<br>
                    Date : $dateEch<br>
                    <div class='circle' style='background:$color_qualite;
                    border-radius:50%;
                    width:160px;
                    height:160px;'></div>";
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


<!-- ######################################################################################################### -->
<!-- Section HTML -->
<!-- ######################################################################################################### -->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Atmos'fair</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

</head>

<body>
    <h1>Atmos'fair</h1>
    <div class="section-container">
        <section id="section-1">
            <h2>Météo près de chez vous</h2>
            <?= $meteoHtml ?>
        </section>

        <section id="section-2">
            <h2>Carte du Trafic : Problèmes de circulation dans le Grand Nancy</h2>
            <div id="map" style="height: 400px;"></div>
        </section>

        <section id="section-3">
            <h2>Qualité de l'air</h2>
            <div class="air-container">

                <?= $airHtml ?>

                <div class="air-legend">

                    <ul>
                        <li>
                            <div class='circle-legend' id="circle-legend-1"></div>
                            <p>Bon</p>
                        </li>
                        <li>
                            <div class='circle-legend' id="circle-legend-2"></div>
                            <p>Moyen</p>
                        </li>
                        <li>
                            <div class='circle-legend' id="circle-legend-3"></div>
                            <p>Dégradé</p>
                        </li>
                        <li>
                            <div class='circle-legend' id="circle-legend-4"></div>
                            <p>Mauvais</p>
                        </li>
                        <li>
                            <div class='circle-legend' id="circle-legend-5"></div>
                            <p>Très mauvais</p>
                        </li>
                        <li>
                            <div class='circle-legend' id="circle-legend-6"></div>
                            <p>Extrêmement mauvais</p>
                        </li>
                    </ul>


                </div>

            </div>
        </section>

        <section id="search-section">
            <h2>Ajouter un lieu</h2>
            <form id="search-form" action="" method="POST">
                <label for="place">Entrez un lieu :</label>
                <input type="text" id="place" name="place" placeholder="Ex: Ville, Lieu, Adresse, etc." required />
                <label for="place">Entrez une description :</label>
                <input type="text" id="desc" name="desc" placeholder="Ex: Maison, Travail, etc." required />
                <button type="submit">Ajouter</button>
            </form>
        </section>

        <section id="info-section">
            <h2>Légende</h2>
            <div class="items-container">
                <div class="legend-item">
                    <img src="assets/images/marker1.svg" alt="Problème de circulation" class="legend-icon">
                    <span>Problème de circulation</span>
                </div>
                <div class="legend-item">
                    <img src="assets/images/marker2.svg" alt="Lieu ajouté" class="legend-icon">
                    <span>Lieu ajouté</span>
                </div>
            </div>
        </section>

        <section id="section-api-url">
            <h2>APIs utilisées</h2>

            <ul class="api-list">
                <li>
                    <a href="https://carto.g-ny.org/data/cifs/cifs_waze_v2.json" target="_blank" rel="noopener noreferrer">
                        API Trafic
                    </a>
                </li>
                <li>
                    <a href="https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson"
                        target="_blank" rel="noopener noreferrer">
                        API Qualité de l'air
                    </a>
                </li>
                <li>
                    <a href="https://www.infoclimat.fr/public-api/gfs/xml?_ll=48,6&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2" target="_blank" rel="noopener noreferrer">
                        API Météo
                    </a>
                </li>
                <li>
                    <a href="https://api.ipgeolocation.io/ipgeo?apiKey=22d6ddda1b5d49d9a3ab3f4d33624e0a&ip=8.8.8.8" target="_blank" rel="noopener noreferrer">

                        API Géolocalisation
                    </a>
                </li>
            </ul>
        </section>


    </div>
</body>

</html>

<script>
    window.traficData = <?php echo json_encode($traficFeatures, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
</script>

<script src="src/js/leaflet.js"></script>