<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartOfAccountController extends Controller
{

    protected $bankLogController;

    public function __construct(BankLogController $bankLogController)
    {
        $this->bankLogController = $bankLogController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $group = null)
    {
        // Base query
        $query = tableWithBranch('company_bank_accounts');

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

//        $query->where('Bank_Type', '!=', 'Bank');
//        $query->where('Bank_Type', '!=', 'Collector');

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

        $user_id = (int)session('userid');
        $Bank = [
            'Bank_Type' => "ChartOfAccount",
            'code' => $request->input('code'),
            'Bank_Name' => $request->input('acc_name'),
            'Account_Name' => $request->input('acc_name'),
            'Account_No' => $request->input('code'),
            'Bank_Branch' => '-',
            'Account_Balance' => '0.00',
            'type' => $request->input('acc_type'),
            'cashflow' => $request->input('cash_flow_type'),
            'acc_type_group' => $request->input('acc_type_group'),
            'User' => $user_id,
        ];
        if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('code', '=', $request->input('code'))->exists()) {
            return response()->json(["id" => "0"], 200);
        } else {
            insertWithBranch('chart_of_account',[
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
            $insertedId = insertWithBranch('company_bank_accounts', $Bank);
            $this->bankLogController->index($insertedId,"Account Creation","-","-","credit","0.00",'-');
            return response()->json(['status' => 'success']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $chart_of_accounts = tableWithBranch('company_bank_accounts')->get();
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

                deleteWithBranch('manual_journal_has_amount','id_manual_journal', $journalId);
            } else {

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
                // Split the account string by '-' and get the first part
                $accountParts = explode('-', $row['account']);
                $firstNumber = $accountParts[0];  // Get the first part before '-'

                $bank_id=tableWithBranch('company_bank_accounts')
                    ->where('Idbank','=',$firstNumber)
                    ->first();

                if ($row['debit_amount']>0){
                    $this->bankLogController->index($bank_id->Idbank,"Manual Journal",$row['description'],"-","debit",$row['debit_amount'],'-');
                }

                if ($row['credit_amount']>0){
                    $this->bankLogController->index($bank_id->Idbank,"Manual Journal",$row['description'],"-","credit",$row['credit_amount'],'-');
                }
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
        // Fetch matching records from the `manual_journal_has_amount` table
        $data = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
            ->where('Bank_Account_Id', '=',$account) // Match records starting with accountCode
            ->orderBy('id')
            ->get();
        // Replace NULL values with '-'
        $data->transform(function ($item) {
            $item->account_name = $item->account_name ?? '-';
            return $item;
        });


        // Return data as JSON
        return response()->json($data);
    }


    public function getAccountTrialBalance(Request $request)
    {
        $dateFrom = Carbon::parse($request->input('date_from'))->toDateString() . ' 00:00:00';
        $dateTo = Carbon::parse($request->input('date_to'))->toDateString() . ' 23:59:59';


        // Fetch all bank accounts with their respective Account Names
        $bankAccounts = tableWithBranch('company_bank_accounts')->get();

        // Initialize an array to store additional data
        $additionalData = [];

        // Loop through each bank account and fetch the debit/credit sums for the respective Account_Name
        foreach ($bankAccounts as $bank) {
            $acc_name = $bank->Account_Name; // Account Name from the company_bank_accounts table
            $type = $bank->Bank_Type; // Bank Type from the company_bank_accounts table
            $bank_id = $bank->Idbank;
            $acc_type_group = $bank->acc_type_group;

            // Check if the Bank Type is one of the "System_default_X" types
            if (strpos($type, 'System_default') !== false) {
                $type = "System Generated";  // Custom name
            }

            if (strpos($type, 'ChartOfAccount') !== false) {
                $type = "Chart Of Account";  // Custom name
            }

            if (strpos($type, 'Bank') !== false) {
                $type = "Bank Account";  // Custom name
                // Append Bank details to Account Name
                $acc_name .= ' - ' . $bank->Bank_Name . ' (' . $bank->Account_No . ') - ' . $bank->Bank_Branch;
            }

            if (strpos($type, 'Collector') !== false) {
                $type = "Collector Account";  // Custom name
            }

            // Fetch the total debit and credit within the given date range for this account name
            $total_debit = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', '=', $bank_id)
                ->whereBetween('Date_Time', [$dateFrom, $dateTo])  // Filter by date range
                ->sum('Debit');

            $total_credit = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', '=', $bank_id)
                ->whereBetween('Date_Time', [$dateFrom, $dateTo])  // Filter by date range
                ->sum('Credit');

            // If no records found, set both debit and credit to 0
            if (!$total_debit) {
                $total_debit = 0;
            }

            if (!$total_credit) {
                $total_credit = 0;
            }

            // Add the account data to the array
            $additionalData[] = [
                'account_id' => $bank_id,
                'acc_name' => $acc_name,
                'acc_type' => $acc_type_group,
                'type' => $type,
                'total_debit' => $total_debit,
                'total_credit' => $total_credit
            ];
        }


        // Sort the array to prioritize "System Generated" first, then "Chart Of Account", then others
        usort($additionalData, function ($a, $b) {
            $order = ['Bank Account','Collector Account','System Generated', 'Chart Of Account', 'Expenses', 'Income'];

            // First, prioritize by type (System Generated, Chart Of Account)
            $aTypeRank = array_search($a['type'], $order) !== false ? array_search($a['type'], $order) : 2;
            $bTypeRank = array_search($b['type'], $order) !== false ? array_search($b['type'], $order) : 2;

            // If types are equal, leave the order unchanged
            if ($aTypeRank == $bTypeRank) {
                return 0;
            }

            // Otherwise, sort by rank (System Generated and Chart Of Account first)
            return $aTypeRank < $bTypeRank ? -1 : 1;
        });

        // Return the data as JSON response
        return response()->json($additionalData);
    }


    public function getLog(Request $request){
        $dateFrom = Carbon::parse($request->input('date_from'))->toDateString() . ' 00:00:00';
        $dateTo = Carbon::parse($request->input('date_to'))->toDateString() . ' 23:59:59';
        $account_id = $request->account_id;


        if (isset($request->account_id)){
            // Fetch matching records from the `manual_journal_has_amount` table
            $data = tableWithBranch('company_bank_has_log','company_bank_has_log')
                ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
                ->where('Bank_Account_Id', '=',$account_id) // Match records starting with accountCode
                ->whereBetween('Date_Time', [$dateFrom, $dateTo])  // Filter by date range
                ->orderBy('id')
                ->get();

            // Replace NULL values with '-'
            $data->transform(function ($item) {
                $item->account_name = $item->account_name ?? '-';
                return $item;
            });

            // Return data as JSON
            return response()->json($data);
        }

        // Fetch matching records from the `company_bank_has_log` table
        $data = tableWithBranch('company_bank_has_log', 'company_bank_has_log')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->whereBetween('company_bank_has_log.Date_Time', [$dateFrom, $dateTo])  // Filter by date range
            ->where(function ($query) {
                $query->where('company_bank_has_log.Credit', '>', 0)
                    ->orWhere('company_bank_has_log.Debit', '>', 0);
            })  // Ensure at least one of Credit or Debit is greater than 0
            ->orderBy('id')
            ->get();

// Replace NULL values with '-'
        $data->transform(function ($item) {
            $item->account_name = $item->account_name ?? '-';
            return $item;
        });
        // Return data as JSON
        return response()->json($data);


    }


    public function getBalanceSheetLog(Request $request){
        $date_to = $request->date_to ?? now()->toDateString(); // Default to today
        $account_id = $request->account_id;

        // Fetch matching records from the `manual_journal_has_amount` table
        $data = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->leftjoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
            ->where('Bank_Account_Id', '=',$account_id) // Match records starting with accountCode
            ->where('Date_Time','<=',$date_to) // Filter by date range
            ->orderBy('id')
            ->get();

        // Replace NULL values with '-'
        $data->transform(function ($item) {
            $item->account_name = $item->account_name ?? '-';
            return $item;
        });


        // Return data as JSON
        return response()->json($data);


    }




    public function BalanceSheetView()
    {
        $date_from = Carbon::now()->format('Y-m-d'); // Default: Current date
        $date_to = Carbon::now()->format('Y-m-d');   // Default: Current date

        // Initialize all financial categories as empty arrays
        $revenue = [];
        $expenses = [];
        $current_assets = [];
        $non_current_assets = [];
        $equity = [];
        $liabilities = [];

        // Calculate Totals (Ensure total is `0` if dataset is empty)
        $total_revenue =  0;
        $total_expenses =  0;
        $total_assets =  0;
        $total_liabilities =  0;
        $total_equity =  0;
        $total_liabilities_and_equity =  0;

        return view('pages.Accounting.BalanceSheet', compact(
            'revenue', 'expenses', 'current_assets', 'non_current_assets',
            'liabilities', 'equity', 'total_revenue', 'total_expenses',
            'total_assets', 'total_liabilities', 'total_equity',
            'total_liabilities_and_equity', 'date_from', 'date_to'
        ));
    }

    public function BalanceSheet(Request $request)
    {
        $date_to = $request->date_to ?? now()->toDateString(); // Default to today

        $system_expenses = tableWithBranch('company_bank_accounts', 'company_bank_accounts')
            ->join('company_bank_has_log AS log1', 'company_bank_accounts.Idbank', '=', 'log1.Bank_Account_Id')
            ->where('log1.Date_Time', '<=', $date_to)
            ->where('company_bank_accounts.Bank_Type', '!=', 'Collector')
            ->whereIn('company_bank_accounts.acc_type_group', ['Assets', 'Liabilities', 'Equity'])
            ->whereRaw('log1.Date_Time = (SELECT MAX(log2.Date_Time) FROM company_bank_has_log AS log2 WHERE log2.Bank_Account_Id = log1.Bank_Account_Id)')
            ->select(
                'company_bank_accounts.Idbank',
                'company_bank_accounts.acc_type_group',
                'company_bank_accounts.Bank_Name',
                \DB::raw('MAX(log1.Balance) as Balance'), // Get the latest balance
                \DB::raw('MAX(log1.type) as type') // Get the latest type
            )
            ->groupBy('company_bank_accounts.Idbank', 'company_bank_accounts.acc_type_group', 'company_bank_accounts.Bank_Name')
            ->get()
            ->groupBy('acc_type_group');

        // ✅ Ensure variables are always defined
        $total_assets = 0;
        $total_liabilities = 0;
        $total_equity = 0;

        // ✅ Define empty arrays for each category (Prevents Undefined Variable error)
        $assets = [];
        $liabilities = [];
        $equity = [];

        // ✅ Process data and categorize it with Idbank
        foreach ($system_expenses as $category => $items) {
            foreach ($items as $item) {
                switch ($category) {
                    case 'Assets':
                        $assets[] = [
                            'idbank' => $item->Idbank,
                            'name' => $item->Bank_Name,
                            'balance' => $item->Balance
                        ];
                        $total_assets += $item->Balance;
                        break;
                    case 'Liabilities':
                        $liabilities[] = [
                            'idbank' => $item->Idbank,
                            'name' => $item->Bank_Name,
                            'balance' => $item->Balance
                        ];
                        $total_liabilities += $item->Balance;
                        break;
                    case 'Equity':
                        $equity[] = [
                            'idbank' => $item->Idbank,
                            'name' => $item->Bank_Name,
                            'balance' => $item->Balance
                        ];
                        $total_equity += $item->Balance;
                        break;
                }
            }
        }



        $total_liabilities_and_equity = $total_liabilities + $total_equity;

        // ✅ Now these variables are always defined and passed to the view
        return view('pages.Accounting.BalanceSheet', compact(
            'date_to', 'assets', 'liabilities', 'equity',
            'total_assets', 'total_liabilities', 'total_equity', 'total_liabilities_and_equity'
        ));
    }









}
