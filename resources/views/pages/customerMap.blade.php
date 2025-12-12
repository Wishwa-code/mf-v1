@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #customerMap {
            width: 100%;
            height: 650px !important;
            min-height: 650px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .map-wrapper-card {
            background: #fff;
            padding: 15px 20px 20px;
            border-radius: 12px;
            border: 1px solid #eee;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>
@endsection

@section('content')
    <div class="row mb-2">
        <div class="col-md-3">
            <label class="form-label">Route</label>
            <select id="routeFilter" class="form-select">
                <option value="">All Routes</option>
                @foreach(tableWithBranch('route')->get() as $r)
                    <option value="{{ $r->id_route }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="map-wrapper-card">
                <h4 class="page-title mb-2">All Customers - Location Map</h4>
                <div id="customerMap"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>

        // ICONS
        const redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });

        const blueIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });

        const yellowIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });

        let map;
        let markersLayer;

        const SL_CENTER = [7.8731, 80.7718];
        const SL_BOUNDS = [
            [5.8, 79.4],
            [9.9, 81.9]
        ];

        function initMap() {
            map = L.map('customerMap', {
                zoomControl: true,
                minZoom: 7,
                maxZoom: 18,
            }).setView(SL_CENTER, 8);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            map.setMaxBounds(SL_BOUNDS);

            markersLayer = L.layerGroup().addTo(map);

            setTimeout(() => map.invalidateSize(), 500);

            loadCustomers();
        }

        function loadCustomers() {
            fetch("{{ route('customers.mapData') }}")
                .then(res => res.json())
                .then(data => {
                    markersLayer.clearLayers();

                    const customers = data.customers || [];
                    const pts = [];

                    customers.forEach(c => {
                        const lat = parseFloat(c.lat);
                        const lng = parseFloat(c.lng);

                        if (isNaN(lat) || isNaN(lng)) return;

                        // SELECT PIN COLOR
                        let icon = redIcon;
                        if (c.pin_color === 'yellow') icon = yellowIcon;
                        if (c.pin_color === 'blue') icon = blueIcon;

                        const marker = L.marker([lat, lng], { icon });

                        const photoHtml = c.photo
                            ? `<div style="margin-bottom:6px;text-align:center;">
                               <img src="${c.photo}" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:1px solid #ccc;">
                           </div>`
                            : '';

                        const gmapUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                        marker.bindPopup(`
                        ${photoHtml}
                        <b>${c.name}</b><br>
                        <small>Cus No: ${c.cus_number}</small><br>
                        <small>${c.phone}</small><br>
                        <small>${c.address}</small><br>
                        <a href="${gmapUrl}" class="btn btn-sm btn-outline-primary" target="_blank">📍 Google Map</a>
                    `);

                        marker.addTo(markersLayer);
                        pts.push([lat, lng]);
                    });

                    if (pts.length > 0) {
                        map.fitBounds(pts, { padding: [40, 40] });
                    } else {
                        map.setView(SL_CENTER, 8);
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', initMap);

    </script>

@endsection

