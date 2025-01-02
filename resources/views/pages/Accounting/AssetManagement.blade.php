@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <style>
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-content {
            border-radius: 12px;
            padding: 20px;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            font-size: 1.25rem;
        }

        .table thead th {
            font-weight: 600;
            background-color: #f1f1f1;
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .form-group label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        }

        .radio-group label {
            margin-right: 20px;
        }

        .alert-secondary {
            background-color: #f1f1f1;
            border-color: #ddd;
        }
    </style>
@endsection

@section('content')
{{--    <div class="container mt-4">--}}
{{--        <div class="row mb-4">--}}
{{--            --}}
{{--        </div>--}}
{{--    </div>--}}
<br><br>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">View Asset Management</h4>
        </div>
        <div class="card-body">
            <!-- Full-width Dropdown -->
            <form action="{{ route('assetManagement.search') }}" method="POST">
                {{ csrf_field() }}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="filterOption">Filter By</label>
                        <select id="filterOption" name="filterOption" class="form-control select2">
                            <option value="0">All Types</option>
                            @foreach($asset_management as $item)
                                <option value="{{ $item->id }}" {{ old('filterOption', request('filterOption')) == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column (3 Inputs) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bankAccounts_purchase">Bank Accounts (Purchase):</label>
                            <select id="bankAccounts_purchase" name="bankAccounts_purchase" class="form-control select2">
                                <option value="0">All Bank Accounts</option>
                                @foreach($bank as $item)
                                    <option value="{{ $item->Idbank }}" {{ old('bankAccounts_purchase', request('bankAccounts_purchase')) == $item->Idbank ? 'selected' : '' }}>
                                        {{ $item->Bank_Name }}-{{ $item->Account_No }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Column (3 Inputs) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bankAccounts_sold">Bank Accounts (Sold):</label>
                            <select id="bankAccounts_sold" name="bankAccounts_sold" class="form-control select2">
                                <option value="0">All Bank Accounts</option>
                                @foreach($bank as $item)
                                    <option value="{{ $item->Idbank }}" {{ old('bankAccounts_sold', request('bankAccounts_sold')) == $item->Idbank ? 'selected' : '' }}>
                                        {{ $item->Bank_Name }}-{{ $item->Account_No }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>


        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h6>Export Data for this page</h6>
            <table id="assetTable" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Purchase or Opening Value</th>
                    <th>Opening/Purchase Price</th>
                    <th>Purchase/Opening Date</th>
                    <th>Current Book Amount</th>
                    <th>Sold</th>
                    <th>Sold Date</th>
                    <th>Sold Price</th>
                    <th>Bank Account (Sold)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($asset as $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->purchase_date}}</td>
                        <td>{{$item->purchase_value}}</td>
                        <td>{{$item->purchase_date}}</td>
                        <td>{{$item->current_value}}</td>
                        @if($item->sold_status == NULL)
                            <td>No</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#saleAssetsModal"
                                            onclick="openSaleModal({{$item->id_assest}})">
                                        <i class="fa fa-dollar-sign"></i>
                                    </button>

                                    <!-- Book Values Icon Button -->
                                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#bookValuesModal"
                                            title="Book Values" onclick="openSaleModal_2({{$item->id_assest}})">
                                        <i class="fa fa-book"></i> <!-- Font Awesome Book Icon -->
                                    </button>
                                </div>
                            </td>
                        @else
                            <td>Yes</td>
                            <td>{{$item->sold_date}}</td>
                            <td>{{$item->sold_amount}}</td>
                            <td>{{$item->Bank_Name}}-{{$item->Account_No}}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-primary" disabled>
                                        <i class="fa fa-dollar-sign"></i>
                                    </button>

                                    <!-- Book Values Icon Button -->
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="fa fa-book"></i> <!-- Font Awesome Book Icon -->
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
                <!-- Repeat for more rows -->
                </tbody>
            </table>
        </div>
    </div>


    <!-- Sale Assets Modal -->
    <!-- Sale Assets Modal -->
    <div class="modal fade" id="saleAssetsModal" tabindex="-1" role="dialog" aria-labelledby="saleAssetsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleAssetsModalLabel">Sale Assets</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-secondary" role="alert">
                        <small>Enter the sold date and value to update the asset.</small>
                    </div>
                    <form id="saleForm">
                        @csrf
                        <input type="hidden" id="assetId" name="assetId">
                        <div class="form-group">
                            <label for="soldDate">Sold Date</label>
                            <input type="date" class="form-control" id="soldDate" name="soldDate" placeholder="dd/mm/yyyy">
                        </div>
                        <div class="form-group">
                            <label for="soldValue">Sold Value</label>
                            <input type="text" class="form-control" id="soldValue" name="soldValue">
                        </div>
                        <div class="form-group">
                            <label for="sold_bank">Select the Destination of Fund: Cash/Bank</label>
                            <select class="form-control" id="sold_bank" name="sold_bank">
                                @foreach($bank as $item)
                                    <option value="{{ $item->Idbank }}">{{ $item->Bank_Name }}-{{ $item->Account_No }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveAssetChanges()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Values Modal -->
    <!-- Book Values Modal -->
    <div class="modal fade" id="bookValuesModal" tabindex="-1" role="dialog" aria-labelledby="bookValuesModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookValuesModalLabel">Book Values</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bookValuesForm">
                        <input type="hidden" id="assetId_2" name="assetId_2">
                        <div class="form-group">
                            <label for="book_date">Book Date</label>
                            <input type="date" class="form-control" id="book_date" name="book_date" required>
                        </div>
                        <div class="form-group">
                            <label for="book_value">Book Value</label>
                            <input type="text" class="form-control" id="book_value" name="book_value">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveBookValues">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
@section('script')

    <!-- jQuery (Full version) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let x = ["#book_value","#soldValue"];
            decimalFormat(x);
            $('.select2').select2();
        });

        function openSaleModal(item_id) {
            $('#assetId').val(item_id);
        }

        function openSaleModal_2(item_id) {
            $('#assetId_2').val(item_id);
        }

        function saveAssetChanges() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update the asset information?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $('#saleForm').serialize();

                    $.ajax({
                        url: '{{ route("asset.update") }}',
                        type: 'POST',
                        data: formData,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: 'Asset has been updated.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload(); // Reload the page to reflect changes
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an issue updating the asset.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to update asset.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }


        // Save Book Values
        $('#saveBookValues').click(function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save the book values?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $('#bookValuesForm').serialize();

                    $.ajax({
                        url: '{{ route("bookValues.store") }}',
                        type: 'POST',
                        data: formData,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Saved!',
                                    text: 'Book values have been saved.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload(); // Reload the page to reflect changes
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an issue saving the book values.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to save book values.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

    </script>
@endsection

