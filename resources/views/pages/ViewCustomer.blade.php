@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }
    </style>

    <style>
        .style-tr > td {
            padding: 2px 15px;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 0.25rem;
        }

        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }


    </style>
@endsection

@section('content')



        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-start mb-3">
                            <h4 class="page-title mb-3">Customer Details</h4>

                            <!-- File input -->
                            <label style="color: red">Upload Excel</label>
                            <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="form-control mb-2 w-50">

                            <!-- Upload button, aligned below the file input -->
                            <input type="button" onclick="upload_excel()" class="btn btn-success mt-2" value="Upload">
                        </div>


                        <!-- DataTable -->
                        <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead  class="bg-purple">
                            <tr>
                                <th>Center</th>
                                <th>Group Name</th>
                                <th>Customer No</th>
                                <th>Customer Name</th>
                                <th>Nic</th>
                                <th>Address</th>
                                <th>Contact Number</th>
                                <th>Points</th>
                                <th>Current Loan Count</th>
                                <th>Settled Loan Count</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Blacklist</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->center_name ?? '-' }}</td>
                                    <td>{{ $customer->group_name ?? '-' }}</td>
                                    <td>{{ $customer->cus_number }}</td>
                                    <td>{{ $customer->First_Name }} {{ $customer->Last_Name }}</td>
                                    <td>{{ $customer->Nic }}</td>
                                    <td>{{ $customer->Address }},{{ $customer->Address_02 }},{{ $customer->Address_03 }}</td>
                                    <td>{{ $customer->Contact_No }}</td>
                                    <td>{{ number_format($customer->points,2,'.',',') }}</td>
                                    <td>{{$customer->current_loans}}</td>
                                    <td>{{$customer->settled_loans}}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-light" onclick="openMap('{{ $customer->Latitude }}', '{{ $customer->Longitude }}')">
                                            <i class="bi bi-map fs-4"></i>
                                        </button>
                                    </td>

                                    @if($customer->Status == "1")
                                        <td class="text-center"><span class="badge bg-primary">Active</span></td>
                                        <td><button class="btn btn-warning" onclick="change_status({{$customer->idCustomer}})">Move To Blacklist</button></td>
                                    @else
                                        <td class="text-center"><span class="badge bg-danger">Blacklisted</span></td>
                                        <td><button class="btn btn-warning" disabled>Move To Blacklist</button></td>
                                    @endif
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#view-modal" onclick="load_document({{$customer->idCustomer}});">
                                                <i class="bi bi-envelope-check fs-4"></i>
                                            </button>
                                            <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal"
                                                    data-first-name="{{$customer->First_Name}}" data-last-name="{{$customer->Last_Name}}" data-title="{{$customer->Title}}" data-civil="{{$customer->civil_status}}"
                                                    data-email="{{$customer->Email}}" data-contact-no="{{$customer->Contact_No}}" data-nic="{{$customer->Nic}}"
                                                    data-gender="{{$customer->Gender}}" data-dob="{{$customer->Dob}}" data-address="{{$customer->Address}}" data-address_2="{{$customer->Address_02}}" data-address_3="{{$customer->Address_03}}" data-city="{{$customer->City}}"
                                                    data-Per_Address_01="{{$customer->Per_Address_01}}" data-Per_Address_02="{{$customer->Per_Address_02}}" data-Per_Address_03="{{$customer->Per_Address_03}}" data-State="{{$customer->State}}" data-landline="{{$customer->Landline}}" data-gua_title="{{$customer->Gua_title}}" data-gua_name="{{$customer->Gua_name}}"
                                                    data-guardian_gender="{{$customer->Guardian_gender}}" data-gua_relation="{{$customer->Gua_relation}}" data-gua_occu="{{$customer->Gua_occu}}" data-gua_contact="{{$customer->Gua_contact}}"
                                                    data-gua_address="{{$customer->Gua_address}}" data-cus_number="{{$customer->cus_number}}"
                                                    data-occu_job_position="{{$customer->occu_job_position}}" data-occu_monthly_salary="{{$customer->occu_monthly_salary}}" data-occu_address_01="{{$customer->occu_address_01}}" data-occu_address_02="{{$customer->occu_address_02}}"
                                                    data-occu_contact_no="{{$customer->occu_contact_no}}" data-occu_longitude="{{$customer->occu_longitude}}" data-occu_latitude="{{$customer->occu_latitude}}" data-occu_address_03="{{$customer->occu_address_03}}"
                                                    data-note="{{ $customer->Note !== null ? $customer->Note : '-' }}" data-risk-level="{{$customer->Customer_Risk_Level}}" data-gua_nic="{{$customer->Gua_nic}}"
                                                    data-customer-id="{{$customer->idCustomer}}" data-longitude="{{$customer->Longitude}}" data-latitude="{{$customer->Latitude}}" data-root="{{$customer->route_id}}"
                                            >
                                                <i class="bi bi-pencil fs-4"></i></button>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#standard-modal_2" onclick="set_cus({{$customer->idCustomer}})">
                                                <i class="bi bi-envelope-paper fs-4"></i>
                                            </button>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#bank-modal" onclick="setbankid({{$customer->idCustomer}})">
                                                <i class="bi bi-bank2 fs-4"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="deleteCustomer({{$customer->idCustomer}})">
                                                <i class="bi bi-trash fs-4"></i>
                                            </button>
                                            @if (!empty($customer->Cus_phto))
                                                <a href="{{ Storage::url($customer->Cus_phto) }}" target="_blank" class="btn btn-dark">
                                                    <i class="bi bi-people fs-4"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-dark" disabled>
                                                    <i class="bi bi-people fs-4"></i>
                                                </button>
                                            @endif

                                            <a href="/customer_road_map/{{$customer->idCustomer}}" target="_blank" class="btn btn-primary"><i class="bi bi-bar-chart-steps fs-4"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div> <!-- end row -->

        <div class="modal fade" id="bank-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <h4>Bank Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_name" class="form-label">Bank Name</label>
                                        <input type="text" id="bank_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="account_name" class="form-label">Account Name</label>
                                        <input type="text" id="account_name" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="text" id="account_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="branch" class="form-label">Branch</label>
                                        <input type="text" id="branch" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success" id="addBankBtn" onclick="save_bank_details()">Add Bank Account</button>
                        </div>
                        <div class="table-responsive-sm">
                            <div class="table-responsive-sm">
                                <table class="table table-centered mb-0" id="bank_table">
                                    <thead>
                                    <tr>
                                        <th>Bank Name</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                        <th>Branch</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>
        </div>

    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Customer Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive-sm">
                        <table class="table table-centered mb-0" id="document_table">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
    </div>

    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Edit Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="cus_id">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Customer Number <span class="required-asterisk">*</span></label>
                                <input type="text" id="cus_number" name="cus_number" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="example-select" class="form-label">Root</label>
                                <select class="form-select" id="root" name="root">
                                    @foreach($route as $item)
                                        <option value="{{$item->id_route}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="example-select" class="form-label">Title</label>
                                <select class="form-select" id="title" name="title">
                                    <option>Mr</option>
                                    <option>Mrs</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="example-select" class="form-label">Civil Status</label>
                                <select class="form-select" id="civil_status" name="civil_status">
                                    <option>Married</option>
                                    <option>Single</option>
                                    <option>Seperated</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">First Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="f_name" name="f_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Middle/Last Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="last_name" name="last_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Mobile No <span class="required-asterisk">*</span></label>
                                <input type="tel" id="contact_number" name="contact_number" class="form-control" onkeypress="validateContactNumber(event)">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">NIC <span class="required-asterisk">*</span></label>
                                <input type="text" id="nic" name="nic" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="-">-</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date Of Birth</label>
                                <input type="text" id="dob" name="dob" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="curr_address_01" class="form-label">Current Address</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="curr_address_01" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="curr_address_02" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="curr_address_03" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="per_address_01" class="form-label">Permanent Address</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" id="per_address_01" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="per_address_02" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="per_address_03" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">Province / State</label>
                                <input type="text" id="state" name="state" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="landline" class="form-label">Landline Phone</label>
                                <input type="tel" id="landline" name="landline" class="form-control" onkeypress="validateContactNumber(event)">
                            </div>

                            <div class="row">
                                <label for="simpleinput" class="form-label">Note</label>
                                <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a comment here" id="note" name="note"
                                      style="height: 100px"></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="simpleinput" class="form-label">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="simpleinput" class="form-label">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control">
                                        <br>
                                        <input type="button" class="btn btn-danger" onclick="getlocation();"
                                               value="Get Location">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Occupation Details</label>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="occu_job_position" class="form-label">Job Position</label>
                                                    <input type="text" id="occu_job_position" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="occu_monthly_salary" class="form-label">Monthly Salary</label>
                                                    <input type="text" id="occu_monthly_salary" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="occu_address_01" class="form-label">Working Place Address</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_01" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_02" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_03" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="occu_contact_no" class="form-label">Working Place Contact Number</label>
                                                    <input type="text" id="occu_contact_no" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                    <label for="occu_longitude" class="form-label">Longitude</label>
                                                    <input type="text" id="occu_longitude" name="occu_longitude" class="form-control">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label for="occu_latitude" class="form-label">Latitude</label>
                                                    <input type="text" id="occu_latitude" name="occu_latitude" class="form-control">
                                                    <br>
                                                    <input type="button" class="btn btn-danger" onclick="getlocation_occu();" value="Get Location">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Guardian Details</label>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="example-select" class="form-label">Guardian Title</label>
                                                        <select class="form-select" id="gua_title" name="gua_title">
                                                            <option>Mr</option>
                                                            <option>Mrs</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Guardian Name</label>
                                                        <input type="text" id="gua_name" name="gua_name" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Gender</label>
                                                        <select class="form-control" id="guardian_gender" name="guardian_gender">
                                                            <option value="-">-</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">NIC</label>
                                                        <input type="text" id="gua_nic" name="gua_nic" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Relation to borrower</label>
                                                        <input type="text" id="gua_relation" name="gua_relation" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Occupation</label>
                                                        <input type="text" id="gua_occu" name="gua_occu" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Contact No</label>
                                                        <input type="text" id="gua_contact" name="gua_contact" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Address</label>
                                                        <input type="text" id="gua_address" name="gua_address" class="form-control">
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>


                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="update_cus()">Update changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <div class="modal fade" id="standard-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Customer Documents</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="customer">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Description</label>
                            <input type="text" id="description" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Document</label>
                            <input type="file" id="file" class="form-control">
                            <button type="button" onclick="openGlobalCamera('#file')" class="btn btn-outline-secondary mt-1">📷</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="saveDocument()"><i
                                    class="bi bi-upload"></i>&nbsp;&nbsp;
                                Upload</button>
                        </div>
                    </div>
                </div>



            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

