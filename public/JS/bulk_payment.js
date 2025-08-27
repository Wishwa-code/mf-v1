/* bulk_payment.js — full file */

// ====================== CONFIG & GLOBALS ======================
const PER_PAGE = 10; // backend page size

// Persist user inputs across pages: { [loanId]: { amount, savingAmount, date } }
let inputDataStore = {};

// Remember meta for each loan across pages: { [loanId]: { cus_id } }
let loanMeta = {};

// Track current page
let lastLoadedPage = 1;

// Number formatter
const formatter = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });


// ====================== UTILS ======================

// Simple throttle to limit frequent input events
function throttle(fn, wait = 80) {
    let timer = null, lastArgs, lastThis;
    return function (...args) {
        lastArgs = args;
        lastThis = this;
        if (!timer) {
            fn.apply(lastThis, lastArgs);
            timer = setTimeout(() => {
                timer = null;
                if (lastArgs) fn.apply(lastThis, lastArgs);
            }, wait);
        }
    };
}

// Store helpers
function setInputStore(loanId, patch) {
    if (!loanId) return;
    inputDataStore[loanId] = { ...(inputDataStore[loanId] || {}), ...patch };
    updateTotalEnteredAmountFromStore();
}
function getInputStore(loanId) {
    return inputDataStore[loanId] || {};
}
function clearStore() {
    inputDataStore = {};
    updateTotalEnteredAmountFromStore();
}

// Totals across ALL pages (entered amount)
function updateTotalEnteredAmountFromStore() {
    let total = 0;
    for (const k in inputDataStore) {
        const v = parseFloat(inputDataStore[k]?.amount);
        if (!isNaN(v)) total += v;
    }
    $('#tot_installment').text(formatter.format(total));
}


// ====================== INPUT HANDLERS ======================

// Allow only digits and a single dot
$(document).on('input', '.numeric-input', throttle(function () {
    let v = $(this).val().replace(/[^0-9.]/g, '');
    const parts = v.split('.');
    if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
    $(this).val(v);
}, 60));

// Validate amount <= balance + persist to store
$(document).on('input', '.amount-input', throttle(function () {
    const $el = $(this);
    const loanId = $el.data('loan-id');
    const entered = parseFloat($el.val()) || 0;
    const max = parseFloat($el.data('balance')) || 0;

    if (entered > max) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Payment Amount',
            text: `Entered amount (${formatter.format(entered)}) exceeds balance (${formatter.format(max)}).`
        });
        $el.val('');
        setInputStore(loanId, { amount: '' });
        return;
    }
    setInputStore(loanId, { amount: $el.val() });
}, 60));

// Persist saving amount
$(document).on('input', '.saving-amount-input', throttle(function () {
    const $el = $(this);
    setInputStore($el.data('loan-id'), { savingAmount: $el.val() });
}, 60));

// Persist date
$(document).on('change', 'input[name="date_bulk"]', function () {
    const $el = $(this);
    setInputStore($el.data('loan-id'), { date: $el.val() });
});


// ====================== PAGINATION RENDER ======================

function renderPageInfo(p) {
    const start = (p.current_page - 1) * p.per_page + 1;
    const end = Math.min(p.total, p.current_page * p.per_page);
    return `<span class="me-2 align-self-center">Showing ${start}–${end} of ${p.total}</span>`;
}

