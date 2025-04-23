@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .nav-tabs .nav-link {
            color: #555;
            font-weight: 500;
            border: none;
            padding: 12px 20px;
        }

        .nav-tabs .nav-link.active {
            background-color: #fff;
            border: none;
            border-bottom: 3px solid #0d6efd;
            color: #0d6efd;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        .tab-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .table th, .table td {
            font-size: 14px;
        }
    </style>
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }
    </style>

@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="page-title"></h4>
                </div>
                <div class="mb-4">
                    <label for="customerSelect" class="form-label">Select Customer</label>
                    <select id="customerSelect" class="form-select">
                        <option value="0" selected>-- Choose Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->idCustomer }}">{{ $customer->cus_number }}-{{ $customer->First_Name }} {{ $customer->Last_Name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="container-fluid">
                                <h2 class="mb-4"><i class="fas fa-id-card me-2 text-primary"></i>Know Your Customer</h2>

                                <ul class="nav nav-tabs" id="kycTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#basic" role="tab"><i
                                                    class="fas fa-user me-1"></i>Basic Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#guardian" role="tab"><i
                                                    class="fas fa-user-shield me-1"></i>Guardian Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab"><i
                                                    class="fas fa-file-alt me-1"></i>Documents</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#loans" role="tab"><i
                                                    class="fas fa-money-bill me-1"></i>Loans</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#loanSummary" role="tab"><i
                                                    class="fas fa-chart-bar me-1"></i>Guaranteed Loans</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#RoadMap" role="tab"><i
                                                    class="fas fa-chart-bar me-1"></i>Road Map</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#insurance" role="tab"><i
                                                    class="fas fa-shield-alt me-1"></i>Insurance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab"><i
                                                    class="fas fa-history me-1"></i>History</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="kycTabContent">
                                    <div class="tab-pane fade show active" id="basic" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="guardian" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="documents" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="loans" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="loanSummary" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="RoadMap" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="insurance" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="history" role="tabpanel"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
@section('script')
    <!-- jQuery (required by Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selectedId = "{{ $id ?? 0 }}";
            const customerSelect = document.getElementById("customerSelect");

            if (selectedId && selectedId !== "0") {
                customerSelect.value = selectedId;

                // Trigger change event if needed
                const event = new Event('change');
                customerSelect.dispatchEvent(event);
            }
        });
    </script>

    <script>
        let selectedCustomerId = 0;

        $(document).ready(function () {
            // Initialize Select2
            $('#customerSelect').select2({
                placeholder: "-- Choose Customer --",
                allowClear: true,
                width: '100%'
            });



            // Store selected customer ID
            $('#customerSelect').on('change', function () {
                selectedCustomerId = $(this).val();

                if (selectedCustomerId && selectedCustomerId !== '0') {
                    const activeTab = $('#kycTabs .nav-link.active').attr('href').replace('#', '');
                    loadTabContent(activeTab, selectedCustomerId);
                } else {
                    clearTabs();
                }
            });

            // Load content when tab is clicked
            $('#kycTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const tabId = $(e.target).attr('href').replace('#', '');
                if (selectedCustomerId && selectedCustomerId !== '0') {
                    loadTabContent(tabId, selectedCustomerId);
                } else {
                    $('#kycTabContent .tab-pane').html('<p class="text-muted">Please select a customer to view details.</p>');
                    $('#customerSelect').focus();
                }
            });

            // Initial placeholder in each tab
            function clearTabs() {
                $('#kycTabContent .tab-pane').html('<p class="text-muted">Please select a customer to view details.</p>');
            }

            // Load tab via AJAX
            function loadTabContent(tabId, customerId) {
                $('#' + tabId).html('<p>Loading...</p>');

                $.ajax({
                    url: `/kyc/${tabId}/${customerId}`,
                    type: 'GET',
                    success: function (data) {
                        $('#' + tabId).html(data);

                        if (tabId === 'basic') {
                            setTimeout(load_map, 300); // Delay to ensure map container is ready
                        }
                    },
                    error: function () {
                        $('#' + tabId).html('<p class="text-danger">Error loading tab data.</p>');
                    }
                });
            }

            // Leaflet Map Logic
            function load_map() {
                const mapContainer = document.getElementById('map');
                if (!mapContainer) return;

                const lat = parseFloat(mapContainer.dataset.lat || 0);
                const lng = parseFloat(mapContainer.dataset.lng || 0);

                if (lat && lng) {
                    const map = L.map('map').setView([lat, lng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    L.marker([lat, lng]).addTo(map)
                        .bindPopup("Customer Location")
                        .openPopup();
                } else {
                    mapContainer.innerHTML = '<p class="text-muted">Location not available.</p>';
                }
            }

            // Optional: Load default tab on page load if customer already selected
            selectedCustomerId = $('#customerSelect').val();
            if (selectedCustomerId && selectedCustomerId !== '0') {
                loadTabContent('basic', selectedCustomerId);
            } else {
                clearTabs();
            }
        });
    </script>
@endsection

