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
                        <form action="{{route('center.store')}}" method="post">

                        </form>
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Center Details</h4>
                        </div>
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

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="validateSubmitCanter(event)"><i
                                    class="bi bi-save"></i>&nbsp;&nbsp;Save Center</button>
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
    <script src="../JS/center.js?n=2"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

@endsection
