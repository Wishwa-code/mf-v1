@extends('layout.admin')

@section('head')
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Verified Leads History</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small text-muted">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leads.index') }}" class="text-decoration-none text-muted">Leads</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Verified History</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Styles -->
    <style>
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            transition: all 0.2s;
        }

        /* Table Styling */
        .table-modern thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #edf2f9;
            padding: 1rem 0.75rem;
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f9;
            color: #495057;
            font-size: 0.9rem;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table id="verified-list-table" class="table table-modern table-borderless dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Route</th>
                                    <th>Source</th>
                                    <th>District</th>
                                    <th>City</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Visit Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lead Details Modal (Standard) -->
<div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-labelledby="leadDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="leadDetailsModalLabel">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="leadDetailsModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        (async () => {
            // Basic maps import if needed for other functionality
            await google.maps.importLibrary("maps");
            await google.maps.importLibrary("marker");
        })();

        $('#verified-list-table').DataTable({
            processing: true,
            serverSide: true, // Yajra
            ajax: "{{ route('leads.verifiedData') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'full_name',
                    name: 'full_name',
                    render: function(data, type, row) {
                        return `
                            <div>
                                <div class="fw-bold text-dark">${data}</div>
                                <div class="mt-1">${row.action}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'route.route_id',
                    name: 'route.name',
                    defaultContent: '-'
                },
                {
                    data: 'source',
                    name: 'source',
                    defaultContent: '-'
                },
                {
                    data: 'district',
                    name: 'district',
                    defaultContent: '-'
                },
                {
                    data: 'city',
                    name: 'city',
                    defaultContent: '-'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'is_visited',
                    name: 'is_visited'
                },
                // Visit Info column was empty in previous HTML but not in JS columns? 
                // Ah, previous JS had `Visit Info` in HTML but not mapped in JS? 
                // Wait, previous JS code: columns: [id, full_name, phone, address, status, is_visited, action].
                // The HTML had <th>Visit Info</th> but no data mapping.
                // I will stick to the columns mapped in the JS.
            ],
            order: [
                [0, 'desc'] // Order by ID (index 0)
            ],
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },
            language: {
                searchPlaceholder: "Search records...",
                search: "",
                lengthMenu: "Show _MENU_ entries"
            },
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light btn-sm border'
                }
            ],
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm').css('margin-left', '10px');
                $('.dt-buttons').addClass('d-flex gap-1');
            }
        });
    });

    // View Lead Modal
    function viewLeadModal(id) {
        const modalBody = document.getElementById('leadDetailsModalBody');
        modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading details...</p>
        </div>
    `;

        const modal = new bootstrap.Modal(document.getElementById('leadDetailsModal'));
        modal.show();

        $.ajax({
            url: "/leads/verified-details-modal/" + id,
            type: "GET",
            success: function(html) {
                modalBody.innerHTML = html;
            },
            error: function() {
                modalBody.innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-circle fs-1 mb-2"></i>
                    <p>Failed to load lead details. Please try again.</p>
                </div>
            `;
            }
        });
    }

    function rejectLead(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to reject this lead. This action cannot be undone immediately!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/leads/" + id + "/reject",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Rejected!',
                                response.message,
                                'success'
                            );
                            $('#verified-list-table').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endsection