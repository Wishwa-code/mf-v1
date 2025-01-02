@extends('layout.admin')

@section('head')

@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">

                    </div>
                    <h4 class="page-title">Pending Loans</h4>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">

                                <div class="alert alert-purple border-0 text-center mt-3 mb-1" style="background-color: #192d50; color: white;">
                                    <h5  style="margin-top: -3px;margin-bottom: -3px;"> Selected Loan Details</h5>
                                </div>



                                <div class="row">
                                    <div class="col-lg-8">

                                        <div class="row mt-4 mb-5">
                                            <div class="col-sm-3 col-md-6">
                                                <table class='table table-bordered'
                                                       style="border-color:#CBC9C8; width: 100%;">
                                                    <tr class='style-tr'>
                                                        <td class="col-md-5 fw-bold">
                                                            Loan ID
                                                        </td>
                                                        <td>
                                                            {{ $loan->Loan_No }}
                                                        </td>
                                                    </tr>
                                                    <tr class='style-tr'>
                                                        <td class="col-md-5 fw-bold">
                                                            Category
                                                        </td>
                                                        <td>
                                                            {{ $loan->Name }}
                                                        </td>
                                                    </tr>
                                                    <tr class='style-tr'>
                                                        <td class="col-md-5 fw-bold">
                                                            Loan Type
                                                        </td>
                                                        <td>
                                                            {{ $loan->Type }}
                                                        </td>
                                                    </tr>
                                                    <tr class='style-tr'>
                                                        <td class="col-md-5 fw-bold">
                                                            Collection Type
                                                        </td>
                                                        <td>
                                                            {{ $loan->Collection_Type }}
                                                        </td>
                                                    </tr>
                                                </table>

                                            </div>

                                        </div>

                                    </div> <!-- end col -->
                                    <div class="col-lg-4">

                                        <div class="row mb-3 mt-4">
                                            <table class='table table-bordered' style="border-color:#CBC9C8; width: 100%;">
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">
                                                        Customer
                                                    </td>
                                                    <td>
                                                        {{ $customers->Full_Name }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Contact No
                                                    </td>
                                                    <td>
                                                        {{ $customers->Contact_No }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Risk Level
                                                    </td>
                                                    <td>
                                                        {{ $customers->Customer_Risk_Level }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Note
                                                    </td>
                                                    <td>
                                                        {{ $customers->Note !== null ? $customers->Note : '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div> <!-- end col -->
                                </div>
                                <button type="button" class="btn btn-primary" style="float: right" data-bs-toggle="modal" data-bs-target="#issue-loan-modal" onclick="payment_model()">Payment</button>
                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Installment Date</th>
                                            <th>Installment Amount</th>
                                            <th>Panalty Amount</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Panalty Balance </th>
                                            <th>Installment Balance</th>
                                            <th>Total Balance</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($installments as $item)
                                            <tr>
                                                <td>{{$item->No}}</td>
                                                <td class="text-end">{{$item->Installment_Date}}</td>
                                                <td class="text-end">{{$item->Installment_Amount}}</td>
                                                <td class="text-end">{{ number_format($item->Panalty_Amount, 2, '.', '') }}</td>
                                                <td class="text-end">{{$item->Total_Amount}}</td>
                                                <td class="text-end">{{$item->Paid_Amount}}</td>
                                                <td class="text-end">{{$item->Panalty_Balance}}</td>
                                                <td class="text-end">{{$item->Installment_Balance}}</td>
                                                <td class="text-end">{{$item->Total_Balance}}</td>
                                                @if ($item->Status === '0')
                                                    @php
                                                        $currentTime = now()->timezone('Asia/Colombo');
                                                        $installmentDate = \Carbon\Carbon::parse($item->Installment_Date, 'Asia/Colombo');
                                                    @endphp
                                                    @if ($item->Total_Balance > 0 && $installmentDate->lessThan($currentTime))
                                                        <td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td>
                                                    @else
                                                        <td class="text-center"><span class="px-1" style="background-color: #ffdd00;border-radius: 10px; color: #ffdd00;">-</span></td>
                                                    @endif
                                                @else
                                                    <td class="text-center"><span class="px-1" style="background-color: #1bff00;border-radius: 10px; color: #00ff27;">-</span></td>
                                                @endif
                                                <td>
                                                    <div>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button type="button" class="btn btn-success" onclick="load_installment_log_model({{$item->idInstallments}})"  data-bs-toggle="modal" data-bs-target="#standard-modal"><i class="bi bi-eye"></i> </button>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->

                                <div class="row mt-1 mb-1 p-2">

                                    <div class="col-md-10 ml-auto float-left" >
                                        <button type="button" class="btn btn-success">Loan Book</button>
                                    </div>

{{--                                    <div class="col-md-2 pl-3 float-right" >--}}
{{--                                        <button type="button" style="margin-left: 10px !important;" class="btn btn-success">Report View</button>--}}
{{--                                    </div>--}}

                                </div>

                            </div> <!-- end card-->
                        </div> <!-- end col -->


                    </div>
                </div>

            </div>
            <!-- end page title -->


            <!-- end row -->




        </div> <!-- container -->

        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header pt-2">
                        <h4>Installment Log</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="ins_table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Penalty Total</th>
                                    <th>Installment Balance</th>
                                    <th>Total Balance</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 pt-3">

                            <div class="col-md-7 pl-3">
                                <div class="row mt-1 mb-5">
                                    <div class="col-sm-5">
                                        <div>
                                            <span class="fw-bold">Penalty Total(LKR)</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold">Installment Balance(LKR)</span>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <span id="panelty_tot">0.00</span>
                                        </div>
                                        <div>
                                            <span id="ins_tot">0.00</span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 pl-3 float-right">
                                <div class="row mt-1 mb-5">
                                    <div class="col-sm-10">
                                        <div>
                                            <span class="fw-bold">Total Balance(LKR)</span>
                                        </div>


                                    </div>
                                    <div class="col-lg-2">
                                        <div>
                                            <span id="tot_balance">0.00</span>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <div class="modal fade" id="issue-loan-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <!-- <h4>Issue Loan</h4> -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div >

                            <div>
                                <div class="row mt-1 mb-2">
                                    <div class="col-sm-6">

                                        <div>
                                            <span class="fw-bold">Paid Amount(LKR)</span>
                                        </div>

                                    </div>
                                    <div class="col-lg-5">

                                        <input type="hidden" id="loan_id" value="{{$loan->idCustomer_Loan}}" class="form-control">
                                        <input type="hidden" id="ins_id"  class="form-control">
                                        <div>
                                            <input type="text" id="payment_amount" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" onclick="payment()">Pay</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>


            <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!-- <h4 class="modal-title" >gwegerg</h4> -->
                            <h4>Delete Loan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="mb-3 px-3">
                                    <label for="simpleinput" class="form-label">Reason</label>
                                    <input type="text" id="simpleinput" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger">Delete Loan</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
    </div>
@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script src="../JS/payment.js"></script>
    <script>
        $(function() {
            let x = ["#payment_amount"];
            decimalFormat(x);

            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })
    </script>
{{--    <script>--}}
{{--        const exampleModal = document.getElementById('standard-modal')--}}
{{--        exampleModal.addEventListener('show.bs.modal', event => {--}}
{{--            // Button that triggered the modal--}}
{{--            const button = event.relatedTarget--}}
{{--            // Extract info from data-bs-* attributes--}}
{{--            const recipient = button.getAttribute('data-bs-whatever')--}}
{{--            // If necessary, you could initiate an AJAX request here--}}
{{--            // and then do the updating in a callback.--}}
{{--            //--}}
{{--            // Update the modal's content.--}}
{{--            const modalTitle = exampleModal.querySelector('.modal-title')--}}
{{--            const modalBodyInput = exampleModal.querySelector('.modal-body input')--}}

{{--            modalTitle.textContent = `New message to ${recipient}`--}}
{{--            modalBodyInput.value = recipient--}}
{{--        })--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        const issueLoanModal = document.getElementById('issue-loan-modal')--}}
{{--        issueLoanModal.addEventListener('show.bs.modal', event => {--}}
{{--            // Button that triggered the modal--}}
{{--            const button = event.relatedTarget--}}
{{--            // Extract info from data-bs-* attributes--}}
{{--            const recipient = button.getAttribute('data-bs-whatever')--}}
{{--            // If necessary, you could initiate an AJAX request here--}}
{{--            // and then do the updating in a callback.--}}
{{--            //--}}
{{--            // Update the modal's content.--}}
{{--            const modalTitle = issueLoanModal.querySelector('.modal-title')--}}
{{--            const modalBodyInput = issueLoanModal.querySelector('.modal-body input')--}}

{{--            modalTitle.textContent = `New message to ${recipient}`--}}
{{--            modalBodyInput.value = recipient--}}
{{--        })--}}
{{--    </script>--}}

{{--    <script>--}}
{{--        const viewModal = document.getElementById('view-modal')--}}
{{--        exampleModal.addEventListener('show.bs.modal', event => {--}}
{{--            // Button that triggered the modal--}}
{{--            const button = event.relatedTarget--}}
{{--            // Extract info from data-bs-* attributes--}}
{{--            const recipient = button.getAttribute('data-bs-whatever')--}}
{{--            // If necessary, you could initiate an AJAX request here--}}
{{--            // and then do the updating in a callback.--}}
{{--            //--}}
{{--            // Update the modal's content.--}}
{{--            const modalTitle = viewModal.querySelector('.modal-title')--}}
{{--            const modalBodyInput = viewModal.querySelector('.modal-body input')--}}

{{--            modalTitle.textContent = `New message to ${recipient}`--}}
{{--            modalBodyInput.value = recipient--}}
{{--        })--}}
{{--    </script>--}}

@endsection

