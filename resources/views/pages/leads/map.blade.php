@extends('layout.admin')

@section('head')
<title>Lead Location Map</title>
<style>
    .map-container {
        height: calc(100vh - 100px);
        min-height: 500px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }

    .header-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}),
            r = new Set,
            e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
    })({
        key: "{{ config('services.google_maps.key') }}",
        v: "beta"
    });
</script>
@endsection

@section('content')
<div class="container-fluid p-0 mt-3">
    {{-- Header overlay or top bar --}}
    <div class="header-card px-4 py-3 d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-map-fill me-2 text-primary"></i>Lead Location Map
            </h5>
            <small class="text-muted">{{ $lead->full_name }} • {{ $lead->address ?? 'No Address' }}</small>
        </div>
        <div>
            <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to Details
            </a>
        </div>
    </div>

    <div class="p-3">
        <div id="leadMap" class="map-container shadow border"></div>
    </div>
</div>
@endsection

@section('script')
@php
$locations = [];
if ($lead->latitude && $lead->longitude) {
$locations[] = [
'lat' => (float)$lead->latitude,
'lng' => (float)$lead->longitude,
'type' => 'Captured Location',
'iconClass' => 'bi-geo-alt-fill lead-location',
'wrapperClass' => 'text-danger'
];
}
if ($lead->is_visited && $lead->visited_latitude && $lead->visited_longitude) {
$locations[] = [
'lat' => (float)$lead->visited_latitude,
'lng' => (float)$lead->visited_longitude,
'type' => 'Visited Location',
'iconClass' => 'bi-patch-check-fill visited-location',
'wrapperClass' => 'text-success'
];
}
@endphp

<script>
    $(async function() {
        const locations = {!! json_encode($locations) !!};

        if (locations.length > 0) {
            // Import libraries
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerElement
            } = await google.maps.importLibrary("marker");

            const map = new Map(document.getElementById("leadMap"), {
                center: {
                    lat: locations[0].lat,
                    lng: locations[0].lng
                },
                zoom: 14,
                mapId: "DEMO_MAP_ID", // Required for AdvancedMarkerElement
            });

            const bounds = new google.maps.LatLngBounds();
            const infoWindow = new google.maps.InfoWindow();

            locations.forEach(loc => {
                // Create content DOM node using Bootstrap icons
                const iconContainer = document.createElement('div');
                iconContainer.className = loc.wrapperClass;
                iconContainer.style.fontSize = '2.5rem';
                iconContainer.style.filter = 'drop-shadow(0 4px 6px rgba(0, 0, 0, 0.95))';
                iconContainer.innerHTML = `<i class="bi ${loc.iconClass}"></i>`;

                const position = {
                    lat: loc.lat,
                    lng: loc.lng
                };

                const marker = new AdvancedMarkerElement({
                    map: map,
                    position: position,
                    content: iconContainer,
                    title: loc.type,
                });

                const contentString = `
                        <div class="text-center p-2">
                            <h6 class="mb-1 fw-bold">{{ $lead->full_name }}</h6>
                            <p class="mb-2 text-muted small">${loc.type}</p>
                            <a href="https://www.google.com/maps?q=${loc.lat},${loc.lng}" target="_blank" class="btn btn-sm btn-primary w-100"><i class="bi bi-google me-1"></i> Open in Maps</a>
                        </div>
                    `;

                marker.addListener('click', () => {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });

                bounds.extend(position);
            });

            map.fitBounds(bounds);

            // If only one marker, zoom out a bit so it's not too close
            if (locations.length === 1) {
                const listener = google.maps.event.addListener(map, "idle", function() {
                    if (map.getZoom() > 16) map.setZoom(16);
                    google.maps.event.removeListener(listener);
                });
            }

        } else {
            document.getElementById('leadMap').innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light rounded text-muted">
                        <div class="text-center">
                            <i class="bi bi-geo-alt-fill fs-1 opacity-25"></i>
                            <p class="mt-2">No location data available for this lead.</p>
                        </div>
                    </div>
                `;
        }
    });
</script>
@endsection