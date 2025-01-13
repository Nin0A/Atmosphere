// Initialiser la carte avec Leaflet
var map = L.map('map').setView([48.693722, 6.184417], 13); // Coordonnées pour centrer la carte sur Nancy

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
}).addTo(map);

var markers = [];

//icon 1
var traficIcon = L.icon({
    iconUrl: 'assets/images/marker1.svg',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

//icon 2
var searchIcon = L.icon({
    iconUrl: 'assets/images/marker2.svg',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Vérification de la structure des données d'incidents
if (window.traficData && Array.isArray(window.traficData) && window.traficData.length > 0) {
    window.traficData.forEach(function (incident) {
        try {
            console.log('Incident:', incident);

            if (incident.location && incident.location.polyline) {
                let coordinates = incident.location.polyline.split(' ');

                if (coordinates.length >= 2) {
                    let lat = parseFloat(coordinates[0]);
                    let lon = parseFloat(coordinates[1]);
                    let description = incident.description || 'Pas de description disponible';

                    if (!isNaN(lat) && !isNaN(lon)) {
                        // Ajouter un marqueur pour l'incident de trafic avec l'icône rouge
                        var marker = L.marker([lat, lon], { icon: traficIcon })
                            .addTo(map)
                            .bindPopup(description);

                        markers.push(marker);
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

// Gestion de la recherche de lieu et ajout de marqueur avec une icône différente
document.getElementById("search-form").addEventListener("submit", function (event) {
    event.preventDefault();

    var place = document.getElementById("place").value;
    var desc = document.getElementById("desc").value;


    var geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(place)}`;

    fetch(geocodeUrl)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                var location = data[0];
                var lat = parseFloat(location.lat);
                var lon = parseFloat(location.lon);

                var searchMarker = L.marker([lat, lon], { icon: searchIcon })
                    .addTo(map)
                    .bindPopup(desc || "Pas de description fournie");
                map.setView([lat, lon], 13);

                markers.push(searchMarker);
            } else {
                alert("Lieu non trouvé. Veuillez essayer un autre lieu.");
            }
        })
        .catch(error => {
            console.error("Erreur lors du géocodage:", error);
            alert("Erreur lors de la recherche du lieu.");
        });
});
