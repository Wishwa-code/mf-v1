<div class="modal fade" id="cashierStartModal" tabindex="-1" aria-labelledby="cashierStartLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashierStartLabel">Cashier Start Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cashierForm">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <select class="form-control enhanced-select" id="amount" required>
                            <option value="">Select Amount</option>
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
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" required>
                    </div>
                    <button type="button" class="btn btn-secondary" id="addToTable">Add to Table</button>
                </form>

                <!-- Table inside Modal -->
                <!-- Table inside Modal -->
                <h5 class="mt-3">Added Entries</h5>
                <table class="table mt-2">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="modalTableBody">
                    <!-- Entries will be added here -->
                    </tbody>
                </table>

                <!-- Grand Total Row -->
                <h5 class="mt-3">Grand Total: <span id="grandTotal">0</span></h5>


                <button type="button" class="btn btn-primary" id="saveEntries">Save</button>

            </div>
            <button type="button" class="btn btn-success mt-3" id="printDayStartReport">Print Day Start</button>
            <br>
        </div>
    </div>
</div>






