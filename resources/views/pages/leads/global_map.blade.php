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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(function () {
            const leads = @json($leads);

            if (leads.length > 0) {
                const map = L.map('leadsMap', { zoomControl: false });
                L.control.zoom({ position: 'bottomright' }).addTo(map);
                
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                const bounds = L.latLngBounds();

                leads.forEach(lead => {
                    // Choose color based on status
                    let iconColorClass = "text-primary";
                    if(lead.is_visited || lead.status === 'approved') {
                         iconColorClass = "text-success";
                    } else if (lead.status === 'pending') {
                         iconColorClass = "text-warning";
                    }

                    const customIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="${iconColorClass}" style="font-size: 2.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));"><i class="bi bi-geo-alt-fill"></i></div>`,
                        iconSize: [40, 50],
                        iconAnchor: [20, 50],
                        popupAnchor: [0, -45]
                    });

                    const marker = L.marker([lead.latitude, lead.longitude], { icon: customIcon }).addTo(map);
                    
                    // Hover tooltip
                    marker.bindTooltip(`
                        <div class="text-center p-2">
                            <h6 class="mb-0 fw-bold">${lead.full_name}</h6>
                            <span class="badge bg-light text-dark border mt-1">${lead.status.toUpperCase()}</span>
                        </div>
                    `, { 
                        permanent: false, 
                        direction: 'top', 
                        offset: [0, -40],
                        className: 'shadow-lg rounded border-0 px-0 py-0 overflow-hidden' 
                    });

                    // Click popup
                    // Use route helper with a placeholder for ID, then replace it in JS
                    // We assume 'leads.show' route exists as it was used in map.blade.php
                    let showUrl = "{{ route('leads.show', ':id') }}".replace(':id', lead.id);

                    marker.bindPopup(`
                        <div class="text-center p-2">
                            <h6 class="mb-1 fw-bold">${lead.full_name}</h6>
                            <p class="mb-1 text-muted small">${lead.address ? lead.address : ''}</p>
                            <div class="d-grid gap-2 mt-2">
                                <a href="${showUrl}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i> View Details</a>
                                <a href="https://www.google.com/maps?q=${lead.latitude},${lead.longitude}" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-google me-1"></i> Open in Maps</a>
                            </div>
                        </div>
                    `);

                    bounds.extend([lead.latitude, lead.longitude]);
                });

                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
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
