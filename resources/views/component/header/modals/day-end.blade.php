<!-- Day End Modal -->
<div class="modal fade" id="dayEndModal" tabindex="-1" aria-labelledby="dayEndLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayEndLabel">Day End Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dayEndForm">
                    <!-- Starting Cash -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Starting Cash (Plot Amount)</strong></label>
                        <input type="number" class="form-control" id="plotAmount" readonly>
                    </div>

                    <hr>
                    <h5 class="mt-3"><strong>Cash In</strong></h5>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="table-success sticky-top" style="top: 0; z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th>Date Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="cashInTableBody">
                            <!-- Cash In entries will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <h5 class="mt-3"><strong>Total Income: <span id="totalIncome">0.00</span></strong></h5>


                    <hr>
                    <h5 class="mt-3"><strong>Cash Out</strong></h5>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="table-danger sticky-top" style="top: 0; z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th>Date Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="cashOutTableBody">
                            <!-- Cash Out entries will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <h5 class="mt-3"><strong>Total Expenses: <span id="totalExpenses">0.00</span></strong></h5>


                    <hr> <!-- Horizontal Line for Separation -->
                    <!-- Balance Calculation Section -->
                    <hr> <!-- Separator -->
                    <u><h5 class="mt-3"><strong>Balance Calculation</strong></h5></u>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label"><strong>Balance Amount</strong></label>
                            <input type="number" class="form-control" id="balanceAmount" readonly>
                        </div>
                    </div>
                    <hr>

                    <hr> <!-- Horizontal Line for Separation -->
                    <!-- Cash Drawer Balance Section -->
                    <u><h5 class="mt-3"><strong>Cash Drawer Balance</strong></h5></u>
                    <form id="cashDrawerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select Denomination</label>
                                <select id="cashAmount" class="form-control enhanced-select" required>
                                    <option value="">Select Denomination</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="5000">5000</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="cashQuantity" required>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mt-3" id="addCashToTable">Add to Table</button>
                    </form>

                    <!-- Cash Drawer Balance Table -->
                    <table class="table table-bordered mt-3">
                        <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Denomination</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="cashDrawerTableBody">
                        <!-- Entries will be added dynamically -->
                        </tbody>
                    </table>

                    <!-- Total Cash Drawer Balance -->
                    <h5 class="mt-3"><strong>Total Cash Drawer Balance: <span id="totalCashDrawer">0.00</span></strong></h5>

                    <!-- Balance Difference Display -->
                    <h5 class="mt-3 text-end"><strong>Balance Difference: <span id="balanceDifference" class="text-danger">0.00</span></strong></h5>

                    <div class="d-flex justify-content-between mt-4">
                        <!-- Save Button -->
                        <button type="button" class="btn btn-primary" id="saveDayEnd">Save Day End</button>
                        <button type="button" class="btn btn-success" id="printDayEndReport">Print Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
