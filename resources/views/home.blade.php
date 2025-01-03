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
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                            <li class="breadcrumb-item active">Welcome!</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Welcome!</h4>
                    <form action="{{route('loan_settlement.capitalbalance')}}" method="post">
                        @csrf
                        <input type="submit" value="test capital">
                    </form>
                    <br>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if($dashboard==1)
            <div class="row">
                <div class="col-xxl-3 col-sm-6">
                    <a href="/pendingloan">
                        <div class="card widget-flat text-bg-pink">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-eye-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Pending Loans ({{$customer_loan_pending_Count}})</h6>
                                <h3 class="my-2">Rs.{{number_format($customer_loan_pending_Amount,2,'.',',')}}</h3>
                            </div>
                        </div>
                    </a>

                </div> <!-- end col-->

                <div class="col-xxl-3 col-sm-6">
                    <a href="/payment_step_1">
                        <div class="card widget-flat text-bg-purple">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-wallet-2-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Current Loans ({{$customer_loan_current_Count}})</h6>
                                <h3 class="my-2">Rs.{{number_format($customer_loan_current_Amount,2,'.',',')}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->

                <div class="col-xxl-3 col-sm-6">
                    <a href="/showsettleloan">
                        <div class="card widget-flat text-bg-danger">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-file-paper-2-fill widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Settled Loans ({{$setteled_loan_Count}})</h6>
                                <h3 class="my-2">Rs.{{number_format($setteled_loan_current_Amount,2,'.',',')}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->


                <div class="col-xxl-3 col-sm-6">
                    <a href="/showcustomers">
                        <div class="card widget-flat text-bg-primary">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-group-2-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Customer Count</h6>
                                <h3 class="my-2">{{$customerCount}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->

                <hr>

                <div class="col-xxl-3 col-sm-6">
                    <a href="/payment">
                        <div class="card widget-flat text-bg-info">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-shopping-basket-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Today Collection</h6>
                                <h3 class="my-2">LKR {{number_format($todayinstallment,'2','.',',')}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->

                <div class="col-xxl-3 col-sm-6">
                    <a href="/payment">
                        <div class="card widget-flat text-bg-info">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-shopping-basket-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Total Arease</h6>
                                <h3 class="my-2">LKR {{number_format($arrease,'2','.',',')}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->

                <div class="col-xxl-3 col-sm-6">
                    <a href="/payment">
                        <div class="card widget-flat text-bg-info">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ri-shopping-basket-line widget-icon"></i>
                                </div>
                                <h6 class="text-uppercase mt-0" title="Customers">Total Outstanding</h6>
                                <h3 class="my-2">LKR {{number_format($totalBalanceUntil,'2','.',',')}}</h3>

                            </div>
                        </div>
                    </a>
                </div> <!-- end col-->

            </div>

            @if($shortcut_count>0)
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Shortcuts</h4>
                        </div>
                    </div>
                </div>
            @endif



            <div class="row">
                @foreach($shortcut as $item)
                        <?php $url="/"; $name="";?>
                    @if($item->name==="Add_Customer")
                            <?php $url="/customers"; $name="Add Customer"; ?>
                    @elseif($item->name==="View_Customer")
                            <?php $url="/showcustomers"; $name="View Customer"; ?>
                    @elseif($item->name==="Assign_Customers_to_group")
                            <?php $url="/customergroupassign"; $name="Add Customers To Group"; ?>
                    @elseif($item->name==="View_Products")
                            <?php $url="/viewproduct"; $name="View Product"; ?>
                    @elseif($item->name==="Pending_Loans")
                            <?php $url="/pendingloan"; $name="Pending Loans"; ?>
                    @elseif($item->name==="Current_Loans")
                            <?php $url="/payment_step_1"; $name="Current Loans"; ?>
                    @elseif($item->name==="Loan_In_arrears")
                            <?php $url="/latePayment"; $name="Loan In Areas"; ?>
                    @elseif($item->name==="Add_Repayment")
                            <?php $url="/payment"; $name="Add Repayment"; ?>
                    @elseif($item->name==="Repayment_details")
                            <?php $url="/viewpayment"; $name="View Repayment"; ?>
                    @elseif($item->name==="Collector_wise_collections")
                            <?php $url="/collection"; $name="Agent Collection"; ?>
                    @elseif($item->name==="Loan_Calculator")
                            <?php $url="/calculator"; $name="Loan Calculator"; ?>
                    @elseif($item->name==="Add_Expenses")
                            <?php $url="/expenses"; $name="Add Expenses"; ?>
                    @elseif($item->name==="Add_Income")
                            <?php $url="/income"; $name="Add Income"; ?>
                    @endif
                    <div class="col-xxl-3 col-sm-6">
                        <a href="<?php echo $url ?>">
                            <div class="card widget-flat text-bg-purple">
                                <div class="card-body">

                                    <h6 class="text-uppercase mt-0" title="Customers"><?php echo $name ?></h6>
                                </div>
                            </div>
                        </a>

                    </div>
                @endforeach
            </div>




{{--            <div class="row">--}}
{{--                <div class="col-lg-8">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="card-widgets">--}}
{{--                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>--}}
{{--                                <a data-bs-toggle="collapse" href="#weeklysales-collapse" role="button" aria-expanded="false" aria-controls="weeklysales-collapse"><i class="ri-subtract-line"></i></a>--}}
{{--                                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>--}}
{{--                            </div>--}}
{{--                            <h5 class="header-title mb-0">Weekly Sales Report</h5>--}}

{{--                            <div id="weeklysales-collapse" class="collapse pt-3 show">--}}
{{--                                <div dir="ltr">--}}
{{--                                    <div id="revenue-charts" class="apex-charts" data-colors="#3bc0c3,#1a2942,#d1d7d973"></div>--}}
{{--                                </div>--}}

{{--                                <div class="row text-center">--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3">Current Week</p>--}}
{{--                                        <h3 class=" mb-0">--}}
{{--                                            <span>LKR 506k</span>--}}
{{--                                        </h3>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3">Previous Week</p>--}}
{{--                                        <h3 class=" mb-0">--}}
{{--                                            <span>LKR 305k </span>--}}
{{--                                        </h3>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3">Conversation</p>--}}
{{--                                        <h3 class=" mb-0">--}}
{{--                                            <span>3.27%</span>--}}
{{--                                        </h3>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3">Customers</p>--}}
{{--                                        <h3 class=" mb-0">--}}
{{--                                            <span>3k</span>--}}
{{--                                        </h3>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div> <!-- end card-body-->--}}
{{--                    </div> <!-- end card-->--}}
{{--                </div>--}}
{{--                <!-- end col-->--}}
{{--                <div class="col-lg-4">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="card-widgets">--}}
{{--                                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>--}}
{{--                                <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button" aria-expanded="false" aria-controls="yearly-sales-collapse"><i class="ri-subtract-line"></i></a>--}}
{{--                                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>--}}
{{--                            </div>--}}
{{--                            <h5 class="header-title mb-0">Yearly Sales Report</h5>--}}

{{--                            <div id="yearly-sales-collapse" class="collapse pt-3 show">--}}
{{--                                <div dir="ltr">--}}
{{--                                    <div id="yearly-sales-charts" class="apex-charts" data-colors="#3bc0c3,#1a2942,#d1d7d973"></div>--}}
{{--                                </div>--}}
{{--                                <div class="row text-center">--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3 mb-2">Quarter 1</p>--}}
{{--                                        <h4 class="mb-0">LKR56.2k</h4>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3 mb-2">Quarter 2</p>--}}
{{--                                        <h4 class="mb-0">LKR42.5k</h4>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <p class="text-muted mt-3 mb-2">All Time</p>--}}
{{--                                        <h4 class="mb-0">LKR 102.03k</h4>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div> <!-- end card-body-->--}}
{{--                    </div> <!-- end card-->--}}


{{--                </div> <!-- end card-->--}}
{{--            </div> <!-- end col-->--}}
        @endif
    </div>

    </div>
@endsection

@section('script')
    <!-- Daterangepicker js -->
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>

    <!-- Apex Charts js -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>

    <!-- Vector Map js -->
    <script src="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="assets/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script>

    <!-- Dashboard App js -->
    <script src="assets/js/pages/dashboard.js"></script>


    <script>
        $(document).ready(function() {

            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    width: '100%',
                },
                series: [{
                    name: 'Current Week',
                    data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
                }, {
                    name: 'Previous Week',
                    data: [10, 20, 15, 30, 25, 35, 40, 50, 65]
                }],
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                },
                colors: ['#3bc0c3', '#1a2942']
            };

            var chart = new ApexCharts(document.querySelector("#revenue-charts"), options);
            chart.render();
        });

        var options = {
            chart: {
                type: 'area',
                height: 350,
                width: '100%',
            },
            series: [{
                name: 'Quarter 1',
                data: [56200, 42500] // Replace with your actual sales data for Quarter 1 and Quarter 2
            }, {
                name: 'Quarter 2',
                data: [42500, 65000] // Replace with your actual sales data for Quarter 2 and Quarter 3
            }],
            xaxis: {
                categories: ['Quarter 1', 'Quarter 2'] // Replace with your quarter labels
            },
            colors: ['#3bc0c3', '#1a2942']
        };

        var chart = new ApexCharts(document.querySelector("#yearly-sales-charts"), options);
        chart.render();


    </script>



@endsection

