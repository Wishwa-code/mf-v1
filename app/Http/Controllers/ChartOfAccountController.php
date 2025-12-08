<?php

namespace App\Http\Controllers;

use App\Services\BankBalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        $query = DB::table('company_bank_accounts as c')
            ->leftJoin('company_bank_accounts as p', 'c.primary_account', '=', 'p.Idbank')
            ->select(
                'c.*',
                DB::raw("COALESCE(p.Bank_Name, '-') as primary_account_name")
            );

        if ($group && $group !== 'all') {
            $query->where('c.acc_type_group', ucfirst($group));
        }

        if ($request->has('code') && !empty($request->code)) {
            $query->where('c.code', 'LIKE', '%' . $request->code . '%');
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('c.acc_name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->where('c.acc_type', 'LIKE', '%' . $request->type . '%');
        }
        if ($request->boolean('sub_only')) {
            $query->whereNotNull('c.primary_account')->where('c.primary_account', '!=', 0);
        }

        $query->where('c.branch_id','=',session('branch_id'));
        $data = $query->get();

        return response()->json($data);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company_banks = tableWithBranch('company_bank_accounts')
            ->get();

        return view('pages.Accounting.ChartOfAccount',compact('company_banks'));
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


        $isSubAccount=$request->isSubAccount;
        $primaryAccountSelect="0";
        if ($isSubAccount=="1"){
            $primaryAccountSelect=$request->primaryAccountSelect;
        }

        $tracking_no = $request->log_no;




        $user_id = (int)session('userid');
        $Bank = [
            'Bank_Type' => "ChartOfAccount",
            'code' => $request->input('code'),
            'Bank_Name' => $request->input('acc_name'),
            'Account_Name' => $request->input('acc_name'),
            'Account_No' => $request->input('code'),
            'Bank_Branch' => '-',
            'Account_Balance' => $request->input('opening_balance'),
            'type' => $request->input('acc_type'),
            'cashflow' => $request->input('cash_flow_type'),
            'acc_type_group' => $request->input('acc_type_group'),
            'primary_account' => $primaryAccountSelect,
            'tracking_no' => $tracking_no,
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
                'opening_balance' => $request->input('opening_balance'),
                'current_balance' => $request->input('opening_balance'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $insertedId = insertWithBranch('company_bank_accounts', $Bank);
            $this->bankLogController->index($insertedId,"Account Creation","-","-","credit",$request->input('opening_balance'),'-');
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
            Log::info($request->date);
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
                $dateTime = $request->date . ' ' . now()->format('H:i:s');
                if ($row['debit_amount']>0){
                    $this->bankLogController->index($bank_id->Idbank,"Manual Journal",$row['description'],"-","debit",$row['debit_amount'],'-','0','0',$dateTime);
                }

                if ($row['credit_amount']>0){
                    $this->bankLogController->index($bank_id->Idbank,"Manual Journal",$row['description'],"-","credit",$row['credit_amount'],'-','0','0',$dateTime);
                }
                $service = new BankBalanceService();
                $service->updateRunningBalance($bank_id->Idbank);

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
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save Manual Journal.']);
        }
    }

    // Fetch data for the tables
    public function fetch(Request $request)
    {
        // ✅ Step 1: Ensure columns exist (only runs once if missing)
        if (!Schema::hasColumn('manual_journal', 'reversed_at')) {
            Schema::table('manual_journal', function ($table) {
                $table->timestamp('reversed_at')->nullable()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('manual_journal', 'reversed_by')) {
            Schema::table('manual_journal', function ($table) {
                $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
            });
        }

        $query = tableWithBranch('manual_journal');

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

        // Only statuses we care about here
        $results = $query->whereIn('status', [1, 2])->get();

        $posted   = $results->where('status', 1)->values();
        $reversed = $results->where('status', 2)->values();

        return response()->json([
            'posted'   => $posted,
            'reversed' => $reversed,
        ]);
    }


    public function reverse(Request $request)
    {
        $request->validate(['id_manual_journal' => 'required|integer']);
        $id = (int)$request->id_manual_journal;

        return DB::transaction(function () use ($id) {
            // 1) Lock header
            $header = tableWithBranch('manual_journal','manual_journal')
                ->where('id_manual_journal', $id)
                ->lockForUpdate()
                ->first();

            if (!$header) {
                return response()->json(['status'=>'error','message'=>'Journal not found.'],404);
            }
            if ((int)$header->status === 2) {
                return response()->json(['status'=>'error','message'=>'This journal is already reversed.'],400);
            }

            // 2) Get lines (we’ll use them mainly to read account strings and debit/credit)
            $lines = tableWithBranch('manual_journal_has_amount','manual_journal_has_amount')
                ->where('id_manual_journal', $id)
                ->orderBy('id_manual_journal_has_amount')
                ->get();

            if ($lines->isEmpty()) {
                return response()->json(['status'=>'error','message'=>'No lines found to reverse.'],400);
            }

            // 3) Reverse bank transactions:
            //    We will look at each row’s `account` (format "BANKCODE-...") and post the
            //    opposite entry using your existing BankLogController + BankBalanceService.
            foreach ($lines as $row) {
                // Split the account string by '-' and get the first part as bank code
                $accountParts = explode('-', (string)$row->account);
                $firstNumber  = $accountParts[0] ?? null;

                if (!$firstNumber) {
                    // Skip if no recognizable bank id; optional: throw error instead
                    continue;
                }

                $bank = tableWithBranch('company_bank_accounts')
                    ->where('Idbank', '=', $firstNumber)
                    ->first();

                if (!$bank) {
                    // Skip silently; or handle as needed
                    continue;
                }

                // Opposite transactions
                $dateTime = ($header->date ?? now()->toDateString()).' '.now()->format('H:i:s');
                $desc = $row->description;

                // If original posted a debit, we now credit the same amount
                if ((float)$row->debit_amount > 0) {
                    $this->bankLogController->index(
                        $bank->Idbank,
                        "Manual Journal Reverse",
                        $desc,
                        "-",
                        "credit",                           // opposite of original debit
                        (float)$row->debit_amount,
                        '-',
                        '0',
                        '0',
                        $dateTime
                    );
                }

                // If original posted a credit, we now debit the same amount
                if ((float)$row->credit_amount > 0) {
                    $this->bankLogController->index(
                        $bank->Idbank,
                        "Manual Journal Reverse",
                        $desc,
                        "-",
                        "debit",                            // opposite of original credit
                        (float)$row->credit_amount,
                        '-',
                        '0',
                        '0',
                        $dateTime
                    );
                }

                // Recompute running balance for this bank
                $service = new BankBalanceService();
                $service->updateRunningBalance($bank->Idbank);
            }

            tableWithBranch('manual_journal','manual_journal')
                ->where('id_manual_journal', $id)
                ->update([
                    'status'      => 2,
                    'reversed_at' => now(),
                    'reversed_by' => auth()->id() ?? null,
                    'updated_at'  => now(),
                ]);

            return response()->json(['status'=>'success','message'=>'Journal reversed successfully.']);
        });
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
        $data = tableWithBranch('manual_journal', 'manual_journal')
            ->join('manual_journal_has_amount', 'manual_journal.id_manual_journal', '=', 'manual_journal_has_amount.id_manual_journal')
            ->join('company_bank_accounts', function ($join) {
                $join->on(DB::raw("SUBSTRING_INDEX(manual_journal_has_amount.account, '-', 1)"), '=', 'company_bank_accounts.Idbank');
            })
            ->where('manual_journal.id_manual_journal', $id)
            ->select(
                'manual_journal_has_amount.description',
                'manual_journal_has_amount.debit_amount',
                'manual_journal_has_amount.credit_amount',
                'manual_journal_has_amount.account',
                'company_bank_accounts.acc_type_group'
            )
            ->get();

        return response()->json($data);
    }




    public function fetchLedger($account, Request $request)
    {
        $query = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
            ->where('Bank_Account_Id', '=',$account) // Match records starting with accountCode
            ->orderBy('id');

        // Optional filters: date range takes precedence, then today filter; default is no filter
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        if ($start && $end) {
            // normalize to day bounds
            $startDT = Carbon::parse($start)->startOfDay();
            $endDT = Carbon::parse($end)->endOfDay();
            $query = $query->whereBetween('company_bank_has_log.Date_Time', [$startDT, $endDT]);
        } elseif ($request->has('todayfilter') && (string)$request->get('todayfilter') === '1') {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();
            $query = $query->whereBetween('company_bank_has_log.Date_Time', [$todayStart, $todayEnd]);
        }

        $data = $query->get();
        
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

        // Base query
        $query = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->leftjoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
            ->where('Bank_Account_Id', '=', $account_id)
            ->whereDate('Date_Time','<=', $date_to)
            ->orderBy('id');

        // If client explicitly asks for pagination, return Laravel paginator under 'item'
        // Keep backward compatibility: when not requested, return the original array
        $wantsPagination = $request->boolean('paginate') || $request->has('per_page');
        if ($wantsPagination) {
            $perPage = (int) $request->input('per_page', 25);
            // reasonable bounds
            if ($perPage < 1) { $perPage = 1; }
            if ($perPage > 200) { $perPage = 200; }

            $paginator = $query->paginate($perPage);
            return response()->json([
                'item' => $paginator,
            ]);
        }

        // Default: old behavior (no pagination)
        $data = $query->get();
        $data->transform(function ($item) {
            $item->account_name = $item->account_name ?? '-';
            return $item;
        });
        return response()->json($data);


    }




    public function BalanceSheetView()
    {
        $date_from = Carbon::now()->format('Y-m-d'); // Default: Current date
        $date_to   = Carbon::now()->format('Y-m-d'); // Default: Current date

        // Initialize all financial categories as empty arrays
        $revenue            = [];
        $expenses           = [];
        $current_assets     = [];
        $non_current_assets = [];
        $equity             = [];
        $liabilities        = [];
        $assets             = [];

        // Calculate Totals (Ensure total is `0` if dataset is empty)
        $total_revenue                 = 0;
        $total_expenses                = 0;
        $total_assets                  = 0;
        $total_liabilities             = 0;
        $total_equity                  = 0;
        $total_liabilities_and_equity  = 0;
        $final_result_float            = 0;

        return view('pages.Accounting.BalanceSheet', compact(
            'revenue',
            'expenses',
            'current_assets',
            'non_current_assets',
            'liabilities',
            'equity',
            'total_revenue',
            'total_expenses',
            'total_assets',
            'total_liabilities',
            'total_equity',
            'total_liabilities_and_equity',
            'date_from',
            'date_to',
            'assets',
            'final_result_float'
        ));
    }


    public function BalanceSheet(Request $request)
    {
        // 1) Decide the date_to
        $date_to = $request->date_to ?? now()->toDateString();

        // 2) Recalculate all bank balances ONCE per branch (if needed)
        //    This keeps your process, but avoids parallel recalcs / deadlocks.
        $this->recalcAllBankBalancesSafely();

        // 3) Call the profit function (unchanged)
        //    If profit() uses dates, make sure date_to is inside $request.
        $profitRequest = clone $request;
        $profitRequest->merge(['date_to' => $date_to]);

        $profitData = $this->profit($profitRequest);

        // 4) Initialize variables (your logic)
        $total_assets      = 0;
        $total_liabilities = 0;
        $total_equity      = 0;
        $assets            = [];
        $liabilities       = [];
        $equity            = [];

        // 5) Net Profit / Loss logic (your formula)
        $final_result = (
            $profitData['total_difference_revenue']
            + $profitData['interest']
            + $profitData['panelty']
            + $profitData['other_chargers']
            - $profitData['total_difference']
        );
        $final_result      = str_replace(',', '', $final_result);
        $final_result_float = floatval($final_result);

        // 6) Subquery to get the latest log for each account (your query)
        $latestLogs = tableWithBranch('company_bank_has_log as log1')
            ->select(DB::raw('MAX(log1.Id) as latest_log_id'))
            ->whereDate('log1.Date_Time', '<=', $date_to)
            ->groupBy('log1.Bank_Account_Id');

        // 7) Fetch account balances with latest logs (your query)
        $accountData = tableWithBranch('company_bank_accounts', 'company_bank_accounts')
            ->join('company_bank_has_log', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->joinSub($latestLogs, 'latest_logs', function ($join) {
                $join->on('company_bank_has_log.Id', '=', 'latest_logs.latest_log_id');
            })
            ->whereIn('company_bank_accounts.acc_type_group', ['Assets', 'Liabilities', 'Equity'])
            ->where('company_bank_accounts.Bank_Type', '!=', 'System_default_2')
            ->where('company_bank_accounts.Bank_Type', '!=', 'System_default_5')
            ->select(
                'company_bank_accounts.Idbank',
                'company_bank_accounts.Bank_Name',
                'company_bank_accounts.acc_type_group',
                'company_bank_accounts.primary_account',
                'company_bank_has_log.Balance'
            )
            ->get();

        // 8) Process accounts (your logic)
        foreach ($accountData as $item) {
            $entry = [
                'idbank'          => $item->Idbank,
                'name'            => $item->Bank_Name,
                'balance'         => floatval($item->Balance),
                'primary_account' => $item->primary_account ?? 0,
            ];

            switch ($item->acc_type_group) {
                case 'Assets':
                    $assets[]       = $entry;
                    $total_assets  += $entry['balance'];
                    break;

                case 'Liabilities':
                    $liabilities[]       = $entry;
                    $total_liabilities  += $entry['balance'];
                    break;

                case 'Equity':
                    $equity[]       = $entry;
                    $total_equity  += $entry['balance'];
                    break;
            }
        }

        // 9) Add Net Profit or Net Loss (your logic)
        if ($final_result_float > 0) {
            $liabilities[] = [
                'idbank'          => 'Net Profit',
                'name'            => 'Net Profit',
                'balance'         => $final_result_float,
                'primary_account' => 0
            ];
            $total_liabilities += $final_result_float;

        } elseif ($final_result_float < 0) {
            $assets[] = [
                'idbank'          => 'Net Loss',
                'name'            => 'Net Loss',
                'balance'         => abs($final_result_float),
                'primary_account' => 0
            ];
            $total_assets += abs($final_result_float);
        }

        // 10) Totals
        $total_liabilities_and_equity = $total_liabilities + $total_equity;

        // 11) Return view (your variables)
        return view('pages.Accounting.BalanceSheet', compact(
            'date_to',
            'assets',
            'liabilities',
            'equity',
            'total_assets',
            'total_liabilities',
            'total_equity',
            'total_liabilities_and_equity',
            'final_result',
            'final_result_float'
        ));
    }



    function recalcAllBankBalancesSafely()
    {
        $branchId = session('branch_id') ?? 'all';
        $lockKey  = "all_bank_balance_recalc_branch_{$branchId}";
        $lock     = Cache::lock($lockKey, 300); // 5 minutes

        if (! $lock->get()) {
            // Another request/background job is already doing full recalculation.
            // We skip here to avoid deadlock and heavy duplicate work.
            return;
        }

        try {
            $service = new BankBalanceService();

            // === YOUR ORIGINAL LOOP (KEPT) ===
            $bank = tableWithBranch('company_bank_accounts')->get();
            foreach ($bank as $banks) {
                $service->updateRunningBalance($banks->Idbank);
            }
            // =================================

        } catch (\Throwable $e) {
            // Log but don't break the Balance Sheet view completely
            \Log::error('recalcAllBankBalancesSafely failed: ' . $e->getMessage());
        } finally {
            try {
                if (isset($lock) && method_exists($lock, 'release')) {
                    $lock->release();
                }
            } catch (\Throwable $e) {
                // ignore lock release errors
            }
        }
    }




    public function profit(Request $request)
    {
        $date_from = '2010-08-20';
        $date_to = $request->date_to;
        $total_expenses = 0.00;

        $date_to_2 = Carbon::parse($date_to)->endOfDay();
        $date_from_2 = Carbon::parse($date_from)->startOfDay();

        // Calculate various values
        $bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_2')->first();

        $interest_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$bank->Idbank)
            ->sum('Credit');

        $interest_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$bank->Idbank)
            ->sum('Debit');

        $interest=$interest_Credit-$interest_Debit;


        $penelty_system=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_5')->first();

        $panelty_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$penelty_system->Idbank)
            ->where('company_bank_has_log.Type','!=','Penalty')
            ->sum('Credit');

        $panelty_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$penelty_system->Idbank)
            ->where('company_bank_has_log.Type','!=','Penalty')
            ->sum('Debit');

        $panelty=$panelty_Credit-$panelty_Debit;


        // Calculate various values
        $chargers=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_9')->first();

        $chargers_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$chargers->Idbank)
            ->sum('Credit');

        $chargers_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$chargers->Idbank)
            ->sum('Debit');

        $other_chargers = $chargers_Credit-$chargers_Debit;

        $total_income = tableWithBranch('expences')
            ->whereBetween('date', [$date_from_2, $date_to_2])
            ->where('reason', 'not like', '%Other loan charges for loan number:%')
            ->where('type', '=', 'Income')
            ->sum('amount');

        $system_expenses = tableWithBranch('company_bank_accounts', 'company_bank_accounts')
            ->join('company_bank_has_log', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('acc_type_group', '=', 'Expenses')
            ->whereBetween('company_bank_has_log.Date_Time', [$date_from_2, $date_to_2])
            ->select(
                'company_bank_accounts.Bank_Name',
                DB::raw("SUM(COALESCE(company_bank_has_log.Credit, 0)) as total_credit"),
                DB::raw("SUM(COALESCE(company_bank_has_log.Debit, 0)) as total_debit"),
                DB::raw("(SUM(COALESCE(company_bank_has_log.Debit, 0)) - SUM(COALESCE(company_bank_has_log.Credit, 0))) as balance_difference")
            )
            ->groupBy('company_bank_accounts.Bank_Name')
            ->havingRaw("balance_difference != 0") // Exclude zero balance difference
            ->orderByDesc('balance_difference') // Order by highest difference
            ->get();

        // Calculate the total balance difference from system_expenses
        $total_difference = $system_expenses->sum('balance_difference');

        $system_revenue = tableWithBranch('company_bank_accounts', 'company_bank_accounts')
            ->join('company_bank_has_log', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('company_bank_accounts.Bank_Type', '=', 'ChartOfAccount')
            ->where('acc_type_group', '=', 'Revenue')
            ->whereBetween('company_bank_has_log.Date_Time', [$date_from_2, $date_to_2])
            ->select(
                'company_bank_accounts.Bank_Name',
                DB::raw("SUM(COALESCE(company_bank_has_log.Credit, 0)) as total_credit"),
                DB::raw("SUM(COALESCE(company_bank_has_log.Debit, 0)) as total_debit"),
                DB::raw("(SUM(COALESCE(company_bank_has_log.Credit, 0)) - SUM(COALESCE(company_bank_has_log.Debit, 0))) as balance_difference")
            )
            ->groupBy('company_bank_accounts.Bank_Name')
            ->havingRaw("balance_difference != 0") // Exclude zero balance difference
            ->orderByDesc('balance_difference') // Order by highest difference
            ->get();

        $total_difference_revenue = $system_revenue->sum('balance_difference');

        // Return all the data to the BalanceSheet function
        return [
            'interest' => $interest,
            'panelty' => $panelty,
            'other_chargers' => $other_chargers,
            'total_income' => $total_income,
            'total_expenses' => $total_expenses,
            'total_difference' => $total_difference, // Add total_difference to the result
            'total_revenue' => $system_revenue, // Add total_difference to the result
            'total_difference_revenue' => $total_difference_revenue, // Add total_difference to the result
        ];
    }












}
