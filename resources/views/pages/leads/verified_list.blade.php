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

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3">
                <h4 class="card-title mb-0" style="font-weight: 600; color: #333;">Verified Leads History</h4>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="verified-list-table" class="table table-hover table-striped dt-responsive nowrap w-100" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Lead Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Visit Status</th>
                                <th>Visit Info</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                        name: 'full_name'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
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
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                language: {
                    searchPlaceholder: "Search records",
                    search: "",
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-light btn-sm'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control').css('margin-left', '10px');
                    $('.dt-buttons .btn').removeClass('btn-secondary').addClass('btn-light btn-sm border');
                }
            });
        });

        // View Lead Modal
        function viewLeadModal(id) {
            // Show modal with loading state
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

            // Fetch details
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

    <!-- Lead Details Modal -->
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