@endsection

@section('script')
    <script src="../assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="../assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="../assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
{{--    <script src="../JS/customer.js"></script>--}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {


            // Initialize Select2 Elements
            $('.select2').select2();

            // Replace special characters in the company name
            var companyName = {!! json_encode($company->company_name) !!}.replace(/&/g, ' And ');


            // Initialize DataTable
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        filename: companyName // Use the modified company name
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        filename: companyName // Use the modified company name
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        filename: companyName // Use the modified company name
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        filename: companyName // Use the modified company name
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        filename: companyName // Use the modified company name
                    }
                ]
            });
            $(document).on('click', '.edit-btn', function() {
                // Use `this` to reference the specific button that was clicked
                const editButton = $(this);

                // Retrieve the data attributes from the clicked button
                const firstName = editButton.data('first-name');
                const lastName = editButton.data('last-name');
                const cusNumber = editButton.data('cus_number');
                const title = editButton.data('title');
                const email = editButton.data('email');
                const contactNo = editButton.data('contact-no');
                const nic = editButton.data('nic');
                const gender = editButton.data('gender');
                const dob = editButton.data('dob');
                const address = editButton.data('address');
                const address2 = editButton.data('address_2');
                const address3 = editButton.data('address_3');
                const perAddress1 = editButton.data('per_address_01');
                const perAddress2 = editButton.data('per_address_02');
                const perAddress3 = editButton.data('per_address_03');
                const city = editButton.data('city');
                const state = editButton.data('state');
                const landline = editButton.data('landline');
                const guaTitle = editButton.data('gua_title');
                const guaName = editButton.data('gua_name');
                const guardianGender = editButton.data('guardian_gender');
                const guaRelation = editButton.data('gua_relation');
                const guaOccu = editButton.data('gua_occu');
                const guaContact = editButton.data('gua_contact');
                const guaAddress = editButton.data('gua_address');
                const cusId = editButton.data('customer-id');
                const guaNic = editButton.data('gua_nic');
                const civil = editButton.data('civil');
                const note = editButton.data('note');
                const longitude = editButton.data('longitude');
                const latitude = editButton.data('latitude');
                const occuJobPosition = editButton.data('occu_job_position');
                const occuMonthlySalary = editButton.data('occu_monthly_salary');
                const occuAddress1 = editButton.data('occu_address_01');
                const occuAddress2 = editButton.data('occu_address_02');
                const occuAddress3 = editButton.data('occu_address_03');
                const occuContactNo = editButton.data('occu_contact_no');
                const occuLongitude = editButton.data('occu_longitude');
                const occuLatitude = editButton.data('occu_latitude');
                const root = editButton.data('root');

                // Set the values of the input fields in your modal or form
                $('#title').val(title);
                $('#root').val(root);
                $('#cus_number').val(cusNumber);
                $('#f_name').val(firstName);
                $('#last_name').val(lastName);
                $('#email').val(email);
                $('#contact_number').val(contactNo);
                $('#nic').val(nic);
                $('#gender').val(gender);
                $('#dob').val(dob);
                $('#longitude').val(longitude);
                $('#latitude').val(latitude);
                $('#curr_address_01').val(address);
                $('#curr_address_02').val(address2);
                $('#curr_address_03').val(address3);
                $('#per_address_01').val(perAddress1);
                $('#per_address_02').val(perAddress2);
                $('#per_address_03').val(perAddress3);
                $('#city').val(city);
                $('#state').val(state);
                $('#landline').val(landline);
                $('#gua_title').val(guaTitle);
                $('#gua_name').val(guaName);
                $('#guardian_gender').val(guardianGender);
                $('#gua_relation').val(guaRelation);
                $('#gua_occu').val(guaOccu);
                $('#gua_contact').val(guaContact);
                $('#gua_address').val(guaAddress);
                $('#cus_id').val(cusId);
                $('#gua_nic').val(guaNic);
                $('#civil_status').val(civil);
                $('#note').val(note);
                $('#occu_job_position').val(occuJobPosition);
                $('#occu_monthly_salary').val(occuMonthlySalary);
                $('#occu_address_01').val(occuAddress1);
                $('#occu_address_02').val(occuAddress2);
                $('#occu_address_03').val(occuAddress3);
                $('#occu_contact_no').val(occuContactNo);
                $('#occu_longitude').val(occuLongitude);
                $('#occu_latitude').val(occuLatitude);

                // Open your modal or perform any other required actions
            });

        });

        // function upload_excel() {
        //     var fileInput = document.getElementById('uploadExcel');  // Get the file input element
        //     var file = fileInput.files[0];  // Get the selected file
        //
        //     if (file) {
        //         var reader = new FileReader();
        //         reader.onload = function(e) {
        //             var data = new Uint8Array(e.target.result);
        //             var workbook = XLSX.read(data, { type: 'array' });
        //
        //             // Assuming the first sheet in the Excel file
        //             var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        //
        //             // Convert sheet to JSON, starting from the 5th row (index 5 in zero-indexed array)
        //             var jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
        //
        //             // Start reading data from the 5th index (skip the first 5 rows)
        //             var dataFrom5thRow = jsonData.slice(5);
        //
        //             console.log(dataFrom5thRow);  // Debugging: see the data in console
        //
        //             // SweetAlert2 confirmation prompt
        //             Swal.fire({
        //                 title: 'Are you sure?',
        //                 text: "Do you want to upload the Excel data?",
        //                 icon: 'warning',
        //                 showCancelButton: true,
        //                 confirmButtonText: 'Yes, upload it!',
        //                 cancelButtonText: 'No, cancel!',
        //                 reverseButtons: true
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     // Send data to backend using AJAX
        //                     $.ajax({
        //                         // url: '/upload-excel-customer',  // Your route URL
        //                         url: '/upload-excel-guardian',  // Your route URL
        //                         type: 'POST',
        //                         data: {
        //                             excelData: dataFrom5thRow,  // Send the Excel data
        //                         },
        //                         headers: {
        //                             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //                         },
        //                         success: function(response) {
        //                             Swal.fire({
        //                                 position: "center",
        //                                 icon: "success",
        //                                 title: "Your Excel data has been uploaded.",
        //                             }).then(function () {
        //                                 window.location.reload();
        //                             });
        //                         },
        //                         error: function(xhr, status, error) {
        //                             Swal.fire(
        //                                 'Error!',
        //                                 'There was an issue uploading the file.',
        //                                 'error'
        //                             );
        //                             console.error(error);  // Handle errors
        //                         }
        //                     });
        //                 } else if (result.dismiss === Swal.DismissReason.cancel) {
        //                     Swal.fire(
        //                         'Cancelled',
        //                         'Your Excel data upload was cancelled.',
        //                         'error'
        //                     );
        //                 }
        //             });
        //         };
        //         reader.readAsArrayBuffer(file);
        //     }
        // }



        function getlocation_occu() {
            // Check if Geolocation is supported by the browser
            if ("geolocation" in navigator) {
                // Geolocation is supported
                // Use getCurrentPosition method to get the user's current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Log the latitude and longitude to the console (you can do further processing here)
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);

                    // If you want to do something with the latitude and longitude, you can call a function here
                    // For example, set the values of input fields:
                    $('#occu_latitude').val(latitude);
                    $('#occu_longitude').val(longitude);
                }, function(error) {
                    // Handle any errors that occur while getting the location
                    console.error("Error getting location:", error);
                });
            } else {
                // Geolocation is not supported by the browser
                console.error("Geolocation is not supported by this browser.");
            }
        }

    </script>
    <script>
        function openMap(latitude, longitude) {
            var url = `https://maps.google.com/maps?q=${latitude},${longitude}`;
            window.open(url, '_blank');
        }

        function load_document(id){
            $.ajax({
                type: "GET",
                url: "/customerdoc/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    var tbody = $('#document_table tbody');
                    tbody.empty(); // Clear existing rows

                    data.item.forEach(function(item) {
                        var idCustomer_Documents = item.idCustomer_Documents;
                        var description = item.Description;
                        var path = item.Path;

                        // Construct the table row
                        var row = `
            <tr>
                <td>${description}</td>
                <td>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success" onclick="openDocument('${path}')"><i class="bi bi-eye"></i></button>
                        <button type="button" class="btn btn-danger" onclick="delete_doc(${idCustomer_Documents})">
    <i class="bi bi-trash"></i> <!-- Replace bi-bucket with bi-trash for trash bucket icon -->
</button>
                    </div>
                </td>
            </tr>
        `;

                        // Append the row to the table body
                        tbody.append(row);
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }



        function openDocument(path) {
            path = "/storage/" + path;
            window.open(path, '_blank');
        }


        function delete_doc(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Document ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/cusdocument/delete/"+id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully deleted !",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                    });
                }
            });
        }


        function update_cus() {
            // Get data from modal fields
            var id = $('#cus_id').val();
            var title = $('#title').val();
            var f_name = $('#f_name').val();
            var cus_number = $('#cus_number').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var contact_number = $('#contact_number').val();
            var nic = $('#nic').val();
            var gender = $('#gender').val();
            var dob = $('#dob').val();
            var root = $('#root').val();

            var curr_address_01 = $('#curr_address_01').val();
            var curr_address_02 = $('#curr_address_02').val();
            var curr_address_03 = $('#curr_address_03').val();

            var per_address_01 = $('#per_address_01').val();
            var per_address_02 = $('#per_address_02').val();
            var per_address_03 = $('#per_address_03').val();

            var city = $('#city').val();
            var state = $('#state').val();
            var landline = $('#landline').val();
            var gua_title = $('#gua_title').val();
            var gua_name = $('#gua_name').val();
            var guardian_gender = $('#guardian_gender').val();
            var gua_relation = $('#gua_relation').val();
            var gua_occu = $('#gua_occu').val();
            var gua_contact = $('#gua_contact').val();
            var gua_address = $('#gua_address').val();
            var note = $('#note').val();
            var longitude = $('#longitude').val();
            var latitude = $('#latitude').val();
            var gua_nic = $('#gua_nic').val();


            var occu_job_position = $('#occu_job_position').val();
            var occu_monthly_salary = $('#occu_monthly_salary').val();
            var occu_address_01 = $('#occu_address_01').val();
            var occu_address_02 = $('#occu_address_02').val();
            var occu_address_03 = $('#occu_address_03').val();
            var occu_contact_no = $('#occu_contact_no').val();
            var occu_longitude = $('#occu_longitude').val();
            var occu_latitude = $('#occu_latitude').val();


            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this Customer?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request
                    $.ajax({
                        url: '/update-customer',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                            root: root,
                            title: title,
                            f_name: f_name,
                            last_name: last_name,
                            email: email,
                            contact_number: contact_number,
                            nic: nic,
                            cus_number: cus_number,
                            gender: gender,
                            dob: dob,
                            curr_address_01: curr_address_01,
                            curr_address_02: curr_address_02,
                            curr_address_03: curr_address_03,
                            per_address_01: per_address_01,
                            per_address_02: per_address_02,
                            per_address_03: per_address_03,
                            city: city,
                            state: state,
                            landline: landline,
                            gua_title: gua_title,
                            gua_name: gua_name,
                            guardian_gender: guardian_gender,
                            gua_relation: gua_relation,
                            gua_occu: gua_occu,
                            gua_contact: gua_contact,
                            gua_address: gua_address,
                            note: note,
                            longitude: longitude,
                            latitude: latitude,
                            gua_nic: gua_nic,
                            occu_job_position: occu_job_position,
                            occu_monthly_salary: occu_monthly_salary,
                            occu_address_01: occu_address_01,
                            occu_address_02: occu_address_02,
                            occu_address_03: occu_address_03,
                            occu_contact_no: occu_contact_no,
                            occu_longitude: occu_longitude,
                            occu_latitude: occu_latitude,
                        },
                        success: function(response) {
                            console.log(response);
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully updated!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });
                }
            });
        }


        function saveDocument() {
            var customer = $('#customer').val();
            var description = $('#description').val();
            var file = $('#file')[0].files[0]; // Get the first selected file

            var formData = new FormData();
            formData.append('customer', customer);
            formData.append('description', description);
            formData.append('file', file);

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save this Document ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Upload it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/savecustomerdocument',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully saved!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error('Error saving document:', error);
                        }
                    });

                }
            });
        }


        function set_cus(id){
            $('#customer').val(id);
        }

        function setbankid(id){
            $('#customer').val(id);
            load_bank(id);
        }

        function save_bank_details() {


            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save the Bank Details ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Save it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();

                    var id = $('#customer').val();



                    // Get the values from the input fields
                    var bankName = $('#bank_name').val();
                    var accountName = $('#account_name').val();
                    var accountNumber = $('#account_number').val();
                    var branch = $('#branch').val();

                    // Append the values to the FormData object
                    formData.append('bankName', bankName);
                    formData.append('accountName', accountName);
                    formData.append('accountNumber', accountNumber);
                    formData.append('branch', branch);
                    formData.append('id', id); // Append the general ID


                    // Send the form data via AJAX
                    $.ajax({
                        url: "/save-bank-details-single",
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });
                }
            });



        }


        function getlocation() {
            // Check if Geolocation is supported by the browser
            if ("geolocation" in navigator) {
                // Geolocation is supported
                // Use getCurrentPosition method to get the user's current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Log the latitude and longitude to the console (you can do further processing here)
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);

                    // If you want to do something with the latitude and longitude, you can call a function here
                    // For example, set the values of input fields:
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function(error) {
                    // Handle any errors that occur while getting the location
                    console.error("Error getting location:", error);
                });
            } else {
                // Geolocation is not supported by the browser
                console.error("Geolocation is not supported by this browser.");
            }
        }


        function load_bank(id) {
            $.ajax({
                type: "GET",
                url: "/customers/bank/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        // Clear the table body
                        $('#bank_table tbody').empty();

                        // Iterate over each item in the response and append it to the table
                        data.item.forEach(function(bankDetail) {
                            var newRow = `
                        <tr>
                            <td>${bankDetail.bank_name}</td>
                            <td>${bankDetail.account_name}</td>
                            <td>${bankDetail.account_number}</td>
                            <td>${bankDetail.branch}</td>
                            <td>
                                <button type="button" class="btn btn-danger remove-btn" onclick="removeBank(${bankDetail.id})">Remove</button>
                            </td>
                        </tr>
                    `;
                            $('#bank_table tbody').append(newRow);
                        });
                    } else {
                        Swal.fire("Error!", "Failed to load data!", "error");
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire("Error!", "Failed to load data!", "error");
                }
            });
        }

        // Function to remove bank detail row
        function removeBank(bankId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to remove the Bank Details ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Remove it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/customers/bank/remove/" + bankId,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                // Remove the row from the table
                                $('#bank_table tbody').find(`tr:has(button[onclick='removeBank(${bankId})'])`).remove();
                                Swal.fire("Success!", "Bank detail removed successfully!", "success");
                            } else {
                                Swal.fire("Error!", "Failed to remove bank detail!", "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire("Error!", "Failed to remove bank detail!", "error");
                        }
                    });
                }
            });

        }




        function change_status(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to change the Status?",
                icon: "warning",
                input: "text", // Adds an input field for the note
                inputPlaceholder: "Enter a note...",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Change it!",
                preConfirm: (note) => {
                    // Validation to ensure a note is entered
                    if (!note) {
                        Swal.showValidationMessage('Please enter a note!');
                        return false; // Prevents the SweetAlert from closing if the note is empty
                    }
                    return note; // Returns the note entered by the user
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let note = result.value; // The entered note is available in result.value
                    $.ajax({
                        type: "POST", // POST is more appropriate for sending data
                        url: "/customers/status",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            id:id,
                            note: note // Send the note to the server in the body
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: data.message,
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            Swal.fire("Error!", "Failed to save data!", "error");
                        }
                    });
                }
            });
        }



        function deleteCustomer(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Customer ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/customers/delete/"+id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: data.item,
                                    title: data.message,
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                    });
                }
            });
        }

        function getlocation() {
            // Check if Geolocation is supported by the browser
            if ("geolocation" in navigator) {
                // Geolocation is supported
                // Use getCurrentPosition method to get the user's current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Log the latitude and longitude to the console (you can do further processing here)
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);

                    // If you want to do something with the latitude and longitude, you can call a function here
                    // For example, set the values of input fields:
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function(error) {
                    // Handle any errors that occur while getting the location
                    console.error("Error getting location:", error);
                });
            } else {
                // Geolocation is not supported by the browser
                console.error("Geolocation is not supported by this browser.");
            }
        }

    </script>


    <script>
        async function upload_excel() {
            const fileInput = document.getElementById('uploadExcel');
            const file = fileInput.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = async function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                // Filter out only data rows
                const dataFrom5thRow = jsonData.filter((row, index) => index >= 5 && row[3]);

                console.log("Parsed Excel Data:", dataFrom5thRow);  // Debug

                const confirm = await Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to upload the Excel data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, upload it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                });

                if (!confirm.isConfirmed) {
                    Swal.fire('Cancelled', 'Your Excel data upload was cancelled.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait while we upload the data.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                try {
                    const response = await fetch('/upload-excel-customer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ excelData: dataFrom5thRow })
                    });

                    const result = await response.json();

                    Swal.fire({
                        icon: 'success',
                        title: 'Upload complete!',
                        text: result.message || 'Your Excel data has been uploaded.'
                    }).then(() => {
                        window.location.reload();
                    });

                } catch (error) {
                    console.error("Upload error:", error);
                    Swal.fire('Error!', 'There was an issue uploading the file.', 'error');
                }
            };

            reader.readAsArrayBuffer(file);
        }

    </script>

@endsection
