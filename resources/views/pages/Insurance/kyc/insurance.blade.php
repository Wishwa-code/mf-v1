<div class="card p-4">
    <h5 class="mb-4 text-primary">
        <i class="fas fa-shield-alt me-2"></i>Request Insurance
    </h5>

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Insurance Category</label>
            <select class="form-select" id="insuranceCategory">
                <option value="0">Select Insurance Category</option>
                @foreach($insurance_category as $category)
                    <option
                            value="{{ $category->id_insurance_category }}"
                            data-type="{{ $category->type }}"
                            data-amount="{{ $category->amount }}">
                        {{ $category->description }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> Add New
            </button>
        </div>

        <div class="col-md-2">
            <label class="form-label">Date Count</label>
            <input type="number" class="form-control" id="dateCount" value="1">
        </div>

        <div class="col-md-2">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" id="insurance_amount" value="0">
        </div>

        <div class="col-md-2">
            <label class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="totalAmount" readonly>
        </div>
    </div>

    <div class="mt-4">
        <label class="form-label">Upload Evidence (Multiple allowed)</label>
        <input type="file" class="form-control" id="evidenceInput" multiple accept="image/*,.pdf,.doc,.docx,.xlsx,.csv,.txt">
        <div class="mt-3" id="filePreview" style="display: flex; flex-wrap: wrap; gap: 1rem;"></div>

    </div>


    <div class="mt-3">
        <label class="form-label">Note</label>
        <textarea class="form-control" rows="3" id="insurance_note" placeholder="Any notes related to this request..."></textarea>
    </div>

    <div class="mt-4">
        <button class="btn btn-success" id="request_insurance">
            <i class="fas fa-paper-plane me-1"></i> Request Insurance
        </button>
    </div>
</div>

<hr class="my-5">

<div class="card p-4">
    <h5 class="mb-3 text-primary"><i class="fas fa-history me-2"></i>Insurance Request History</h5>
    <div class="table-responsive">
        <table id="insuranceHistoryTable" class="table table-bordered table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Created By</th>
                <th>Approved By</th>
                <th>Status</th>
                <th>Evidence</th>
{{--                <th>Action</th>--}}
            </tr>
            </thead>
            <tbody>
            <!-- This will be replaced by JavaScript -->
            </tbody>
        </table>

    </div>
</div>


<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add Insurance Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Inside #addCategoryModal -->
                    <div class="col-md-6">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="categoryType">
                            <option value="">Select Type</option>
                            <option value="One Time">One Time</option>
                            <option value="Recurring - Per Day">Recurring - Per Day</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" id="categoryAmount">
                    </div>

                </div>

                <hr class="my-4">

                <h6 class="mb-2">Approval Levels</h6>
                <div class="container mt-4">
                    <!-- Add/remove level controls -->
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button class="btn btn-outline-danger" id="removeLastLevelBtn">
                            <i class="fas fa-minus-circle me-1"></i> Remove Last Level
                        </button>
                        <button class="btn btn-success" id="addNewLevelBtn">
                            <i class="fas fa-plus-circle me-1"></i> Add New Level
                        </button>
                    </div>


                    <!-- Levels will appear here -->
                    <div id="levelsContainer"></div>
                    <!-- Place this somewhere in your Blade template -->
                    <div id="designationOptions" class="d-none">
                        @foreach($designation as $item)
                            <option value="{{ $item->idDesignation }}">{{ $item->name }}</option>
                        @endforeach
                    </div>


                </div>




            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="saveApprovalData()">
                    <i class="fas fa-save me-1"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Evidence Modal -->
<div class="modal fade" id="viewEvidenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uploaded Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="evidenceContent" style="display: flex; flex-wrap: wrap; gap: 1rem;"></div>
        </div>
    </div>
</div>


<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let levelIndex = 1;
    const levelsData = [];
    $(document).ready(function () {
        loadInsuranceHistory();


        document.getElementById('addNewLevelBtn').addEventListener('click', () => {
            const levelId = `level-${levelIndex}`;
            const optionsHTML = document.getElementById('designationOptions').innerHTML;

            const levelHTML = `
    <div class="card border mb-4 shadow-sm" id="${levelId}">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div class="fw-bold">Level ${levelIndex.toString().padStart(2, '0')}</div>
            <input class="form-control w-50" placeholder="Enter Level Description" id="${levelId}-desc" />
        </div>
        <div class="card-body p-4 bg-light">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Designation</label>
                    <select class="form-select" id="${levelId}-designation">
                        <option disabled selected>Select Designation</option>
                        ${optionsHTML}
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="addDesignation('${levelId}')">
                        <i class="fas fa-plus me-1"></i> Add Designation
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered bg-white">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70%;">Designation</th>
                            <th style="width: 30%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="${levelId}-tableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
`;


            document.getElementById('levelsContainer').insertAdjacentHTML('beforeend', levelHTML);
            levelsData.push({ level: levelIndex, description: '', designations: [] });
            levelIndex++;
        });

        document.getElementById('removeLastLevelBtn').addEventListener('click', () => {
            if (levelIndex <= 1) {
                alert("No levels to remove.");
                return;
            }

            const lastLevelId = `level-${levelIndex - 1}`;
            const lastCard = document.getElementById(lastLevelId);
            if (lastCard) {
                lastCard.remove();
            }

            levelsData.pop();
            levelIndex--;

            console.log(`Removed Level ${levelIndex}.`, levelsData);
        });








        function loadInsuranceHistory() {
            let cus_id = {{ $id }}; // Assuming this is passed from Blade

            fetch(`/insurance/history/${cus_id}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#insuranceHistoryTable tbody');
                    tbody.innerHTML = ''; // Clear old rows

                    if (!data.length) {
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center">No insurance requests found.</td></tr>`;
                        return;
                    }

                    data.forEach(item => {
                        const row = `
                    <tr>
                        <td>${item.date}</td>
                        <td>${item.category}</td>
                        <td>Rs. ${parseFloat(item.total_amount).toFixed(2)}</td>
                        <td>${item.note || '-'}</td>
                        <td>${item.created_by}</td>
                        <td>${item.approved_by}</td>
                        <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
                        <td><button class="btn btn-sm btn-primary" onclick="viewEvidence(${item.id_insurance})"><i class="fas fa-eye"></i></button></td>

                    </tr>
                `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });

                })
                .catch(err => {
                    console.error("Failed to load insurance history:", err);
                });
        }


        function getStatusBadge(status) {
            switch (String(status)) {
                case '1': return 'bg-success'; // Approved
                case '0': return 'bg-warning text-dark'; // Pending
                case '-1': return 'bg-danger'; // Rejected
                default: return 'bg-secondary';
            }
        }

        function getStatusText(status) {
            switch (String(status)) {
                case '1': return 'Approved';
                case '0': return 'Pending';
                case '-1': return 'Rejected';
                default: return 'Unknown';
            }
        }

        const categoryDropdown = document.getElementById('insuranceCategory');
        const dateCountField = document.getElementById('dateCount');
        const amountField = document.getElementById('insurance_amount');
        const totalAmountField = document.getElementById('totalAmount');

        const dateCountWrapper = dateCountField.parentElement;
        const amountWrapper = amountField.parentElement;
        const totalAmountWrapper = totalAmountField.parentElement;

        let currentAmount = 0; // This will store the selected amount safely

        // Hide fields initially
        hideFields();

        categoryDropdown.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.getAttribute('data-type');
            const amount = parseFloat(selectedOption.getAttribute('data-amount')) || 0;

            currentAmount = amount;

            if (selectedOption.value === "0") {
                hideFields();
                return;
            }

            totalAmountWrapper.style.display = '';

            if (type !== 'One Time') {
                console.log(amount);
                dateCountWrapper.style.display = '';
                amountWrapper.style.display = '';

                dateCountField.value = 1;


                $('#insurance_amount').val(amount.toFixed(2));
                updateTotalAmount();
            } else {
                dateCountWrapper.style.display = 'none';
                amountWrapper.style.display = 'none';

                dateCountField.value = 1;


                $('#insurance_amount').val(amount.toFixed(2));
                totalAmountField.value = currentAmount.toFixed(2);
            }

        });


        dateCountField.addEventListener('input', updateTotalAmount);
        amountField.addEventListener('input', function () {
            currentAmount = parseFloat(this.value) || 0;
            updateTotalAmount();
        });

        function updateTotalAmount() {
            const count = parseInt(dateCountField.value) || 0;
            totalAmountField.value = (count * currentAmount).toFixed(2);
        }

        function hideFields() {
            dateCountWrapper.style.display = 'none';
            amountWrapper.style.display = 'none';
            totalAmountWrapper.style.display = 'none';
        }

        // Trigger logic on page load
        window.addEventListener('DOMContentLoaded', () => {
            categoryDropdown.dispatchEvent(new Event('change'));
        });

        document.getElementById('evidenceInput').addEventListener('change', function (e) {
            const previewContainer = document.getElementById('filePreview');
            previewContainer.innerHTML = ''; // Clear previous previews

            const files = Array.from(this.files);

            files.forEach(file => {
                const fileReader = new FileReader();
                const fileType = file.type;

                const previewCard = document.createElement('div');
                previewCard.classList.add('border', 'rounded', 'p-2');
                previewCard.style.width = '150px';
                previewCard.style.textAlign = 'center';
                previewCard.style.position = 'relative';

                // Show thumbnail for image
                if (fileType.startsWith('image/')) {
                    fileReader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-fluid', 'rounded');
                        img.style.maxHeight = '100px';
                        previewCard.appendChild(img);
                    };
                    fileReader.readAsDataURL(file);
                } else {
                    // Show icon and filename for non-image
                    const icon = document.createElement('div');
                    icon.innerHTML = `<i class="fas fa-file-alt fa-2x text-secondary"></i>`;
                    const name = document.createElement('div');
                    name.textContent = file.name;
                    name.style.fontSize = '12px';
                    name.classList.add('mt-2');
                    previewCard.appendChild(icon);
                    previewCard.appendChild(name);
                }

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '5px';
                removeBtn.style.right = '5px';
                removeBtn.style.padding = '2px 6px';
                removeBtn.onclick = () => {
                    const index = files.indexOf(file);
                    files.splice(index, 1);
                    const dataTransfer = new DataTransfer();
                    files.forEach(f => dataTransfer.items.add(f));
                    document.getElementById('evidenceInput').files = dataTransfer.files;
                    previewCard.remove();
                };

                previewCard.appendChild(removeBtn);
                previewContainer.appendChild(previewCard);
            });
        });

        document.getElementById('request_insurance').addEventListener('click', function () {
            const categoryId = document.getElementById('insuranceCategory').value;
            const customerSelect = {{$id}};
            const dayCount = document.getElementById('dateCount').value;
            const amount = document.getElementById('insurance_amount').value;
            const totalAmount = document.getElementById('totalAmount').value;
            const note = document.getElementById('insurance_note').value;
            const files = document.getElementById('evidenceInput').files;

            if (categoryId === "0") {
                Swal.fire("Validation Error", "Please select an insurance category.", "warning");
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "You are about to request insurance.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, submit",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id_insurance_category', categoryId);
                    formData.append('day_count', dayCount);
                    formData.append('amount', amount);
                    formData.append('total_amount', totalAmount);
                    formData.append('note', note);
                    formData.append('customer_id', customerSelect);

                    for (let i = 0; i < files.length; i++) {
                        formData.append('documents[]', files[i]);
                    }

                    fetch("{{ route('insurance.request') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Success", data.message, "success");

// Clear form fields
                                document.getElementById('insuranceCategory').value = "0";
                                document.getElementById('dateCount').value = "1";
                                document.getElementById('insurance_amount').value = "0";
                                document.getElementById('totalAmount').value = "";
                                document.getElementById('insurance_note').value = "";
                                document.getElementById('evidenceInput').value = "";

// Clear preview area
                                document.getElementById('filePreview').innerHTML = "";

// Hide fields (optional: reset UI)
                                hideFields();
                                // 🔥 Load latest history
                                loadInsuranceHistory();
                            } else {
                                Swal.fire("Error", data.message, "error");
                            }
                        })
                        .catch(err => {
                            Swal.fire("Error", "Something went wrong!", "error");
                            console.error(err);
                        });
                }
            });
        });

    });

    function addDesignation(levelId) {
        const levelNum = parseInt(levelId.split('-')[1]);
        const descInput = document.getElementById(`${levelId}-desc`);
        const selectEl = document.getElementById(`${levelId}-designation`);
        const tableBody = document.getElementById(`${levelId}-tableBody`);

        const designationId = selectEl.value;
        const designationName = selectEl.options[selectEl.selectedIndex].text;
        const level = levelsData.find(l => l.level === levelNum);

        if (!designationId || designationId === "Select Designation") {
            alert("Please select a designation.");
            return;
        }

        // Save level description if it's filled
        if (descInput.value.trim()) {
            level.description = descInput.value.trim();
        }

        // Prevent duplicates
        const alreadyExists = level.designations.some(d => d.id === designationId);
        if (alreadyExists) {
            alert("This designation is already added.");
            return;
        }

        level.designations.push({ id: designationId, name: designationName });

        // Update table
        const row = `
            <tr>
                <td>${designationName}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeDesignation('${levelId}', '${designationId}')">
                        Remove
                    </button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', row);
    }

    function removeDesignation(levelId, designationId) {
        const levelNum = parseInt(levelId.split('-')[1]);
        const level = levelsData.find(l => l.level === levelNum);
        level.designations = level.designations.filter(d => d.id !== designationId);

        const tableBody = document.getElementById(`${levelId}-tableBody`);
        tableBody.innerHTML = '';
        level.designations.forEach(d => {
            const row = `
                <tr>
                    <td>${d.name}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="removeDesignation('${levelId}', '${d.id}')">Remove</button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function saveApprovalData() {
        const modal = document.getElementById('addCategoryModal');

        const categoryName = modal.querySelector('#categoryName')?.value?.trim();
        const type = modal.querySelector('#categoryType')?.value?.trim();
        const amount = modal.querySelector('#categoryAmount')?.value?.trim();

        // Validate category fields first
        if (!categoryName || !amount || !type) {
            Swal.fire("Validation Error", "Please fill in Category Name, Type, and Amount.", "warning");
            return;
        }

        const levelCards = modal.querySelectorAll('#levelsContainer .card');
        if (levelCards.length === 0) {
            Swal.fire("Validation Error", "Please add at least one approval level.", "warning");
            return;
        }

        const finalLevels = [];
        let isValid = true;

        levelCards.forEach((card, index) => {
            const levelId = card.id;
            const descInput = modal.querySelector(`#${levelId}-desc`);
            const tableBody = modal.querySelector(`#${levelId}-tableBody`);

            const description = descInput?.value?.trim();
            if (!description) {
                Swal.fire("Validation Error", `Level ${index + 1} must have a description.`, "warning");
                isValid = false;
                return;
            }

            const rows = tableBody?.querySelectorAll('tr') || [];
            if (rows.length === 0) {
                Swal.fire("Validation Error", `Level ${index + 1} must have at least one designation.`, "warning");
                isValid = false;
                return;
            }

            const designations = Array.from(rows).map(row => {
                const name = row.querySelector('td')?.textContent?.trim();
                const idAttr = row.querySelector('button')?.getAttribute('onclick') || '';
                const idMatch = idAttr.match(/removeDesignation\('.*?',\s*'(.*?)'\)/);
                const id = idMatch ? idMatch[1] : null;
                return { id, name };
            });

            finalLevels.push({
                level: index + 1,
                description,
                designations
            });
        });

        if (!isValid) return;

        // Confirm and submit
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to save this category and approval structure?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, save it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('insurance.category.save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({
                        category_name: categoryName,
                        type: type,
                        amount: amount,
                        levels: finalLevels
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Saved!", data.message, "success");

// Add new category to dropdown
                            const select = document.getElementById('insuranceCategory');
                            const newOption = document.createElement('option');
                            newOption.value = data.category.id;
                            newOption.text = data.category.description;
                            newOption.setAttribute('data-type', data.category.type);
                            newOption.setAttribute('data-amount', data.category.amount);
                            select.appendChild(newOption);

// Select the newly added category
                            select.value = data.category.id;
                            select.dispatchEvent(new Event('change'));

// Reset form fields
                            modal.querySelector('#categoryName').value = '';
                            modal.querySelector('#categoryType').value = '';
                            modal.querySelector('#categoryAmount').value = '';
                            document.getElementById('levelsContainer').innerHTML = '';
                            levelsData.length = 0;
                            levelIndex = 1;

// Close modal
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) modalInstance.hide();

                        } else {
                            Swal.fire("Error", data.message || "Something went wrong.", "error");
                        }
                    })
                    .catch(() => {
                        Swal.fire("Error", "An unexpected error occurred.", "error");
                    });
            }
        });
    }

    function viewEvidence(id) {
        fetch(`/insurance/evidence/${id}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('evidenceContent');
                container.innerHTML = '';

                if (!data.length) {
                    container.innerHTML = `<p class="text-muted">No evidence uploaded.</p>`;
                } else {
                    data.forEach(file => {
                        const card = document.createElement('div');
                        card.className = 'border p-2 rounded';
                        card.style.width = '150px';
                        card.style.textAlign = 'center';

                        const mime = file.type || '';

                        if (mime.startsWith('image/')) {
                            card.innerHTML = `<img src="${file.url}" class="img-fluid rounded" style="max-height:100px;">`;
                        } else {
                            card.innerHTML = `
                            <i class="fas fa-file-alt fa-2x text-secondary"></i>
                            <div style="font-size:12px; word-break: break-all;">${file.name}</div>
                            <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">View</a>
                        `;
                        }

                        container.appendChild(card);
                    });
                }

                const modal = new bootstrap.Modal(document.getElementById('viewEvidenceModal'));
                modal.show();
            })
            .catch(err => {
                console.error("Error fetching evidence files", err);
                Swal.fire("Error", "Could not load evidence files.", "error");
            });
    }




</script>


