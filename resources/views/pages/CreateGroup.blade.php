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
                            <h4 class="page-title">Group Details</h4>
                        </div>

                        <div class="mb-3">
                            <label for="center" class="form-label">Select Center<span class="required-asterisk">*</span></label>
                            <select class="form-control select2" id="center_details">
                                @foreach($center as $item)
                                    <option value="{{$item->idCenter}}">{{ $item->Name }}-{{$item->Contact_no}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="group_number" class="form-label">Group Number<span class="required-asterisk">*</span></label>
                            <input type="text" id="group_number" class="form-control">
                        </div>
                        <div class="mb-3" hidden>
                            <label for="group_name" class="form-label">Group Name<span class="required-asterisk">*</span></label>
                            <input type="text" id="group_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="leader" class="form-label">Leader Name<span class="required-asterisk">*</span></label>
                            <input type="text" id="leader" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number<span class="required-asterisk">*</span></label>
                            <input type="text" id="contact" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="saveGroup(event)">
                                <i class="bi bi-save"></i>&nbsp;&nbsp;Save Group
                            </button>
                        </div>


                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>





    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

@endsection
