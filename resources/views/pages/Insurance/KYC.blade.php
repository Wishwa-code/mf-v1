@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .nav-tabs .nav-link {
            color: #555;
            font-weight: 500;
            border: none;
            padding: 12px 20px;
        }

        .nav-tabs .nav-link.active {
            background-color: #fff;
            border: none;
            border-bottom: 3px solid #0d6efd;
            color: #0d6efd;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        .tab-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .table th, .table td {
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="page-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="container-fluid">
                                <h2 class="mb-4"><i class="fas fa-id-card me-2 text-primary"></i>Know Your Customer</h2>

                                <ul class="nav nav-tabs" id="kycTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#basic" role="tab"><i
                                                    class="fas fa-user me-1"></i>Basic Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#guardian" role="tab"><i
                                                    class="fas fa-user-shield me-1"></i>Guardian Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab"><i
                                                    class="fas fa-file-alt me-1"></i>Documents</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#loans" role="tab"><i
                                                    class="fas fa-money-bill me-1"></i>Loans</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#loanSummary" role="tab"><i
                                                    class="fas fa-chart-bar me-1"></i>Guaranteed Loans</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#insurance" role="tab"><i
                                                    class="fas fa-shield-alt me-1"></i>Insurance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab"><i
                                                    class="fas fa-history me-1"></i>History</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="kycTabContent">
                                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                        @include('pages.Insurance.kyc.basic')
                                    </div>
                                    <div class="tab-pane fade" id="guardian" role="tabpanel">
                                        @include('pages.Insurance.kyc.guardian')
                                    </div>
                                    <div class="tab-pane fade" id="documents" role="tabpanel">
                                        @include('pages.Insurance.kyc.documents')
                                    </div>
                                    <div class="tab-pane fade" id="loans" role="tabpanel">
                                        @include('pages.Insurance.kyc.loans')
                                    </div>
                                    <div class="tab-pane fade" id="loanSummary" role="tabpanel">
                                        @include('pages.Insurance.kyc.guranteed_loan')
                                    </div>
                                    <div class="tab-pane fade" id="insurance" role="tabpanel">
                                        @include('pages.Insurance.kyc.insurance')
                                    </div>
                                    <div class="tab-pane fade" id="history" role="tabpanel">
                                        @include('pages.Insurance.kyc.history')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

