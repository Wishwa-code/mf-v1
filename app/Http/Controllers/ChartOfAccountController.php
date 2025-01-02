<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $group = null)
    {
        // Base query
        $query = tableWithBranch('chart_of_account');

        // Filter by group (if not 'all')
        if ($group && $group !== 'all') {
            $query->where('acc_type_group', ucfirst($group)); // Match exact case (e.g., 'Assets')
        }

        // Filter by code, name, and type (if provided)
        if ($request->has('code') && !empty($request->code)) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('acc_name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->where('acc_type', 'LIKE', '%' . $request->type . '%');
        }

        // Fetch the filtered data
        $data = $query->get();

        return response()->json($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input (optional but recommended)
        $request->validate([
            'code' => 'required|numeric',
            'acc_name' => 'required|string|max:255',
            'acc_type_group' => 'required|string',
            'acc_type' => 'required|string',
            'cash_flow_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Insert data into DB
        $inserted = insertWithBranch('chart_of_account',[
            'code' => $request->input('code'),
            'acc_name' => $request->input('acc_name'),
            'acc_type_group' => $request->input('acc_type_group'),
            'acc_type' => $request->input('acc_type'),
            'cash_flow_type' => $request->input('cash_flow_type'),
            'description' => $request->input('description'),
            'opening_balance' => 0, // set default if needed
            'current_balance' => 0, // set default if needed
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if($inserted) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $chart_of_accounts = tableWithBranch('chart_of_account')->get();
        return view('pages.Accounting.AddJournal', compact('chart_of_accounts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the main journal record
        $journal = tableWithBranch('manual_journal')->where('id_manual_journal', $id)->first();

        if (!$journal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Journal not found.',
            ]);
        }

        // Fetch the related details
        $details = tableWithBranch('manual_journal_has_amount')
            ->where('id_manual_journal', $id)
            ->get();

        // Return the response
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $journal->id_manual_journal,
                'narration' => $journal->narration,
                'date' => $journal->date,
                'type' => $journal->type,
                'details' => $details,
            ],
        ]);
    }


    public function save(Request $request)
    {
        try {

            Log::error('ID from request: ' . $request->input('id_manual_journal'));

            if ($request->filled('id_manual_journal')) {
                // Update the manual journal

                updateWithBranch('manual_journal', 'id_manual_journal', $request->center_id, [
                    'narration' => $request->narration,
                    'date' => $request->date,
                    'type' => $request->type,
                    'tot_debit' => $request->tot_debit,
                    'tot_credit' => $request->tot_credit,
                    'total_amount' => $request->total_amount,
                    'updated_at' => now(),
                ]);

                $journalId = $request->id_manual_journal;

                // Delete existing details for the journal
                deleteWithBranch('manual_journal_has_amount','id_manual_journal', $journalId);
            } else {
                Log::info("Inserting a new manual journal.");
                // Insert new manual journal
                $journalId = insertWithBranch('manual_journal',[
                    'narration' => $request->narration,
                    'date' => $request->date,
                    'type' => $request->type,
                    'tot_debit' => $request->tot_debit,
                    'tot_credit' => $request->tot_credit,
                    'total_amount' => $request->total_amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($request->rows as $row) {
                insertWithBranch('manual_journal_has_amount',[
                    'id_manual_journal' => $journalId,
                    'description' => $row['description'],
                    'account' => $row['account'],
                    'tax_rate' => $row['tax_rate'],
                    'debit_amount' => $row['debit_amount'],
                    'credit_amount' => $row['credit_amount'],
                    'created_at' => now(),
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Manual Journal saved successfully.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save journal.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function saveManualJournal(Request $request)
    {
        try {


            // Insert into the manual_journal table
            $manualJournalId = insertWithBranch('manual_journal',[
                'narration' => $request->narration,
                'date' => $request->date,
                'type' => $request->type,
                'tot_debit' => $request->tot_debit,
                'tot_credit' => $request->tot_credit,
                'total_amount' => $request->total_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert rows into manual_journal_has_amount table
            foreach ($request->rows as $row) {
                insertWithBranch('manual_journal_has_amount',[
                    'id_manual_journal' => $manualJournalId,
                    'description' => $row['description'],
                    'account' => $row['account'],
                    'tax_rate' => $row['tax_rate'],
                    'debit_amount' => $row['debit_amount'],
                    'credit_amount' => $row['credit_amount'],
                    'created_at' => now(),
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Manual Journal saved successfully!']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save Manual Journal.']);
        }
    }

    // Fetch data for the tables
    public function fetch(Request $request)
    {
        $query = tableWithBranch('manual_journal');

        // Apply filters
        if ($request->narration) {
            $query->where('narration', 'LIKE', '%' . $request->narration . '%');
        }
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        } elseif ($request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        } elseif ($request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->from_amount && $request->to_amount) {
            $query->whereBetween('total_amount', [$request->from_amount, $request->to_amount]);
        } elseif ($request->from_amount) {
            $query->where('total_amount', '>=', $request->from_amount);
        } elseif ($request->to_amount) {
            $query->where('total_amount', '<=', $request->to_amount);
        }


        $results = $query->get();

        // Separate results into posted and deleted
        $posted = $results->where('status', 1)->values();
        $deleted = $results->where('status', 0)->values();

        return response()->json([
            'posted' => $posted,
            'deleted' => $deleted,
        ]);
    }



    public function updateStatus(Request $request)
    {
        $id = $request->input('id_manual_journal');
        $status = $request->input('status');

        updateWithBranch('manual_journal','id_manual_journal',$id,[
            'status' => $status
        ]);

        return response()->json(['status' => 'success', 'message' => 'Status updated successfully.']);
    }

    public function viewDetails($id)
    {
        // Fetch data from manual_journal_has_amount based on id_manual_journal
        $data = tableWithBranch('manual_journal_has_amount')
            ->where('id_manual_journal', $id)
            ->get();

        // Return the data as JSON response
        return response()->json($data);
    }


    public function fetchLedger($account)
    {
        // Extract the first part of the account value
        $accountCode = explode('-', $account)[0];

        // Fetch matching records from the `manual_journal_has_amount` table
        $data = tableWithBranch('manual_journal_has_amount')
            ->where('account', 'LIKE', "$accountCode%") // Match records starting with accountCode
            ->get();

        // Return data as JSON
        return response()->json($data);
    }


    public function getAccountTrialBalance(Request $request)
    {
        $searchOption = $request->input('searchOption');
        $dateRange = explode(' to ', $searchOption); // Split the string by ' to '
        $dateTo = trim($dateRange[1]);

        // Query the database for account data
        $results = tableWithBranch('manual_journal_has_amount','manual_journal_has_amount')
            ->leftJoin('chart_of_account', 'chart_of_account.code', '=', DB::raw("SUBSTRING_INDEX(manual_journal_has_amount.account, '-', 1)"))
            ->select(
                DB::raw("SUBSTRING_INDEX(manual_journal_has_amount.account, '-', 1) as code"), // Extracted code
                'chart_of_account.code as acc_code', // Actual code from chart_of_account
                'chart_of_account.acc_name',
                DB::raw("SUM(manual_journal_has_amount.debit_amount) as total_debit"),
                DB::raw("SUM(manual_journal_has_amount.credit_amount) as total_credit")
            )
            ->whereDate('manual_journal_has_amount.created_at', '<=', $dateTo)
            ->groupBy('code', 'acc_code', 'chart_of_account.acc_name', 'manual_journal_has_amount.account')
            ->get();

        // Calculate additional summary data
        $loan = tableWithBranch('customer_loan')->where('Date_Time', '<=', $dateTo)->where('Status', 0)->sum('capital_balance');
        $client = tableWithBranch('Savings_Account_Log')->where('Date_Time', '<=', $dateTo)->where('Type', "=", "Deposit")->sum('Credit');
        $interest_loan = tableWithBranch('Loan_Log')->where('Date_Time', '<=', $dateTo)->sum('Interest_Payment');
        $interest_saving = tableWithBranch('Savings_Account_Log')->where('Date_Time', '<=', $dateTo)->where('Type', "=", "Interest")->sum('Credit');
        $expenses = tableWithBranch('expences')->where('date', '<=', $dateTo)->where('type', "=", "Expense")->sum('amount');
        $earning = tableWithBranch('expences')->where('date', '<=', $dateTo)->where('type', '=', 'Income')->where('reason', 'NOT LIKE', '%Other loan charges for loan number:%')->sum('amount');
        $otherchargers = tableWithBranch('expences')->where('date', '<=', $dateTo)->where('type', '=', 'Income')->where('reason', 'LIKE', '%Other loan charges for loan number:%')->sum('amount');
        $loan_interest = tableWithBranch('customer_loan')->where('Date_Time', '<=', $dateTo)->where('Status', 0)->sum('installment_balance');

        // Additional manual data array
        $additionalData = [
            ['acc_code' => '-', 'acc_name' => 'Loan Portfolio', 'total_debit' => $loan, 'total_credit' => 0.00],
            ['acc_code' => '-', 'acc_name' => 'Client Savings', 'total_debit' => 0.00, 'total_credit' => $client],
            ['acc_code' => '-', 'acc_name' => 'Interest Income (Loans)', 'total_debit' => 0.00, 'total_credit' => $interest_loan],
            ['acc_code' => '-', 'acc_name' => 'Loan Other Chargers', 'total_debit' => 0.00, 'total_credit' => $otherchargers],
            ['acc_code' => '-', 'acc_name' => 'Interest Payable (Savings)', 'total_debit' => $interest_saving, 'total_credit' => 0.00],
            ['acc_code' => '-', 'acc_name' => 'Other Earnings', 'total_debit' => 0.00, 'total_credit' => $earning],
            ['acc_code' => '-', 'acc_name' => 'Expenses', 'total_debit' => $expenses, 'total_credit' => 0.00],
            ['acc_code' => '-', 'acc_name' => 'Loan Interest Receivable', 'total_debit' => $loan_interest, 'total_credit' => 0.00],
            ['acc_code' => '-', 'acc_name' => 'Capital', 'total_debit' => 0.00, 'total_credit' => 0.00],
            ['acc_code' => '-', 'acc_name' => 'Retained Earnings', 'total_debit' => 0.00, 'total_credit' => 0.00],
        ];

        // Combine the results and the additional data
        $combinedData = $results->map(function ($item) {
            return [
                'acc_code' => $item->acc_code,  // Include acc_code
                'acc_name' => $item->acc_name,
                'total_debit' => $item->total_debit,
                'total_credit' => $item->total_credit,
            ];
        })->toArray();

        $finalData = array_merge($combinedData, $additionalData);

        // Return the combined data as JSON
        return response()->json($finalData);
    }

    public function BalanceSheetView(){
        $date_from = Carbon::now()->format('Y-m-d'); // Current date
        $date_to = Carbon::now()->format('Y-m-d'); // Current date
        $current_assets=[];
        $non_current_assets=[];
        $equity=[];
        $liabilities=[];
        return view('pages.Accounting.BalanceSheet', compact(
            'current_assets', 'non_current_assets', 'liabilities', 'equity', 'date_from', 'date_to'
        ));
    }

    public function BalanceSheet(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        // Fetch current assets
        $current_assets = [
            'cash_and_bank' => $this->getBalanceByAccountType("Cash and Bank", $date_to),
            'cash_on_hand' => tableWithBranch('company_bank_accounts')->where('Account_No', '=', 'Cash')->value('Account_Balance'),
            'bank' => tableWithBranch('company_bank_accounts')->where('Account_No','!=','Cash')->sum('Account_Balance'),
            'account_receivable' => $this->getBalanceByAccountType("Account Receivable", $date_to),
            'receivable' => $this->getBalanceByAccountType("Receivable", $date_to),
            'taxes_paid_on_purchase' => $this->getBalanceByAccountType("Taxes Paid on Purchase", $date_to),
            'current_asset' => $this->getBalanceByAccountType("Current Asset", $date_to),
        ];

        // Fetch non-current assets
        $non_current_assets = [
            'loan' => $this->getBalanceByAccountType("Loan", $date_to),
            'non_current_asset' => $this->getBalanceByAccountType("Non-Current Asset", $date_to),
        ];

        // Fetch liabilities
        $liabilities = [
            'accounts_payable' => $this->getBalanceByAccountType("Accounts Payable", $date_to),
            'accumulated_depreciation' => $this->getBalanceByAccountType("Accumulated Depreciation", $date_to),
            'investor_deposit' => $this->getBalanceByAccountType("Investor Deposit", $date_to),
            'borrower_saving_deposit' => $this->getBalanceByAccountType("Borrower Saving Deposit", $date_to),
            'payable' => $this->getBalanceByAccountType("Payable", $date_to),
            'taxes_received_on_sale' => $this->getBalanceByAccountType("Taxes Received on Sale", $date_to),
        ];

        // Fetch equity
        $equity = [
            'equity' => $this->getBalanceByAccountType("Equity", $date_to),
            'retained_earning' => $this->getBalanceByAccountType("Retained Earning", $date_to),
        ];

        return view('pages.Accounting.BalanceSheet', compact(
            'current_assets', 'non_current_assets', 'liabilities', 'equity', 'date_from', 'date_to'
        ));
    }




    public function getBalanceByAccountType($accountTypeId, $dateTo)
    {
        Log::info($accountTypeId);

        return tableWithBranch('chart_of_account','chart_of_account')
            ->join('manual_journal_has_amount', 'chart_of_account.code', '=', DB::raw("SUBSTRING_INDEX(manual_journal_has_amount.account, '-', 1)"))
            ->where('chart_of_account.acc_type', $accountTypeId)
            ->whereDate('manual_journal_has_amount.created_at', '<=', $dateTo) // Filter by date
            ->select(DB::raw('SUM(manual_journal_has_amount.debit_amount - manual_journal_has_amount.credit_amount) as balance'))
            ->value('balance'); // Get the calculated balance
    }









}