function renderPagination(p) {
    const btn = (label, page, disabled = false, active = false) =>
        `<button class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-primary'} mx-1"
             data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;

    let html = '';
    html += btn('« First', 1, p.current_page === 1);
    html += btn('‹ Prev', Math.max(1, p.current_page - 1), p.current_page === 1);

    const start = Math.max(1, p.current_page - 2);
    const end   = Math.min(p.last_page, p.current_page + 2);
    for (let i = start; i <= end; i++) {
        html += btn(String(i), i, false, i === p.current_page);
    }

    html += btn('Next ›', Math.min(p.last_page, p.current_page + 1), p.current_page === p.last_page);
    html += btn('Last »', p.last_page, p.current_page === p.last_page);

    const $c = $('#pagination').empty();
    $c.append(renderPageInfo(p)).append(html);

    // Bind page clicks — do NOT clear store; do NOT recompute totals
    $c.find('button[data-page]').off('click').on('click', function () {
        const page = parseInt($(this).data('page'), 10);
        load_payment_table(page, false);
    });
}


// ====================== MAIN LOADER ======================

/**
 * Load one page and render.
 * @param {number} page - page number (1-based)
 * @param {boolean} includeTotals - if true, backend returns gettotal (use on first load / filter change)
 */
function load_payment_table(page = 1, includeTotals = false) {
    lastLoadedPage = page;

    // Selected filters for request
    const center_details      = $('#center_details').val();
    const group               = $('#group').val();
    const customer            = $('#customer_id').val();
    const status              = $('#status').val();
    const route               = $('#route').val();
    const loan_number_search  = $('#loan_number_search').val();

    // Selected filters badges
    $('#selected_filters_content').html(`
    <span class="badge bg-success">Route: ${$('#route option:selected').text()}</span>
    <span class="badge bg-success">Center: ${$('#center_details option:selected').text()}</span>
    <span class="badge bg-success">Group: ${$('#group option:selected').text()}</span>
    <span class="badge bg-success">Customer: ${$('#customer_id option:selected').text()}</span>
    <span class="badge bg-success">Status: ${$('#status option:selected').text()}</span>
    <span class="badge bg-success">Loan No: ${$('#loan_number_search option:selected').text()}</span>
  `);

    // Date constraints (backdate rules)
    const today = new Date().toISOString().split('T')[0];
    const backdateSetting = window.APP_SETTINGS?.payment_backdate; // "enabled" | "disabled"
    let minDate;
    if (backdateSetting === 'enabled') {
        const lastYear = new Date(); lastYear.setFullYear(lastYear.getFullYear() - 1);
        minDate = lastYear.toISOString().split('T')[0];
    } else {
        minDate = today;
    }

    $.ajax({
        type: 'POST',
        url: `/today_payment_load_check_bulk?page=${page}`, // Laravel paginator reads ?page
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            center_details,
            group,
            customer,
            route,
            loan_number_search,
            status,
            per_page: PER_PAGE,
            include_totals: includeTotals ? 1 : 0
        },
        success: function (res, _text, xhr) {
            if (xhr.status !== 200) return;

            const pager = res.item || {};
            const data  = pager.data || [];

            let rowsHTML = '';
            const defaultDate = today;

            for (let i = 0; i < data.length; i++) {
                const item = data[i];
                if (item.type !== 'Flat Rate') continue;

                const loanId = item.idCustomer_Loan;

                // cache cus_id for this loan (cross-page submission)
                loanMeta[loanId] = { cus_id: item.idCustomer };

                // restore stored values for this loan
                const store        = getInputStore(loanId);
                const inputDate    = store.date || defaultDate; // default to today
                const inputAmount  = store.amount ?? '';
                const savingAmount = store.savingAmount ?? '';

                // ensure a date is stored (so bulk submit has a value even if user didn't touch the date)
                if (!store.date) setInputStore(loanId, { date: inputDate });

                const placeholder = item.saving_payment === '1' ? 'Installment Amount' : 'Enter amount';

                rowsHTML += `
          <tr>
            <td>${item.Loan_No}</td>
            <td>${item.customer_name} ${item.customer_lastname}</td>
            <td>${item.center_no}</td>
            <td>${item.group_name}</td>
            <td>${formatter.format(parseFloat(item.Loan_Amount))}</td>
            <td>${formatter.format(parseFloat(item.Balance_With_Penalty))}</td>
            <td>${formatter.format(parseFloat(item.Last_Payment_Amount))}</td>
            <td>${item.Last_Payment_Date}</td>
            <td>${formatter.format(parseFloat(item.Today_installment))}</td>
            <td>
              <input type="date" name="date_bulk" class="form-control"
                     value="${inputDate}" data-loan-id="${loanId}"
                     min="${minDate}" max="${today}" />
            </td>
            <td>
              <input type="text" class="form-control numeric-input amount-input" style="width:200px;"
                     placeholder="${placeholder}" value="${inputAmount}"
                     data-loan-id="${loanId}" data-balance="${item.Balance_With_Penalty}" />
              ${item.saving_payment === '1' ? `
                <br>
                <input type="text" class="form-control numeric-input saving-amount-input" style="width:200px;"
                       placeholder="Enter Saving Amount" value="${savingAmount}"
                       data-loan-id="${loanId}" data-balance="${item.Balance_With_Penalty}" />
              ` : ``}
              <input type="hidden" name="loan_id" value="${loanId}" />
              <input type="hidden" name="cus_id" value="${item.idCustomer}" />
            </td>
            <td>${item.NIC}</td>
            <td>${item.type}</td>
          </tr>
        `;
            }

            // inject table rows once (fast)
            $('#loan_table tbody').html(rowsHTML);

            // “Total Today Installment” — only recompute when includeTotals = true
            if (includeTotals) {
                let tot = 0;
                const totals = res.gettotal || [];
                for (let i = 0; i < totals.length; i++) {
                    tot += parseFloat(totals[i].Today_installment || 0);
                }
                $('#tot_amount').text(formatter.format(tot));
            }

            // Global total of user-entered amounts across all pages
            updateTotalEnteredAmountFromStore();

            // Pagination
            renderPagination(pager);
        },
        error: function (_xhr, _t, err) {
            console.error('load_payment_table error:', err);
        }
    });
}


// ====================== BULK PAYMENT ======================

async function performPayment(cus_id, payment_amount, reduce_balance_loan_id, payment_date, saving_amount) {
    const file = $('#file')[0]?.files[0]; // optional file (shared input)
    const formData = new FormData();
    formData.append('cus_id', cus_id);
    formData.append('payment_amount', payment_amount);
    formData.append('saving_amount', saving_amount || 0);
    formData.append('file', file || '');
    formData.append('loan_id', reduce_balance_loan_id);
    formData.append('payment_date', payment_date);
    formData.append('payment_type', 'Cash');
    formData.append('bank_account_company', '1');
    formData.append('sms', '1');

    return new Promise((resolve) => {
        $.ajax({
            type: 'POST',
            url: '/payment_save_today',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: formData,
            processData: false,
            contentType: false,
            success: function (_d, _t, xhr) { resolve(xhr.status === 200); },
            error: function (_x, _t, e) {
                Swal.fire('Error!', `Failed to save data: ${e}`, 'error');
                resolve(false);
            }
        });
    });
}

/**
 * Process ALL payments the user has entered across ALL pages.
 * Reads from inputDataStore + loanMeta (cus_id).
 */
function automatePayments() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to process all payments you’ve entered (across all pages)?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) {
            Swal.fire({ icon: 'info', title: 'Action Cancelled', text: 'Payment processing was not started.' });
            return;
        }

        // Build jobs from the store (all pages)
        const jobs = Object.entries(inputDataStore)
            .map(([loanId, s]) => ({
                loanId,
                amount: parseFloat(s.amount || 0),
                savingAmount: parseFloat(s.savingAmount || 0) || 0,
                date: s.date,
                cus_id: loanMeta[loanId]?.cus_id || null
            }))
            .filter(j => j.cus_id && j.amount > 0 && j.date);

        if (jobs.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Nothing to process', text: 'No valid payments found across pages.' });
            return;
        }

        Swal.fire({
            title: 'Processing Payments',
            html: `
        <div style="margin-top: 20px;">
          <div id="progress-container" style="width: 100%; background: #f3f3f3; border-radius: 8px; overflow: hidden; height: 25px;">
            <div id="progress-bar" style="height: 100%; width: 0%; transition: width 0.3s;"></div>
          </div>
          <p style="margin-top: 10px;" id="progress-text">Initializing...</p>
        </div>
      `,
            showConfirmButton: false,
            allowOutsideClick: false,
            willOpen: async () => {
                let successCount = 0;

                for (let i = 0; i < jobs.length; i++) {
                    const j = jobs[i];

                    // progress UI
                    const pct = Math.round(((i + 1) / jobs.length) * 100);
                    $('#progress-text').text(`Processing ${i + 1} of ${jobs.length}...`);
                    $('#progress-bar').css('width', `${pct}%`);

                    // submit
                    const ok = await performPayment(j.cus_id, j.amount, j.loanId, j.date, j.savingAmount);
                    if (ok) {
                        successCount++;
                        // clear just this loan entry from store
                        setInputStore(j.loanId, { amount: '', savingAmount: '', date: '' });
                    }
                }

                if (successCount > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payments Complete',
                        text: `${successCount} payment(s) were successfully processed.`
                    }).then(() => {
                        // reload current page; totals are already adjusted by setInputStore
                        load_payment_table(lastLoadedPage, false);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'No Payments Processed', text: 'No valid payments were made. Please check your data.' });
                }
            }
        });
    });
}


// ====================== FILTER CHANGE & INIT ======================

// When any filter changes → clear inputs, load page 1, ask backend for totals
$(document).on('change', '#route,#center_details,#group,#customer_id,#status,#loan_number_search', function () {
    clearStore();
    load_payment_table(1, true);
});

// Initial load – include totals once
$(function () {
    load_payment_table(1, true);

    // Expose for inline onclick attributes if needed
    window.load_payment_table = load_payment_table;
    window.automatePayments = automatePayments;
});
