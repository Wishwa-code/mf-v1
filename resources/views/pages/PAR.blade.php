@extends('layout.admin')

@section('head')
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Portfolio at Risk monthly (PAR) Overview</h4>
                </div>
                <span style="color: #a19595">"This section provides an overview of the portfolio at risk, helping you track the health of your loan portfolio based on overdue amounts. It shows the percentage of principal at risk for loans overdue within 30, 60, and 90 days, as well as the total outstanding amount."</span>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('partview.create') }}" method="post">
                            @csrf
                            <div class="col-lg-3">
                                <div class="mb-2">
                                    <label for="center" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_details" name="center_details">
                                        <option value="0" {{ isset($center_details) && $center_details == 0 ? 'selected' : '' }}>All</option>
                                        @foreach($center as $item)
                                            <option value="{{ $item->idCenter }}" {{ isset($center_details) && $center_details == $item->idCenter ? 'selected' : '' }}>
                                                {{ $item->No }} - {{ $item->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-danger" value="Search">
                        </form>
                        <div class="row mb-3">

                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-header" style="color: black">PAR 30 Days</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ number_format($thirtypercentage, 2) }}%</h5>
                                        <hr>
                                        <h5 class="card-title">Rs. {{ number_format($thirtycapital, 2) }} Principal At Risk From {{$thirtycount}} Loans</h5>
                                        <h5 class="card-title">Overdue Amount Rs. {{ number_format($totalThirtyDaysOutstanding, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-header" style="color: black">PAR 60 Days</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ number_format($sixtypercentage, 2) }}%</h5>
                                        <hr>
                                        <h5 class="card-title">Rs. {{ number_format($sixtycapital, 2) }} Principal At Risk From {{$sixtycount}} Loans</h5>
                                        <h5 class="card-title">Overdue Amount Rs. {{ number_format($totalSixtyDaysOutstanding, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-header" style="color: black">PAR 90 Days</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ number_format($ninetypercentage, 2) }}%</h5>
                                        <hr>
                                        <h5 class="card-title">Rs. {{ number_format($ninetycapital, 2) }} Principal At Risk From {{$nintycount}} Loans</h5>
                                        <h5 class="card-title">Overdue Amount Rs. {{ number_format($totalNinetyDaysOutstanding, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-header" style="color: black">All Outstanding</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ number_format($allpercentage, 2) }}%</h5>
                                        <hr>
                                        <h5 class="card-title">Rs. {{ number_format($allcapital, 2) }} Principal At Risk From {{$allcount}} Loans</h5>
                                        <h5 class="card-title">Overdue Amount Rs. {{ number_format($totalAllDaysOutstanding, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-centered table-striped table-hover mb-0" id="loan_table">
                                <thead class="thead-dark">
                                <tr style="color: black">
                                    <th>Loan No</th>
                                    <th>Loan Amount</th>
                                    <th>Total Loan Amount</th>
                                    <th>30 Days Outstanding</th>
                                    <th>60 Days Outstanding</th>
                                    <th>90 Days Outstanding</th>
                                    <th>All Outstanding</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($loans as $loan)
                                    @if($loan->alldays_outstanding>0)
                                        <tr>
                                            <td>{{ $loan->Loan_No }}</td>
                                            <td>{{ number_format($loan->Amount, 2) }}</td>
                                            <td>{{ number_format($loan->Total_Loan_Amount, 2) }}</td>
                                            <td>{{ number_format($loan->thirtydays_outstanding, 2) }}</td>
                                            <td>{{ number_format($loan->sixtydays_outstanding, 2) }}</td>
                                            <td>{{ number_format($loan->ninetydays_outstanding, 2) }}</td>
                                            <td>{{ number_format($loan->alldays_outstanding, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div> <!-- end container -->

@endsection

@section('script')
    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#loan_table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>
@endsection
