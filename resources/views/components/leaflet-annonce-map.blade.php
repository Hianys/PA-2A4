@props(['annonce', 'segments'])

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

        const map = L.map('map').setView(
            (fromLat && fromLng) ? [fromLat, fromLng] : [46.6, 2.2],
            6
        );

        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        if (fromLat && toLat) {
            const mainRoute = L.polyline([
                [fromLat, fromLng],
                [toLat, toLng]
            ], { color: 'blue' }).addTo(map);

            map.fitBounds(mainRoute.getBounds());
        }

        @foreach ($segments as $segment)
        @if ($segment->from_lat && $segment->to_lat)
        L.polyline([
            [{{ $segment->from_lat }}, {{ $segment->from_lng }}],
            [{{ $segment->to_lat }}, {{ $segment->to_lng }}]
        ], {
            color: 'green',
            dashArray: '5, 5'
        }).addTo(map);
        @endif
        @endforeach
    });
</script>
