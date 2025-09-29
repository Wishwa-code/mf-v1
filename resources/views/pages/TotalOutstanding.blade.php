@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        thead {
            background-color: #d9edf7;
            color: #31708f;
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-card {
            background: linear-gradient(135deg, #01503c, #2d8a73);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Total Outstanding</h4>
                    <p class="mb-0 text-muted">Active loans with outstanding amounts (Capital Balance + Interest Balance)</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="outstanding_table">
                                <thead class="sticky-top bg-purple">
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th class="text-right">Loan Amount (Capital)</th>
                                        <th class="text-right">Full Loan Amount (Capital + Interest)</th>
                                        <th class="text-right">Total Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($totalOutstandingData as $item)
                                        <tr>
                                            <td>{{ $item->loan_id }}</td>
                                            <td>{{ $item->customer_id }}</td>
                                            <td>{{ $item->customer_name }}</td>
                                            <td class="text-right">{{ number_format($item->capital_amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($item->full_loan_amount, 2) }}</td>
                                            <td class="text-right"><strong>{{ number_format($item->total_outstanding, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#outstanding_table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: 'Export PDF',
                        className: 'btn btn-danger'
                    }
                ],
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[5, 'desc']], // Sort by Total Outstanding column desc
                columnDefs: [
                    { className: "text-right", targets: [3, 4, 5] }
                ]
            });
        });
    </script>
@endsection
