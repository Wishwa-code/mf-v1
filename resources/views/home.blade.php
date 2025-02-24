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
{{--                    <form action="{{route('loan_settlement.capitalbalance')}}" method="post">--}}
{{--                        @csrf--}}
{{--                        <input type="submit" value="test capital">--}}
{{--                    </form>--}}
                    <br>
                </div>
            </div>
        </div>
        <!-- end page title -->

{{--        @if($dashboard==1)--}}
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
                                <h6 class="text-uppercase mt-0" title="Customers">Chq Payments</h6>
                                <h3 class="my-2">LKR {{number_format($checqueamount,'2','.',',')}}</h3>

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


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">Monthly Revenue</h4>
                        <div id="monthly-revenue-chart"></div>
                    </div>
                </div>
            </div>
        </div>




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


{{--    <script>--}}
{{--        $(document).ready(function() {--}}

{{--            var options = {--}}
{{--                chart: {--}}
{{--                    type: 'bar',--}}
{{--                    height: 350,--}}
{{--                    width: '100%',--}}
{{--                },--}}
{{--                series: [{--}}
{{--                    name: 'Current Week',--}}
{{--                    data: [30, 40, 35, 50, 49, 60, 70, 91, 125]--}}
{{--                }, {--}}
{{--                    name: 'Previous Week',--}}
{{--                    data: [10, 20, 15, 30, 25, 35, 40, 50, 65]--}}
{{--                }],--}}
{{--                xaxis: {--}}
{{--                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']--}}
{{--                },--}}
{{--                colors: ['#3bc0c3', '#1a2942']--}}
{{--            };--}}

{{--            var chart = new ApexCharts(document.querySelector("#revenue-charts"), options);--}}
{{--            chart.render();--}}
{{--        });--}}

{{--        var options = {--}}
{{--            chart: {--}}
{{--                type: 'area',--}}
{{--                height: 350,--}}
{{--                width: '100%',--}}
{{--            },--}}
{{--            series: [{--}}
{{--                name: 'Quarter 1',--}}
{{--                data: [56200, 42500] // Replace with your actual sales data for Quarter 1 and Quarter 2--}}
{{--            }, {--}}
{{--                name: 'Quarter 2',--}}
{{--                data: [42500, 65000] // Replace with your actual sales data for Quarter 2 and Quarter 3--}}
{{--            }],--}}
{{--            xaxis: {--}}
{{--                categories: ['Quarter 1', 'Quarter 2'] // Replace with your quarter labels--}}
{{--            },--}}
{{--            colors: ['#3bc0c3', '#1a2942']--}}
{{--        };--}}

{{--        var chart = new ApexCharts(document.querySelector("#yearly-sales-charts"), options);--}}
{{--        chart.render();--}}


{{--    </script>--}}

    <script>
        $(document).ready(function () {
            // Options for Monthly Revenue Chart
            var monthlyRevenueOptions = {
                chart: {
                    type: 'line',
                    height: 350,
                    width: '100%',
                },
                series: [
                    {
                        name: 'Revenue',
                        data: [30000, 40000, 35000, 50000, 60000, 75000, 85000, 90000, 100000, 95000, 110000, 120000], // Example data
                    },
                ],
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                    ], // Months of the year
                },
                colors: ['#3bc0c3'],
                stroke: {
                    curve: 'smooth',
                },
                title: {
                    text: 'Monthly Revenue',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold',
                    },
                },
            };

            // Render the Monthly Revenue Chart
            var monthlyRevenueChart = new ApexCharts(
                document.querySelector('#monthly-revenue-chart'),
                monthlyRevenueOptions
            );
            monthlyRevenueChart.render();
        });
    </script>




@endsection

