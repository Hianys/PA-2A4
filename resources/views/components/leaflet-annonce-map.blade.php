@props(['annonce', 'segments'])

{{-- CSS Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map {
        height: 400px !important;
    }
</style>

<div id="map" class="w-full rounded shadow my-6"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fromLat = {!! json_encode($annonce->from_lat) !!};
        const fromLng = {!! json_encode($annonce->from_lng) !!};
        const toLat = {!! json_encode($annonce->to_lat) !!};
        const toLng = {!! json_encode($annonce->to_lng) !!};

        console.log("Coordonnées départ :", fromLat, fromLng);
        console.log("Coordonnées arrivée :", toLat, toLng);

        const map = L.map('map').setView(
            (fromLat && fromLng) ? [fromLat, fromLng] : [46.6, 2.2],
            6
        );

        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        if (fromLat && fromLng) {
            L.marker([fromLat, fromLng])
                .addTo(map)
                .bindPopup("Départ : {{ $annonce->from_city }}");
        }

        if (toLat && toLng) {
            L.marker([toLat, toLng])
                .addTo(map)
                .bindPopup("Arrivée : {{ $annonce->to_city }}");
        }

        if (fromLat && fromLng && toLat && toLng) {
            const url = `/api/ors/route?from_lat=${fromLat}&from_lng=${fromLng}&to_lat=${toLat}&to_lng=${toLng}`;
            console.log("Appel API ORS via Laravel :", url);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log("Réponse ORS :", data);
                    const coords = data.features[0].geometry.coordinates;

                    const latlngs = coords.map(c => [c[1], c[0]]);

                    L.polyline(latlngs, {
                        color: 'blue',
                        weight: 4
                    }).addTo(map);

                    map.fitBounds(latlngs);
                })
                .catch(err => {
                    console.error(err);
                    alert("Impossible de récupérer le trajet routier.");
                });
        }
    });
</script>

