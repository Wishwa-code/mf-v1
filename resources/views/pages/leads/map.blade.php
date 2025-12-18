@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        {{-- Header overlay or top bar --}}
        <div class="header-card px-4 py-3 d-flex justify-content-between align-items-center shadow-sm sticky-top">
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(function () {
             const locations = [
                @if($lead->latitude && $lead->longitude)
                {
                    lat: {{ $lead->latitude }},
                    lng: {{ $lead->longitude }},
                    type: "Captured Location",
                    iconClass: "bi-geo-alt-fill",
                    wrapperClass: "text-primary"
                },
                @endif
                @if($lead->is_visited && $lead->visited_latitude && $lead->visited_longitude)
                {
                    lat: {{ $lead->visited_latitude }},
                    lng: {{ $lead->visited_longitude }},
                    type: "Visited Location",
                    iconClass: "bi-patch-check-fill",
                    wrapperClass: "text-success"
                }
                @endif
            ];

            if (locations.length > 0) {
                const map = L.map('leadMap', { zoomControl: false });
                L.control.zoom({ position: 'bottomright' }).addTo(map);
                
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                const bounds = L.latLngBounds();

                locations.forEach(loc => {
                    const customIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="${loc.wrapperClass}" style="font-size: 2.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));"><i class="bi ${loc.iconClass}"></i></div>`,
                        iconSize: [40, 50],
                        iconAnchor: [20, 50],
                        popupAnchor: [0, -45]
                    });

                    const marker = L.marker([loc.lat, loc.lng], { icon: customIcon }).addTo(map);
                    
                    // Hover tooltip
                    marker.bindTooltip(`
                        <div class="text-center p-2">
                            <h6 class="mb-0 fw-bold">{{ $lead->full_name }}</h6>
                            <span class="badge bg-light text-dark border mt-1">${loc.type}</span>
                        </div>
                    `, { 
                        permanent: false, 
                        direction: 'top', 
                        offset: [0, -40],
                        className: 'shadow-lg rounded border-0 px-0 py-0 overflow-hidden' 
                    });

                    // Click popup
                    marker.bindPopup(`
                        <div class="text-center p-2">
                            <h6 class="mb-1 fw-bold">{{ $lead->full_name }}</h6>
                            <p class="mb-2 text-muted small">${loc.type}</p>
                            <a href="https://www.google.com/maps?q=${loc.lat},${loc.lng}" target="_blank" class="btn btn-sm btn-primary w-100"><i class="bi bi-google me-1"></i> Open in Maps</a>
                        </div>
                    `);

                    bounds.extend([loc.lat, loc.lng]);
                });

                map.fitBounds(bounds, { padding: [100, 100], maxZoom: 16 });
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
