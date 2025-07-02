@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        #documentModalBody {
            max-height: 600px;
            overflow-y: auto;
        }
        #documentModalBody iframe,
        #documentModalBody img {
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        #documentModalBody > div {
            position: relative;
            z-index: 1;
        }

        a[target="_blank"]::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.01); /* just enough to capture hover */
        }
        a[target="_blank"]:hover::after {
            background: rgba(0, 0, 0, 0.05); /* subtle dark hover overlay */
        }

    </style>
    <style>
        .level-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .level-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .designation-item {
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
        }

        .designation-item:first-of-type {
            border-top: none;
        }
        .level-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .level-header {
            background-color: #f1f5f9;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 15px;
            border-bottom: 1px solid #e1e1e1;
        }

        .level-body {
            padding: 16px;
        }

        .level-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .level-badges .badge {
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .level-footer {
            text-align: right;
            padding: 12px 16px;
            border-top: 1px solid #eee;
            background-color: #fafafa;
        }

    </style>

@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title">Insurance Management</h4>
                <br>
                <div class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input type="date" id="start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <input type="date" id="end_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <select id="customer_id" class="form-select form-select-sm select2" style="min-width: 200px">
                                <option value=" ">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->idCustomer }}">{{ $customer->cus_number }}-{{ $customer->First_Name }} {{ $customer->Last_Name }}-{{ $customer->Contact_No }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select id="category_id" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_insurance_category }}">{{ $cat->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-primary" onclick="loadInsurances()">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>

                <br>
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="insuranceTabs">
                    <li class="nav-item"><a class="nav-link active" data-status="Pending" href="#">Pending</a></li>
                    <li class="nav-item"><a class="nav-link" data-status="Approved" href="#">Issued</a></li>
                    <li class="nav-item"><a class="nav-link" data-status="Rejected" href="#">Rejected</a></li>
                </ul>

                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered" id="insuranceTable">
                        <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Days</th>
                            <th>Total</th>
                            <th>Note</th>
                            <th>Approvals</th> <!-- 👈 New column -->
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="insuranceBody">
                        <!-- Data goes here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <!-- Document Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Insurance Documents</h5>

                </div>
                <div class="modal-body" id="documentModalBody">
                    <!-- Loaded content -->
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Levels Modal -->
    <div class="modal fade" id="approvalLevelsModal" tabindex="-1" role="dialog" aria-labelledby="approvalLevelsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document"> <!-- ✅ XL size + scroll -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approval Levels</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- if you're using Bootstrap 5 -->
                </div>
                <div class="modal-body" id="approvalLevelsBody">
                    <!-- Levels will be loaded here -->
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let currentStatus = "0";

        $(document).ready(function () {
            $('#customer_id').select2({
                placeholder: 'Select a customer',
                allowClear: true,
                width: 'resolve'
            });

            loadInsurances();
            const statusMap = {
                "Pending": 0,
                "Approved": 1,
                "Rejected": -1
            };

            $('#insuranceTabs a').click(function (e) {
                e.preventDefault();
                $('#insuranceTabs a').removeClass('active');
                $(this).addClass('active');

                const label = $(this).data('status'); // e.g., "Pending"
                currentStatus = statusMap[label]; // Set to 0, 1, or -1
                loadInsurances();
            });

        });

        function loadInsurances() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const customerId = $('#customer_id').val();
            const categoryId = $('#category_id').val();

            $.ajax({
                url: "{{ route('kyc.loadInsurances') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    status: currentStatus,
                    start_date: startDate,
                    end_date: endDate,
                    customer_id: customerId,
                    category_id: categoryId
                },
                success: function (res) {
                    let rows = '';
                    res.forEach(item => {
                        rows += `
<tr>
    <td>${item.First_Name} ${item.Last_Name}</td>
    <td>${item.category}</td>
    <td>${item.amount}</td>
    <td>${item.day_count}</td>
    <td>${item.total_amount}</td>
    <td>${item.note}</td>
    <td>${item.approved_count || 0} / ${item.total_count || 0}</td> <!-- ✅ -->
    <td>
        <button class="btn btn-sm btn-info view-file-btn" data-id="${item.id_insurance}" disabled>View File</button>
      <button class="btn btn-sm btn-secondary" onclick="viewApprovalLevels(${item.id_insurance_category}, ${item.id_insurance})">Approval Flow</button>
    </td>
</tr>`;

                    });
                    $('#insuranceBody').html(rows);
                    enableFileButtons();
                }
            });
        }

        function enableFileButtons() {
            $('.view-file-btn').each(function () {
                const button = $(this);
                const id = button.data('id');

                $.get(`/insurance/evidence/${id}`, function (data) {
                    if (Array.isArray(data) && data.length > 0) {
                        button.prop('disabled', false);
                        button.attr('onclick', `viewDocuments(${id})`);
                    }
                });
            });
        }

        function viewDocuments(id) {
            $.ajax({
                url: `/insurance/evidence/${id}`,
                method: 'GET',
                success: function (data) {
                    let html = '';

                    if (!data || data.length === 0) {
                        html = '<p class="text-center text-muted">No documents found.</p>';
                    } else {
                        data.forEach(doc => {
                            const ext = doc.name.split('.').pop().toLowerCase();
                            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext);
                            const isPDF = ext === 'pdf';

                            html += `<div class="mb-4 text-center border p-3 rounded">`;

                            if (isImage) {
                                html += `
        <div class="mb-2 position-relative" style="cursor: pointer;">
            <a href="${doc.url}" target="_blank">
                <img src="${doc.url}" class="img-fluid" style="max-height: 300px;">
            </a>
        </div>`;

                            } else if (isPDF) {
                                html += `
        <div class="mb-2 position-relative" style="cursor: pointer;">
            <a href="${doc.url}" target="_blank" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;"></a>
            <iframe src="${doc.url}" style="width: 100%; height: 400px; position: relative; z-index: 1;" frameborder="0"></iframe>
        </div>`;
                            }



                            else {
                                html += `
                            <a href="${doc.url}" target="_blank" class="btn btn-outline-primary">
                                Open File
                            </a>`;
                            }

                            html += `</div>`;
                        });
                    }

                    $('#documentModalBody').html(html);
                    $('#documentModal').modal('show');
                },
                error: function (xhr) {
                    console.error("Error loading documents:", xhr);
                    $('#documentModalBody').html('<p class="text-danger text-center">Error loading documents. Please try again.</p>');
                    $('#documentModal').modal('show');
                }
            });
        }


        function viewApprovalLevels(categoryId, insuranceId) {
            $('#approvalLevelsModal').data('category-id', categoryId);

            $.ajax({
                url: `/insurance/approval-levels/${categoryId}/${insuranceId}`,
                method: 'GET',
                success: function (data) {
                    let allApproved = true;
                    let html = '';
                    const userDesignation = '{{ session('designation') }}';

                    data.forEach((level) => {
                        const isLevelApproved = level.designations.every(d => d.status === 'Approved');
                        if (!isLevelApproved) {
                            allApproved = false;
                        }

                        let canApproveLevel = userDesignation === 'Admin' ||
                            level.designations.some(des => des.designation === userDesignation);

                        const designationBadges = level.designations.map(des => {
                            const badgeClass = des.status === 'Approved' ? 'bg-success' :
                                des.status === 'Rejected' ? 'bg-danger' : 'bg-secondary';
                            return `<span class="badge ${badgeClass} me-1">${des.designation}</span>`;
                        });

                        const existingNote = level.designations.find(d => d.note)?.note || '';
                        const isDisabled = isLevelApproved || currentStatus != '0' ? 'disabled' : '';

                        html += `
<div class="level-card">
    <div class="level-header">
        Level ${level.level}: ${level.description}
    </div>
    <div class="p-2">
        ${designationBadges.join(' ')}
    </div>
    <div class="p-2">
        <textarea class="form-control form-control-sm mb-2" id="note-level-${level.id}" placeholder="Enter note for this level..." ${isDisabled}>${existingNote}</textarea>
    </div>
    <div class="text-end p-2">
        ${currentStatus == '0' && !isLevelApproved && canApproveLevel ? `
            <button class="btn btn-sm btn-success me-2" onclick="approveLevel(${level.id}, ${insuranceId})">Approve Level</button>
            <button class="btn btn-sm btn-danger" onclick="rejectLevel(${level.id}, ${insuranceId})">Reject Level</button>
        ` : isLevelApproved ? `
            <span class="badge bg-success">Level Approved</span>
        ` : ''}
    </div>
</div>`;
                    });

                    if (allApproved && currentStatus == '0') {
                        $.get('/company/bank-accounts', function (banks) {
                            let bankOptions = banks.map(bank => `
            <option value="${bank.Idbank}">
                ${bank.Bank_Type} - ${bank.Bank_Name} (${bank.Account_Name})
            </option>`).join('');

                            html += `
            <div class="p-3 border rounded bg-light mt-3">
                <label for="bankAccountSelect" class="form-label">Select Bank Account to Issue From</label>
                <select id="bankAccountSelect" class="form-select form-select-sm mb-3">
                    <option value="">-- Select Bank Account --</option>
                    ${bankOptions}
                </select>
                <div class="text-center">
                    <button class="btn btn-success" onclick="issueInsurance(${insuranceId})">Issue Insurance</button>
                </div>
            </div>`;

                            $('#approvalLevelsBody').html(html);
                            $('#approvalLevelsModal').modal('show');
                        });
                    } else {
                        $('#approvalLevelsBody').html(html);
                        $('#approvalLevelsModal').modal('show');
                    }

                }
            });
        }







        function approveLevel(levelId, insuranceId) {
            const note = $(`#note-level-${levelId}`).val();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to approve this entire level.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/insurance/approve-level', {
                        _token: '{{ csrf_token() }}',
                        level_id: levelId,
                        note: note,
                        insurance_id: insuranceId
                    }, function () {
                        const categoryId = $('#approvalLevelsModal').data('category-id');
                        viewApprovalLevels(categoryId, insuranceId);

                        Swal.fire({
                            title: 'Approved!',
                            text: 'The level has been approved.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadInsurances();
                    }).fail(function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    });
                }
            });
        }

        function rejectLevel(levelId, insuranceId) {
            const note = $(`#note-level-${levelId}`).val();

            if (!note) {
                return Swal.fire('Note Required', 'Please enter a note before rejecting.', 'warning');
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to reject this level.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reject it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/insurance/reject', {
                        _token: '{{ csrf_token() }}',
                        level_id: levelId,
                        insurance_id: insuranceId,
                        note: note // ✅ pass the note
                    }, function () {
                        const categoryId = $('#approvalLevelsModal').data('category-id');
                        viewApprovalLevels(categoryId, insuranceId);

                        Swal.fire({
                            title: 'Rejected!',
                            text: 'The level has been rejected.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadInsurances();
                    }).fail(function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    });
                }
            });
        }





        function issueInsurance(insuranceId) {
            const bankId = $('#bankAccountSelect').val(); // ✅ Get selected bank

            if (!bankId) {
                return Swal.fire('Required', 'Please select a bank account before issuing.', 'warning');
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will mark the insurance as issued and close the approval process.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, issue it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/insurance/issue', {
                        _token: '{{ csrf_token() }}',
                        insurance_id: insuranceId,
                        bank_id: bankId // ✅ Send the selected bank ID
                    }, function () {
                        $('#approvalLevelsModal').modal('hide');
                        loadInsurances();

                        Swal.fire({
                            title: 'Issued!',
                            text: 'The insurance has been successfully issued.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }).fail(function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    });
                }
            });
        }





    </script>
@endsection
