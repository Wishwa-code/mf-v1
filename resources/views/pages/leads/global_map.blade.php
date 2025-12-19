@extends('layout.admin')

@section('head')
<title>All Leads Location Map</title>
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
<div class="container-fluid p-0">
    {{-- Header overlay or top bar --}}
    <div class="header-card px-4 py-3 d-flex justify-content-between align-items-center shadow-sm sticky-top mt-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-map-fill me-2 text-primary"></i>All Leads Location Map
            </h5>
            <small class="text-muted">Showing {{ count($leads) }} leads with captured locations</small>
        </div>
        <div>
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-list-ul me-1"></i> View List
            </a>
        </div>
    </div>

    <div class="p-3">
        <div id="leadsMap" class="map-container shadow border"></div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(async function() {
        const leads = JSON.parse('{!! json_encode($leads, 15, 512) !!}');

        if (leads.length > 0) {
            // Import libraries
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerElement
            } = await google.maps.importLibrary("marker");

            const map = new Map(document.getElementById("leadsMap"), {
                center: {
                    lat: 6.9271,
                    lng: 79.8612
                }, // Default center
                zoom: 8,
                mapId: "DEMO_MAP_ID", // Required for AdvancedMarkerElement
            });

            const bounds = new google.maps.LatLngBounds();
            const infoWindow = new google.maps.InfoWindow();

            leads.forEach(lead => {
                if (!lead.latitude || !lead.longitude) return;

                // Choose color based on status
                let iconColorClass = "text-danger";
                // if (lead.is_visited || lead.status === 'approved') {
                //     iconColorClass = "text-danger";
                // } else if (lead.status === 'pending') {
                //     iconColorClass = "text-warning";
                // }

                // Create content DOM node using Bootstrap icons
                const iconContainer = document.createElement('div');
                iconContainer.className = iconColorClass;
                iconContainer.style.fontSize = '2.5rem';
                iconContainer.style.filter = 'drop-shadow(0 4px 6px rgba(28, 1, 1, 0.45))';
                iconContainer.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';

                const position = {
                    lat: parseFloat(lead.latitude),
                    lng: parseFloat(lead.longitude)
                };

                const marker = new AdvancedMarkerElement({
                    map: map,
                    position: position,
                    content: iconContainer,
                    title: lead.full_name
                });

                // Prepare InfoWindow content
                let showUrl = "{{ route('leads.show', ':id') }}".replace(':id', lead.id);
                const contentString = `
                        <div class="text-center p-2" style="min-width: 200px;">
                            <h6 class="mb-1 fw-bold text-dark">${lead.full_name}</h6>
                            <p class="mb-1 text-muted small">${lead.address ? lead.address : ''}</p>
                            <span class="badge bg-light text-dark border mb-2">${lead.status.toUpperCase()}</span>
                            <div class="d-grid gap-2 mt-2">
                                <a href="${showUrl}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i> View Details</a>
                                <a href="https://www.google.com/maps?q=${lead.latitude},${lead.longitude}" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-google me-1"></i> Open in Maps</a>
                            </div>
                        </div>
                    `;

                marker.addListener('click', () => {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });

                bounds.extend(position);
            });

            map.fitBounds(bounds);
        } else {
            document.getElementById('leadsMap').innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light rounded text-muted">
                        <div class="text-center">
                            <i class="bi bi-geo-alt-fill fs-1 opacity-25"></i>
                            <p class="mt-2">No leads with location data found.</p>
                        </div>
                    </div>
                `;
        }
    });
</script>
@endsection