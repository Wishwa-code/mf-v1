@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>



        h2 {
            margin-top: 0;
        }

        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .form-group label {
            flex: 1;
            margin-bottom: 0;
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            flex: 2;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            height: 80px;
        }

        button.add-edit-btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        button.submit-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button.add-edit-btn:hover,
        button.submit-btn:hover {
            background-color: #0056b3;
        }

        .radio-group {
            display: flex;
            justify-content: space-between;
            /* Spread out radio buttons */
            gap: 40px;
            /* Space between radio buttons */
        }

        .radio-group label {
            display: flex;
            align-items: center;
            flex: 1;
            /* Ensure labels take equal width */
        }

        .radio-group input[type="radio"] {
            margin-right: 12px;
            /* Space between radio button and label text */
        }

        .description {
            font-size: 12px;
            color: #666;
            background-color: #f0f0f0;
            /* Light gray background */
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .description-title {
            font-size: 14px;
            font-weight: bold;
            color: red;
            /* Title color */
        }


        .optional .warning,
        .section .warning {
            background-color: #ffcc00;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
@endsection


@section('content')

    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="container">
                    <h2>Add Asset Management - Non-Current Assets</h2>
                    <p>Please note that we have a separate feature for bank accounts at Admin(top menu) — Accounting — Bank
                        Accounts.</p>

                    <form action="#" method="post" id="saveAssest">
                        <!-- Required Fields Section -->
                        <div class="section">
                            <h3>Required Fields:</h3>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select id="type" name="type">
                                @foreach($asset_management as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                                </select>
                                <button type="button" class="add-edit-btn">Add/Edit</button>
                            </div>

                            <div class="form-group">
                                <label class="description-title">Purchase or Opening Date and Value:</label>
                                <div class="description">
                                    This is the value at which you acquired the asset either by purchasing it or from owner's
                                    equity. If you
                                    purchased the asset and it was too long ago, you can enter the opening date and value here as of
                                    start of.
                                </div>
                            </div>

                            <div class="radio-group">
                                <h4>Are you entering the Purchase or Opening Date and Value:</h4>
                                <div class="form-check ">
                                    <input class="form-check-input" type="radio" id="purchase_date" name="date_option" value="purchase_date" checked>
                                    <label class="form-check-label" for="purchase_date">
                                        Purchase Date and Value
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="opening_date" name="date_option" value="opening_date">
                                    <label class="form-check-label" for="opening_date">
                                        Opening Date and Value
                                    </label>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="purchase_date_value">Purchase/Opening Date</label>
                                <input type="date" id="purchase_date_value" name="purchase_date_value">
                            </div>

                            <div class="form-group">
                                <label for="purchase_value">Purchase/Opening Value</label>
                                <input type="text" id="purchase_value" name="purchase_value" step="0.01">
                            </div>

                            <div class="form-group">
                                <label for="source_funds">Select the Source of Funds: Cash/Bank</label>
                                <select id="source_funds" name="source_funds">
                                    @foreach($bank as $item)
                                    <option value="{{ $item->Idbank }}">{{ $item->Bank_Name }}-{{ $item->Account_No }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Optional Fields Section -->
                        <div class="section optional">
                            <h3>Optional Fields:</h3>
                            <p class="warning">Only valid for tangible (physical) assets like equipment, furniture, computers, and
                                vehicles. The below values have no effect on the accounting reports. Please use them for your record
                                keeping purpose only.</p>

                            <div class="form-group">
                                <label for="replacement_value">Description of the asset</label>
                                <input type="text" id="replacement_value" name="replacement_value" step="0.01">
                            </div>

                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" id="serial_number" name="serial_number">
                            </div>

                            <div class="form-group">
                                <label for="bought_from">Bought From (Only valid for tangible assets)</label>
                                <input type="text" id="bought_from" name="bought_from">
                            </div>

                            <div class="form-group">
                                <label for="description">Long Description</label>
                                <textarea id="description" name="description"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="upload">Invoice/Receipt/Photo</label>
                                <input type="file" id="upload" name="upload">
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">Save Asset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Adding a New Type -->
    <div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog" aria-labelledby="addTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTypeModalLabel">Add New Type</h5>
                </div>
                <form id="addTypeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newType">New Type</label>
                            <input type="text" class="form-control" id="newType" name="newType" placeholder="Enter Type" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select form-control" id="category" name="category">
                                <option value="Current Assets">Current Assets</option>
                                <option value="Non-Current Assets">Non-Current Assets</option>
                            </select>
                        </div>

                        <!-- Table to load existing types -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="typesTable">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="typesTableBody">
                                @foreach($asset_management as $item)
                                    <tr>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->category}}</td>
                                        <td><input type="button" class="btn btn-danger" value="Delete" onclick="delete_item({{$item->id}});"></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            let x = ["#purchase_value"];
            decimalFormat(x);

            // Initialize Select2
            // $('#type, #source_funds').select2();

            // Handle form submission with AJAX
            $('#saveAssest').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to save this ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Save it!",
                }).then((result) => {
                    if (result.isConfirmed) {

                        let formData = new FormData(this);

                        // Get selected radio button value (for date_option group)
                        let selectedDateOption = $("input[name='date_option']:checked").val();
                        formData.append('date_option', selectedDateOption); // Add it to formData

                        $.ajax({
                            url: '{{ route('asset.store') }}', // URL for form submission
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function(response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully saved!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to add asset. Please try again.',
                                });
                            }
                        });
                    }
                });
            });




            // Show the modal when Add/Edit button is clicked
            $('.add-edit-btn').on('click', function() {
                $('#addTypeModal').modal('show');
            });


            // Handle form submission using AJAX
            $('#addTypeForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the form from reloading the page



                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to save this ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Save it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = {
                            newType: $('#newType').val(),
                            category: $('#category').val(),
                            _token: $('input[name="_token"]').val()
                        };

                        $.ajax({
                            url: '{{ route("type.store") }}', // Route to store the new type
                            method: 'POST',
                            data: formData,
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function(response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully saved!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to add type. Please try again.',
                                });
                            }
                        });
                    }
                });



            });
        });
        function delete_item(id) {


            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to remove this ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Remove it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/delete-asset-type/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {

                            if(response.success===false){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'This type is already used in an asset !',
                                });
                            }else{
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully Removed !",
                                }).then(function () {
                                    window.location.reload();
                                });
                            }


                        },
                        error: function(xhr) {
                            alert('Failed to delete the asset type.');
                        }
                    });
                }
            });
        }
    </script>
@endsection

