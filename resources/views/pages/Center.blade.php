@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link href="{{ asset('css/ho-dashboard.css') }}" rel="stylesheet">

    <style>
        .style-tr>td {
            padding: 2px 15px
        }
        /* Custom thead style */
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }
        /* Reduce branch card height */
        .branch-card {
            height: 180px !important; /* Reduced from default */
        }
        .branch-card .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 10px;
        }
        .branch-card .metric-card {
            padding: 8px !important;
            font-size: 0.85rem !important;
        }
        .branch-card .metric-value {
            font-size: 1.1rem !important;
        }
        /* Adjust back card for reduced height */
        .branch-card .card-back {
            height: 180px !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
        .branch-card .switch-icon i {
            font-size: 2rem;
        }
        .branch-card .switch-text {
            font-size: 1rem;
            margin: 8px 0 4px;
        }
        .branch-card .switch-subtitle {
            font-size: 0.8rem;
        }
    </style>
@endsection


@section('content')
    <div class="container-fluid">
        @php $isHeadOffice = session('branch_id') == -1; @endphp
        
        @if($isHeadOffice)
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <button class="refresh-btn" onclick="window.location.reload()">
                    <i class="ri-refresh-line"></i>Refresh
                </button>
                <h1 class="dashboard-title"><i class="ri-building-2-line dashboard-title-icon"></i>centers summery</h1>
                <p class="dashboard-subtitle">{{ date('d/m/Y, H:i:s A') }}</p>
            </div>

             <!-- Branch Cards (similar to ho-dashboard) -->
             <div class="branch-grid">
                 @php
                     $colors = ['purple', 'pink', 'blue', 'orange', 'green'];
                     $index = 0;
                     // Group data by branch
                     $branchData = collect($userData)->groupBy('branch_name')->map(function($centers, $branchName) {
                         return [
                             'name' => $branchName ?? 'Unknown Branch',
                             'centers' => $centers->count(),
                             'routes' => $centers->pluck('name')->unique()->count(),
                             'groups' => $centers->sum('Groups'),
                             'members' => $centers->sum('Members'),
                             'avg_members' => $centers->count() > 0 ? round($centers->sum('Members') / $centers->count(), 1) : 0
                         ];
                     });
                 @endphp
                 
                 @foreach($branchData as $b)
                     @php
                         // Find branch ID for switching
                         $branchId = collect($userData)->where('branch_name', $b['name'])->first()->branch_id ?? null;
                     @endphp
                     <div class="branch-card {{ $colors[$index % count($colors)] }}" data-branch="{{ $branchId }}" title="Click to switch to {{ $b['name'] }} branch">
                         <div class="card-inner">
                             <!-- Front of Card -->
                             <div class="card-front">
                                 <div class="branch-header">
                                     <h3 class="branch-name">{{ $b['name'] }}</h3>
                                     <div class="branch-icon"><i class="ri-building-line"></i></div>
                                 </div>
                                 
                                 <div class="metrics-grid">
                                     <div class="metric-card">
                                         <div class="metric-label">Routes</div>
                                         <div class="metric-value">{{ number_format($b['routes']) }}</div>
                                     </div>
                                     <div class="metric-card">
                                         <div class="metric-label">Centers</div>
                                         <div class="metric-value">{{ number_format($b['centers']) }}</div>
                                     </div>
                                     <div class="metric-card">
                                         <div class="metric-label">Groups</div>
                                         <div class="metric-value">{{ number_format($b['groups']) }}</div>
                                     </div>
                                     <div class="metric-card">
                                         <div class="metric-label">Members</div>
                                         <div class="metric-value">{{ number_format($b['members']) }}</div>
                                     </div>
                                 </div>
                             </div>
                             
                             <!-- Back of Card -->
                             <div class="card-back">
                                 <div class="switch-icon">
                                     <i class="ri-arrow-left-right-line"></i>
                                 </div>
                                 <div class="switch-text">Switch Branch</div>
                                 <div class="switch-subtitle">Click to switch to {{ $b['name'] }}</div>
                             </div>
                         </div>
                     </div>
                     @php $index++; @endphp
                 @endforeach
             </div>

             <!-- Summary Cards -->
             <div class="summary-cards">
                 @php
                     $totalRoutes = count($route);
                     $totalCenters = count($userData);
                     $totalGroups = collect($userData)->sum('Groups');
                     $totalMembers = collect($userData)->sum('Members');
                     $routeNames = collect($route)->pluck('name')->unique();
                     $branchNames = collect($userData)->pluck('branch_name')->filter()->unique();
                 @endphp
                 
                 <div class="summary-card blue">
                     <div class="summary-label">Total Routes</div>
                     <div class="summary-value">{{ number_format($totalRoutes) }}</div>
                 </div>
                 
                 <div class="summary-card green">
                     <div class="summary-label">Total Centers</div>
                     <div class="summary-value">{{ number_format($totalCenters) }}</div>
                 </div>
                 
                 <div class="summary-card orange">
                     <div class="summary-label">Total Groups</div>
                     <div class="summary-value">{{ number_format($totalGroups) }}</div>
                 </div>
                 
                 <div class="summary-card teal">
                     <div class="summary-label">Total Members</div>
                     <div class="summary-value">{{ number_format($totalMembers) }}</div>
                 </div>
                 
                 <div class="summary-card purple">
                     <div class="summary-label">Active Branches</div>
                     <div class="summary-value">{{ number_format(count($branchNames)) }}</div>
                 </div>
             </div>
         @endif

         <div class="row {{ $isHeadOffice ? 'mt-4' : 'mt-3' }}">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Centers{{ $isHeadOffice ? ' Details' : '' }}</h4>
                        </div>
                        <table id="centerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Route</th>
                                <th>Center No</th>
                                <th>Branch</th>
                                <th>Center Name</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th>Center In-charge</th>
                                <th class="text-center">Group Count</th>
                                <th class=" text-center">Member Count</th>
                                <th style="width:20%" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($userData as $item)
                                <tr class="style-tr">
                                    <td>{{$item->name ?? '-'}}</td>
                                    <td>{{$item->No}}</td>
                                    <td>{{$item->branch_name ?? '-'}}</td>
                                    <td>{{$item->Name}}</td>
                                    <td>{{$item->Contact_no}}</td>
                                    <td>{{$item->Address}}</td>
                                    <td>{{$item->Location}}</td>
                                    <td>{{$item->Center_incharge}}</td>
                                    <td class="text-center">{{$item->Groups}}</td>
                                    <td class="text-center">{{$item->Members}}</td>
                                    <td  class="text-center">
                                        <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal"
                                                data-canter-no="{{$item->No}}" data-center-name="{{$item->Name}}" data-contact-no="{{$item->Contact_no}}"
                                                data-address="{{$item->Address}}" data-route="{{$item->Route}}" data-location="{{$item->Location}}" data-center-incharge="{{$item->Center_incharge}}"
                                                data-center-id="{{$item->idCenter}}" data-route-id="{{$item->id_route}}" data-bs-placement="top" title="Edit Center">
                                            <i class="bi bi-pencil fs-4"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" onclick="delete_center('{{$item->idCenter}}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Center">
                                            <i class="bi bi-trash fs-4"></i>
                                        </button>

                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>


        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <h4>Edit Center</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="center_id">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="center_incharge" class="form-label">Select Route<span class="required-asterisk">*</span></label>
                                    <select class="form-control" id="route_id">
                                        @foreach($route as $item)
                                            <option value="{{$item->id_route}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_number" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Contact Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="contact" class="form-control">
                                </div>
                                <div class="mb-3" hidden>
                                    <label for="simpleinput" class="form-label">Route<span class="required-asterisk">*</span></label>
                                    <input type="text" id="route" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Address<span class="required-asterisk">*</span></label>
                                    <input type="text" id="address" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Location<span class="required-asterisk">*</span></label>
                                    <input type="text" id="location" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center In-charge<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_incharge" class="form-control">
                                </div>


                            </div>



                        </div>


                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="update_center()">Update changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->








    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js?n=6"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {

            // Replace special characters in the company name
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ');

            $('#centerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 10; // Example customization
                            doc.styles.tableHeader.fontSize = 12;
                        },
                        filename: companyName
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        filename: companyName
                    }
                ],
                initComplete: function(settings, json) {
                    // Initialize tooltips after DataTable is fully initialized
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });


            $(document).on('click', '.edit-btn', function() {
                const editButton = $(this); // Use $(this) instead of document.querySelector
                // Retrieve the data attributes from the button
                const No = editButton.data('canter-no');
                const name = editButton.data('center-name');
                const Contact_no = editButton.data('contact-no');
                const Address = editButton.data('address');
                const route = editButton.data('route');
                const center_incharge = editButton.data('center-incharge');
                const center_id = editButton.data('center-id');
                const location = editButton.data('location');
                const route_id = editButton.data('route-id');

                // Set the values of the input fields
                $('#center_number').val(No);
                $('#center_name').val(name);
                $('#contact').val(Contact_no);
                $('#address').val(Address);
                $('#route').val(route);
                $('#center_incharge').val(center_incharge);
                $('#center_id').val(center_id);
                $('#location').val(location);
                $('#route_id').val(route_id);
            });

            // Initialize tooltips on document ready
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Branch switching functionality (reload current page)
        document.querySelectorAll('.branch-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.getAttribute('data-branch');
                if(!id) return;
                fetch('/update-branch', {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                    body:JSON.stringify({branch_id:id})
                }).then(r=>{
                    if(!r.ok) throw new Error('Switch failed');
                    return r.json().catch(()=>({}));
                }).then(()=>{
                    window.location.reload()
                }).catch(()=>{
                    alert('Failed to switch branch');
                });
            });
        });

    </script>
@endsection
