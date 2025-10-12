@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .style-tr>td {
            padding: 2px 15px
        }
    </style>
@endsection


@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Expense Details</h4>
                        </div>

                        @if(session('branch_id') == -1)
                        <div class="mb-3">
                            <label for="branch_selector" class="form-label">Select Branch<span class="required-asterisk">*</span></label>
                            <select id="branch_selector" class="form-control select2">
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="mb-3" hidden>
                            <label for="simpleinput" class="form-label">Type<span class="required-asterisk">*</span></label>
                            <select id="type">
                                <option value="Income">Income</option>
                                <option value="Expense" selected>Expense</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Category<span class="required-asterisk">*</span></label>
                            <select id="category" class="form-control select2">
                                @foreach($expences_category as $item)
                                    <option value="{{ $item->Idbank }}">{{ $item->Bank_Name }}</option>
                                @endforeach
                            </select>
                            <br><br>
                            <input type="button" class="btn btn-danger" id="addCategory" value="Add Category">
                        </div>

                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Bank<span class="required-asterisk">*</span></label>
                            <select id="bank" class="form-control select2">
                                @foreach($bank as $item)
                                    <option value="{{ $item->Idbank }}">{{ $item->Bank_Name }}-{{ $item->Account_No }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Reason<span class="required-asterisk">*</span></label>
                            <input type="text" id="reason" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Date<span class="required-asterisk">*</span></label>
                            <input type="date" id="date_choose" class="form-control" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Amount<span class="required-asterisk">*</span></label>
                            <input type="text" id="expences_amount" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="validateSubmitExpense(event)"><i
                                    class="bi bi-save"></i>&nbsp;&nbsp;Save Expense</button>
                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>

        <!-- Modal for Adding a New Category -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="categoryForm">
                            <div class="mb-3">
                                <label for="categoryDescription" class="form-label">Category Description</label>
                                <input type="text" class="form-control" id="categoryDescription" name="description" required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save Category</button>
                            </div>
                        </form>
                        <hr>
                        <h5>Existing Categories</h5>
                        <table id="categoriesTable" class="table table-bordered mt-3">
                            <thead>
                            <tr>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($expences_category as $item)
                                    <tr>
                                        <td>{{$item->Bank_Name}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>





    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/expenses.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        // Open the modal on "Add Category" button click
        $('#addCategory').on('click', function() {
            $('#categoryModal').modal('show');
        });

        // Handle form submission
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to add a new category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('expenses_categories.store') }}', // URL to send the request to
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Category added successfully!",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload(); // Reload the page
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred: ' + xhr.responseText,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
        // Handle category deletion
        $(document).on('click', '.delete-category', function() {
            var categoryId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('categories.destroy', ':id') }}'.replace(':id', categoryId), // URL to delete category
                        type: 'DELETE',
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Category deleted successfully!",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload(); // Reload the page
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred: ' + xhr.responseText,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Branch selector for head office
        @if(session('branch_id') == -1)
        $('#branch_selector').on('change', function() {
            const branchId = $(this).val();
            
            if (!branchId) {
                $('#category').empty().append('<option value="">-- Select Branch First --</option>');
                $('#bank').empty().append('<option value="">-- Select Branch First --</option>');
                return;
            }

            $.ajax({
                url: '/get-branch-expense-data',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { branch_id: branchId },
                success: function(response) {
                    // Update categories dropdown
                    $('#category').empty();
                    if (response.categories.length > 0) {
                        response.categories.forEach(function(cat) {
                            $('#category').append(
                                $('<option></option>').val(cat.Idbank).text(cat.Bank_Name)
                            );
                        });
                    } else {
                        $('#category').append('<option value="">No categories found</option>');
                    }
                    $('#category').trigger('change');

                    // Update banks dropdown
                    $('#bank').empty();
                    if (response.banks.length > 0) {
                        response.banks.forEach(function(bank) {
                            $('#bank').append(
                                $('<option></option>').val(bank.Idbank).text(bank.Bank_Name + '-' + bank.Account_No)
                            );
                        });
                    } else {
                        $('#bank').append('<option value="">No banks found</option>');
                    }
                    $('#bank').trigger('change');
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load branch data: ' + xhr.responseText,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        @endif
    </script>
@endsection
