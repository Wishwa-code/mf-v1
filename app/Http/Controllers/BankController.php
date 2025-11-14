<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{

    protected $bankLogController;
    protected $customerLogController;

    public function __construct(CustomerLogController $customerLogController,BankLogController $bankLogController)
    {
        $this->bankLogController = $bankLogController;
        $this->customerLogController = $customerLogController;
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = tableWithBranch('company_bank_accounts','company_bank_accounts')
            ->join('user', 'company_bank_accounts.User', '=', 'user.id')
            ->where('company_bank_accounts.Bank_Type','=','Bank')
            ->get();
        $company_banks = tableWithBranch('company_bank_accounts')
            ->where('company_bank_accounts.Bank_Type','=','Collector')
            ->get();

        return view('pages.BankAccount',compact('banks','company_banks'));
    }

    public function collector_index()
    {
        $banks = tableWithBranch('company_bank_accounts','company_bank_accounts')
            ->join('user', 'company_bank_accounts.User', '=', 'user.id')
            ->where('company_bank_accounts.Bank_Type', 'Collector')
            ->where(function ($q) {
                $q->where('user.collector', '1')
                    ->orWhere('user.cashier', '1');
            })
            ->get();

        $company_banks = DB::table('company_bank_accounts as c2')
            ->leftJoin('user', 'c2.User', '=', 'user.id')
            ->where(function ($q) {
                $q->where('c2.Bank_Type', 'Bank')
                    ->orWhere(function ($q2) {
                        $q2->where('c2.Bank_Type', 'Collector')
                            ->where('user.cashier', '1');
                    });
            })
            ->where('c2.branch_id','=',session('branch_id'))
            ->select([
                'c2.*',
                DB::raw("CASE WHEN c2.Bank_Type='Collector' AND user.cashier='1' THEN 'Bank' ELSE c2.Bank_Type END AS display_bank_type"),
            ])
            ->orderByRaw("display_bank_type ASC")   // 👈 order by your alias
            ->get();


        $user = DB::table('user')->where('id', session('userid'))->first();
        $collector = $user ? $user->collector : 0;

        return view('pages.CollectorAccount',compact('banks','company_banks','collector'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id, Request $request)
    {
        $query = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.contra_account')
            ->leftJoin('user', 'company_bank_has_log.User', '=', 'user.id')
            ->where('Bank_Account_Id', $id)
            ->select('company_bank_has_log.*', 'company_bank_accounts.Account_No', DB::raw("COALESCE(user.Full_Name, '-') as Full_Name"));

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

        $bank_log = $query->get();


        // Replace NULL values with '-'
        $bank_log->transform(function ($item) {
            $item->account_name = $item->account_name ?? '-';
            return $item;
        });


        return response()->json(["item" => $bank_log], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = (int)session('userid');
        $Bank = [
            'Bank_Type' => "Bank",
            'code' => $request->bank_code,
            'Bank_Name' => $request->bank_name,
            'Account_Name' => $request->account_name,
            'Account_No' => $request->account_number,
            'Bank_Branch' => $request->branch,
            'Account_Balance' => $request->opening_balance,
            'type' => "Cash and Bank",
            'cashflow' => "Non Applicable",
            'acc_type_group' => "Assets",
            'User' => $user_id,
        ];

        if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $request->account_number)->exists()) {
            return response()->json(["id" => "0"], 200);
        }else if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('code', '=', $request->bank_code)->exists()) {
            return response()->json(["id" => "0"], 200);
        } else {
            $insertedId = insertWithBranch('company_bank_accounts', $Bank);
            $this->bankLogController->index($insertedId,"Account Creation","-","-","credit",$request->opening_balance,'-');
            return response()->json(["id" => "1"], 200);
        }




    }

    /**
     * Display the specified resource.
     */
    public function show()
    {

        $user_id = (int)session('userid');
        $isHeadOffice = (int)session('branch_id') === -1;

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();

        if ($collector_val) {
            $collector = $collector_val->collector;
            $cashier = $collector_val->cashier;

            if ($collector == 1 || $cashier == 1) {
                // Logged-in user is a collector or cashier
                $banks = tableWithBranch('company_bank_accounts')
                    ->where('Account_No', '=', $user_id)
                    ->get();

                // Exclude this account from $banks_2
                if ($isHeadOffice) {
                    // Head office can see ALL branches
                    $banks_2 = DB::table('company_bank_accounts')
                        ->where(function($query) {
                            $query->where('Bank_Type', '=', 'Bank')
                                ->orWhere('Bank_Type', '=', 'Collector');
                        })
                        ->where('Account_No', '!=', $user_id)
                        ->get();
                } else {
                    $banks_2 = tableWithBranch('company_bank_accounts')
                        ->where(function($query) {
                            $query->where('Bank_Type', '=', 'Bank')
                                ->orWhere('Bank_Type', '=', 'Collector');
                        })
                        ->where('Account_No', '!=', $user_id)
                        ->get();
                }

            } else {
                // Normal access - include everything in both
                // FROM account: always current branch only
                $banks = tableWithBranch('company_bank_accounts')
                    ->where(function($query) {
                        $query->where('Bank_Type', '=', 'Bank')
                            ->orWhere('Bank_Type', '=', 'Collector');
                    })
                    ->get();

                // TO account: head office sees all branches, others see same as FROM
                if ($isHeadOffice) {
                    $banks_2 = DB::table('company_bank_accounts')
                        ->where(function($query) {
                            $query->where('Bank_Type', '=', 'Bank')
                                ->orWhere('Bank_Type', '=', 'Collector');
                        })
                        ->get();
                } else {
                    $banks_2 = clone $banks;
                }
            }
        }

        // Fetch all branches for head office branch selector
        $branches = [];
        if ($isHeadOffice) {
            $branches = DB::table('branch')->where('status', 1)->get();
        }

        // Fetch bank logs with a join to company_bank_accounts, scoped by branch
        $banklog = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->join('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('company_bank_has_log.Type', '=', 'InterBank Transfer')
            ->get();

        return view('pages.Accounting.InnerBankTransfers',compact('banks','banklog','banks_2','branches','isHeadOffice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $fromBank   = $request->fromBank;
        $fromAmount = $request->fromAmount;
        $reason     = $request->reason;
        $toBank     = $request->toBank;

        $isHeadOffice = (int)session('branch_id') === -1;

        // For head office, don't filter by branch
        if ($isHeadOffice) {
            $fromBankDetails = DB::table('company_bank_accounts')
                ->where('Idbank', $fromBank)
                ->first();
        } else {
            $fromBankDetails = tableWithBranch('company_bank_accounts')
                ->where('Idbank', $fromBank)
                ->first();
        }

        if (!$fromBankDetails) {
            return response()->json(['error' => 'Invalid FROM bank account', 'id' => 0], 422);
        }

        // ====== POLICY CHECK (strict vs flexible) ======
        // read policy: strict vs flexible
        $app_settings = DB::table('app_settings')
            ->where('key', '=', 'loan_disbursement_policy')
            ->first();

        $loan_disbursement_policy = $app_settings->value ?? 'flexible';

        if ($loan_disbursement_policy === 'strict') {
            // Use the already loaded $fromBankDetails as "bank"
            if ($fromBankDetails->Account_Balance < $fromAmount) {
                return response()->json([
                    'error' => 'Bank Balance is not enough',
                    'id'    => 0
                ], 200);
            }
        }
        // ====== END POLICY CHECK ======

        // For head office, don't filter by branch
        if ($isHeadOffice) {
            $toBankDetails = DB::table('company_bank_accounts')
                ->where('Idbank', $toBank)
                ->first();
        } else {
            $toBankDetails = tableWithBranch('company_bank_accounts')
                ->where('Idbank', $toBank)
                ->first();
        }

        if (!$toBankDetails) {
            return response()->json(['error' => 'Invalid TO bank account', 'id' => 0], 422);
        }

        // Check if this is an inter-branch transfer (Head Office to Branch or vice versa)
        $isInterBranchTransfer =
            ($fromBankDetails->branch_id == -1 && $toBankDetails->branch_id != -1) ||
            ($fromBankDetails->branch_id != -1 && $toBankDetails->branch_id == -1);

        if ($isInterBranchTransfer && $fromBankDetails->branch_id == -1) {
            // Transfer from Head Office to Branch
            $targetBranchId = $toBankDetails->branch_id;
            $branchName = DB::table('branch')
                ->where('branch_id', $targetBranchId)
                ->value('Name');

            // Step 1: Check if Head Office has "Inter-Branch Transfer - [BranchName]" account
            $interBranchAccountName = "Inter-Branch Transfer - " . $branchName;
            $interBranchAccount = DB::table('company_bank_accounts')
                ->where('branch_id', -1)
                ->where('Account_Name', $interBranchAccountName)
                ->first();

            // If not exists, create it
            if (!$interBranchAccount) {
                $interBranchAccountId = DB::table('company_bank_accounts')->insertGetId([
                    'branch_id'        => -1,
                    'Bank_Type'        => 'Bank',
                    'code'             => 'IBT-' . $targetBranchId,
                    'Bank_Name'        => 'Inter-Branch Transfer',
                    'Account_Name'     => $interBranchAccountName,
                    'Account_No'       => 'IBT-' . $targetBranchId . '-' . time(),
                    'Bank_Branch'      => 'Head Office',
                    'Account_Balance'  => 0,
                    'type'             => 'Cash and Bank',
                    'cashflow'         => 'Non Applicable',
                    'acc_type_group'   => 'Assets',
                    'User'             => session('userid')
                ]);
            } else {
                $interBranchAccountId = $interBranchAccount->Idbank;
            }

            // Step 2: Make first double entry at Head Office
            // DEBIT: Inter-Branch Transfer account, CREDIT: Selected FROM account
            $this->bankLogController->index(
                $interBranchAccountId,
                "InterBank Transfer",
                'To ' . $branchName . ' Branch (' . $toBankDetails->Account_No . ')',
                $reason,
                "debit",
                $fromAmount,
                $fromBank
            );

            $this->bankLogController->index(
                $fromBank,
                "InterBank Transfer",
                'To ' . $branchName . ' Branch (' . $toBankDetails->Account_No . ')',
                $reason,
                "credit",
                $fromAmount,
                $interBranchAccountId
            );

            // Step 3: Check if Branch has "Head Office Transfer" account
            $headOfficeTransferAccount = DB::table('company_bank_accounts')
                ->where('branch_id', $targetBranchId)
                ->where('Account_Name', 'Head Office Transfer')
                ->first();

            // If not exists, create it
            if (!$headOfficeTransferAccount) {
                $headOfficeTransferAccountId = DB::table('company_bank_accounts')->insertGetId([
                    'branch_id'        => $targetBranchId,
                    'Bank_Type'        => 'Bank',
                    'code'             => 'HOT-' . $targetBranchId,
                    'Bank_Name'        => 'Head Office Transfer',
                    'Account_Name'     => 'Head Office Transfer',
                    'Account_No'       => 'HOT-' . $targetBranchId . '-' . time(),
                    'Bank_Branch'      => $branchName,
                    'Account_Balance'  => 0,
                    'type'             => 'Cash and Bank',
                    'cashflow'         => 'Non Applicable',
                    'acc_type_group'   => 'Assets',
                    'User'             => session('userid')
                ]);
            } else {
                $headOfficeTransferAccountId = $headOfficeTransferAccount->Idbank;
            }

            // Step 4: Make second double entry at Branch
            // DEBIT: Selected TO account, CREDIT: Head Office Transfer account
            $this->bankLogController->index(
                $toBank,
                "InterBank Transfer",
                'From Head Office (' . $fromBankDetails->Account_No . ')',
                $reason,
                "debit",
                $fromAmount,
                $headOfficeTransferAccountId
            );

            $this->bankLogController->index(
                $headOfficeTransferAccountId,
                "InterBank Transfer",
                'From Head Office (' . $fromBankDetails->Account_No . ')',
                $reason,
                "credit",
                $fromAmount,
                $toBank
            );

        } else {
            // Normal transfer within same branch or not inter-branch
            $this->bankLogController->index(
                $fromBank,
                "InterBank Transfer",
                'To (' . $toBankDetails->Account_No . ')',
                $reason,
                "credit",
                $fromAmount,
                $toBank
            );

            $this->bankLogController->index(
                $toBank,
                "InterBank Transfer",
                'From (' . $fromBankDetails->Account_No . ')',
                $reason,
                "debit",
                $fromAmount,
                $fromBank
            );
        }

        return response()->json(["id" => "1"], 200);
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
        // Retrieve the current status of the customer
        $bank = tableWithBranch('company_bank_accounts')->where('Id', $id)->first();


        // Check if the customer exists
        if ($bank) {
            // Toggle the status
            $newStatus = ($bank->status == 1) ? 0 : 1;

            // Update the status in the database
            updateWithBranch('company_bank_accounts', 'Id', $id, ['status' => $newStatus]);


            // Optionally, return a response
            return response()->json(['id' => '1'], 200);
        } else {
            // Return an error response if the customer is not found
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }



    public function chq(Request $request){
        $query = tableWithBranch('Cheque_payment','Cheque_payment')
            ->join('company_bank_accounts', 'Cheque_payment.bank_account_company', '=', 'company_bank_accounts.Idbank')
            ->join('customer_loan', 'Cheque_payment.loan_id', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter');

        if ($request->has('date')) {
            $query->whereDate('Cheque_payment.date', $request->date);
        }
        
        // date range
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('Cheque_payment.date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('Cheque_payment.date', '<=', $request->end_date);
        }

        // filters
        if ($request->has('product_id') && !empty($request->product_id)) {
            $query->where('loan_category.idLoan_Category', $request->product_id);
        }
        if ($request->has('loan_number') && !empty($request->loan_number)) {
            $query->where('customer_loan.Loan_No', 'LIKE', '%'.$request->loan_number.'%');
        }
        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $query->where('customer.idCustomer', $request->customer_id);
        }
        if ($request->has('chq_number') && !empty($request->chq_number)) {
            $query->where('Cheque_payment.chq_number', 'LIKE', '%'.$request->chq_number.'%');
        }

        if ($request->has('center_id') && !empty($request->center_id)) {
            $query->where('center.idCenter', $request->center_id);
        }

        // NEW: status filter ("0" Pending, "1" Proceeded, "-1" Returned "-2" Cancelled)
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('Cheque_payment.chq_status', (string)$request->status);
        }

        $chq = $query->select(
                'Cheque_payment.*',
                'company_bank_accounts.Bank_Name',
                'company_bank_accounts.Account_Name',
                'company_bank_accounts.Account_No',
                'customer_loan.Loan_No',
                'customer.cus_number',
                'customer.First_Name',
                'customer.Last_Name',
                'loan_category.Name as product_name',
                'center.No as center_no',
                'center.Name as center_name'
            )
            ->orderBy('Cheque_payment.date', 'desc')->get();

        // options for dropdowns
        $loan_categories = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->select('idCustomer', 'First_Name', 'Last_Name', 'cus_number')->get();
        $centers = tableWithBranch('center')->select('idCenter', 'No', 'Name')->get();

        return view('pages.ChqDetails', compact('chq','loan_categories','customers','centers'));
    }

    public function chq_process(string $id){

        $chq = tableWithBranch('cheque_details')
            ->where('Id', '=', $id)
            ->first();

        if ($chq) {
            updateWithBranch('cheque_details', 'Id', $id, ['Status' => '1']);
            return response()->json(['id' => '1'], 200);
        }

    }


    public function return_chq(string $id){

        $chq = tableWithBranch('Cheque_payment')
            ->where('idChq', '=', $id)
            ->first();
        $user_id = (int)session('userid');
        if ($chq) {
            updateWithBranch('Cheque_payment', 'idChq', $id, ['chq_status' => '-1']);
            $chq_comment='Cheque Returned ! Cheque No : '.$chq->chq_number.' Cheque Date : '.$chq->chq_date.' Cheque Type : '.$chq->chq_type.' Amount : '.$chq->payment_amount;
            $comment_id=insertWithBranch('loan_comment',[
                'comment' => $chq_comment,
                'loan_id' => $chq->loan_id,
                'user_id' => $user_id,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);
            $loan = tableWithBranch('customer_loan')
                ->where('idCustomer_Loan', '=', $chq->loan_id)
                ->first();
            $customer = tableWithBranch('customer')
                ->where('idCustomer', '=', $loan->Customer_idCustomer)
                ->first();

            $request = new Request([
                'customer_id' => $loan->Customer_idCustomer,
                'description' => 'Payment Rejected ('.$customer->First_Name.' '.$customer->Last_Name.')',
                'description_id' => $comment_id,
                'comment' =>$chq_comment,
                'type' => 'Loan Comment',
            ]);
            $this->customerLogController->store($request);
            return response()->json(['id' => '1'], 200);
        }

    }

    public function cancel_chq(string $id){

        $chq = tableWithBranch('Cheque_payment')
            ->where('idChq', '=', $id)
            ->first();
        $user_id = (int)session('userid');
        if ($chq) {
            updateWithBranch('Cheque_payment', 'idChq', $id, ['chq_status' => '-2']);
            $chq_comment='Cheque Cancelled ! Cheque No : '.$chq->chq_number.' Cheque Date : '.$chq->chq_date.' Cheque Type : '.$chq->chq_type.' Amount : '.$chq->payment_amount;
            $comment_id=insertWithBranch('loan_comment',[
                'comment' => $chq_comment,
                'loan_id' => $chq->loan_id,
                'user_id' => $user_id,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);
            $loan = tableWithBranch('customer_loan')
                ->where('idCustomer_Loan', '=', $chq->loan_id)
                ->first();
            $customer = tableWithBranch('customer')
                ->where('idCustomer', '=', $loan->Customer_idCustomer)
                ->first();

            $request = new Request([
                'customer_id' => $loan->Customer_idCustomer,
                'description' => 'Payment Cancelled ('.$customer->First_Name.' '.$customer->Last_Name.')',
                'description_id' => $comment_id,
                'comment' =>$chq_comment,
                'type' => 'Loan Comment',
            ]);
            $this->customerLogController->store($request);
            return response()->json(['id' => '1'], 200);
        }

    }

    public function profitView(){
        $date_from = date('Y-m-d'); // Current date
        $date_to = date('Y-m-d'); // Current date
        $interest=0.00;
        $panelty=0.00;
        $interest_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_2')->first();
        $penelty_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_5')->first();
        $chargers_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_9')->first();
        $other_chargers=0.00;
        $loan_expenses=0.00;
        $total_income=0.00;
        $total_expenses=0.00;
        $system_expenses=[];
        $system_revenue=[];
        return view('pages.Accounting.ProfitLoss',compact('date_from','system_expenses','date_to','interest','panelty','other_chargers','loan_expenses','total_income','total_expenses','system_revenue','interest_bank','penelty_bank','chargers_bank'));
    }


    public function profit(Request $request){
        $date_from=$request->date_from;
        $date_to=$request->date_to;
        $loan_expenses=0.00;
        $total_expenses=0.00;

        $date_to_2 = Carbon::parse($date_to)->endOfDay();
        $date_from_2 = Carbon::parse($date_from)->startOfDay(); // To ensure you're starting from the beginning of the day

        // Calculate various values
        $interest_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_2')->first();

        $interest_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$interest_bank->Idbank)
            ->sum('Credit');

        $interest_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$interest_bank->Idbank)
            ->sum('Debit');

        $interest=$interest_Credit-$interest_Debit;


        $penelty_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_5')->first();

        $panelty_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$penelty_bank->Idbank)
            ->where('company_bank_has_log.Type','!=','Penalty')
            ->sum('Credit');

        $panelty_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$penelty_bank->Idbank)
            ->where('company_bank_has_log.Type','!=','Penalty')
            ->sum('Debit');

        $panelty=$panelty_Credit-$panelty_Debit;


        // Calculate various values
        $chargers_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_9')->first();

        $chargers_Credit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$chargers_bank->Idbank)
            ->sum('Credit');

        $chargers_Debit = tableWithBranch('company_bank_has_log','company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from_2, $date_to_2])
            ->where('company_bank_has_log.Bank_Account_Id','=',$chargers_bank->Idbank)
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
                'company_bank_accounts.idbank',
                DB::raw("SUM(COALESCE(company_bank_has_log.Credit, 0)) as total_credit"),
                DB::raw("SUM(COALESCE(company_bank_has_log.Debit, 0)) as total_debit"),
                DB::raw("(SUM(COALESCE(company_bank_has_log.Debit, 0)) - SUM(COALESCE(company_bank_has_log.Credit, 0))) as balance_difference")
            )
            ->groupBy('company_bank_accounts.Bank_Name','company_bank_accounts.idbank')
            ->havingRaw("balance_difference != 0") // Exclude zero balance difference
            ->orderByDesc('balance_difference') // Order by highest difference
            ->get();


        $system_revenue = tableWithBranch('company_bank_accounts', 'company_bank_accounts')
            ->join('company_bank_has_log', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('acc_type_group', '=', 'Revenue')
            ->where('company_bank_accounts.Bank_Type', '=', 'ChartOfAccount')
            ->whereBetween('company_bank_has_log.Date_Time', [$date_from_2, $date_to_2])
            ->select(
                'company_bank_accounts.Bank_Name',
                'company_bank_accounts.idbank',
                DB::raw("SUM(COALESCE(company_bank_has_log.Credit, 0)) as total_credit"),
                DB::raw("SUM(COALESCE(company_bank_has_log.Debit, 0)) as total_debit"),
                DB::raw("(SUM(COALESCE(company_bank_has_log.Credit, 0)) - SUM(COALESCE(company_bank_has_log.Debit, 0))) as balance_difference")
            )
            ->groupBy('company_bank_accounts.Bank_Name','company_bank_accounts.idbank')
            ->havingRaw("balance_difference != 0") // Exclude zero balance difference
            ->orderByDesc('balance_difference') // Order by highest difference
            ->get();



        return view('pages.Accounting.ProfitLoss',compact('date_from','system_expenses','date_to','interest','panelty','other_chargers','loan_expenses','total_income','total_expenses','system_revenue','interest_bank','penelty_bank','chargers_bank'));
    }

    public function profitLog(Request $request){
        $date_from=$request->date_from;
        $date_to=$request->date_to;

        $date_to = Carbon::parse($date_to)->endOfDay();
        $date_from = Carbon::parse($date_from)->startOfDay(); // To ensure you're starting from the beginning of the day



        $interest = tableWithBranch('Loan_Log', 'Loan_Log')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'Loan_Log.Loan_ID')
            ->join('loan_category', 'loan_category.idLoan_Category', '=', 'customer_loan.Loan_Category_idLoan_Category')
            ->whereBetween('Loan_Log.Date_Time', [$date_from, $date_to])
            ->groupBy('loan_category.idLoan_Category', 'loan_category.Name') // Group only by category
            ->select(
                DB::raw('GROUP_CONCAT(DISTINCT customer_loan.Loan_No ORDER BY customer_loan.Loan_No ASC SEPARATOR ", ") as Loan_No'), // Concatenates loan numbers
                'loan_category.Name',
                DB::raw('SUM(Loan_Log.Interest_Payment) as total_interest')
            )
            ->havingRaw('total_interest > 0') // Apply HAVING instead of WHERE
            ->get();


        return response()->json($interest);

    }


    public function BankReconciliationView(){
        $date_from = Carbon::now()->format('Y-m-d'); // Current date
        $date_to = Carbon::now()->format('Y-m-d'); // Current date
        $bank_details="all";
        $bank = tableWithBranch('company_bank_accounts')->get();

        $bank_log = tableWithBranch('company_bank_has_log')
            ->whereBetween('Date_Time', [$date_from, $date_to])
            ->get();

// Get the opening balance (the last balance before the date_from)
        $opening_balance_record = tableWithBranch('company_bank_has_log')
            ->where('Date_Time', '<', $date_from)
            ->orderBy('Date_Time', 'desc')
            ->first(); // Get the last record before the date_from

        $opening_balance = $opening_balance_record ? $opening_balance_record->Balance : 0; // Use 0 if no record exists

// Get the closing balance (the balance of the last transaction on or before date_to)
        $closing_balance_record = tableWithBranch('company_bank_has_log')
            ->where('Date_Time', '<=', $date_to)
            ->orderBy('Date_Time', 'desc')
            ->first(); // Get the last record on or before the date_to


        $closing_balance = $closing_balance_record ? $closing_balance_record->Balance : 0; // Use 0 if no record exists

        return view('pages.Accounting.BankReconsilation', compact('bank_details','date_from', 'date_to', 'bank', 'bank_log', 'opening_balance', 'closing_balance'));
    }

    public function BankReconciliation(Request $request){
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $bank_details=$request->bank;
        $bank=tableWithBranch('company_bank_accounts')->get();

        if ($bank_details=="all"){
            $bank_log = tableWithBranch('company_bank_has_log')
                ->whereBetween('Date_Time', [$date_from, $date_to])
                ->get();

// Get the opening balance (the last balance before the date_from)
            $opening_balance_record = tableWithBranch('company_bank_has_log')
                ->where('Date_Time', '<', $date_from)
                ->orderBy('Date_Time', 'desc')
                ->first(); // Get the last record before the date_from

            $opening_balance = $opening_balance_record ? $opening_balance_record->Balance : 0; // Use 0 if no record exists

// Get the closing balance (the balance of the last transaction on or before date_to)
            $closing_balance_record = tableWithBranch('company_bank_has_log')
                ->where('Date_Time', '<=', $date_to)
                ->orderBy('Date_Time', 'desc')
                ->first(); // Get the last record on or before the date_to


            $closing_balance = $closing_balance_record ? $closing_balance_record->Balance : 0; // Use 0 if no record exists
        }else{

            $bank_log = tableWithBranch('company_bank_has_log')
                ->whereBetween('Date_Time', [$date_from, $date_to])
                ->where('Bank_Account_Id', '=', $bank_details)
                ->get();

// Get the opening balance (the last balance before the date_from)
            $opening_balance_record = tableWithBranch('company_bank_has_log')
                ->where('Date_Time', '<', $date_from)
                ->where('Bank_Account_Id', '=', $bank_details)
                ->orderBy('Date_Time', 'desc')
                ->first(); // Get the last record before the date_from

            $opening_balance = $opening_balance_record ? $opening_balance_record->Balance : 0; // Use 0 if no record exists

// Get the closing balance (the balance of the last transaction on or before date_to)
            $closing_balance_record = tableWithBranch('company_bank_has_log')
                ->where('Date_Time', '<=', $date_to)
                ->where('Bank_Account_Id', '=', $bank_details)
                ->orderBy('Date_Time', 'desc')
                ->first(); // Get the last record on or before the date_to


            $closing_balance = $closing_balance_record ? $closing_balance_record->Balance : 0; // Use 0 if no record exists
        }


        return view('pages.Accounting.BankReconsilation', compact('bank_details','date_from', 'date_to', 'bank', 'bank_log', 'opening_balance', 'closing_balance'));
    }

    public function loanStatusView() {

        $customer = tableWithBranch('customer')
            ->get();
        $lending_officer = tableWithBranch('user')->where('lending_officer','=','1')->get();
        $recovery_officer = tableWithBranch('user')->where('collector','=','1')->get();

        // Fetch active loans
        $loans = tableWithBranch('customer_loan')
            ->where('Status', '=', '0')
            ->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $center = tableWithBranch('center')->get();
        $group = tableWithBranch('customer_group')->get();

        // Initialize variables
        $current_loan_capital_amount = 0.00;
        $current_loan_interest_amount = 0.00;
        $current_loan_panelty_amount = 0.00;
        $Capital_Payment = 0.00;
        $Interest_Payment = 0.00;
        $Panelty_Payment = 0.00;

        // Initialize variables
        $past_capital_amount = 0.00;
        $past_loan_interest_amount = 0.00;
        $past_panelty_amount = 0.00;
        $past_Capital_Payment = 0.00;
        $past_Interest_Payment = 0.00;
        $past_Panelty_Payment = 0.00;

        foreach ($loans as $loan) {
            // Fetch the most recent installment
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderBy('idInstallments', 'desc')
                ->first();


            // Check if the maturity date is greater than the current date
            $Maturity_Date = $installment->Installment_Date ?? null;
            if ($Maturity_Date && $Maturity_Date > date('Y-m-d')) {
                // Calculate capital amount
                $current_loan_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount
                $current_loan_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $current_loan_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $Capital_Payment += $loan_payments->Capital_Payment ?? 0;
                $Interest_Payment += $loan_payments->Interest_Payment ?? 0;
                $Panelty_Payment += $loan_payments->Panelty_Payment ?? 0;
            }else{
                // Calculate capital amount
                $past_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount
                $past_loan_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $past_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $past_loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $past_Capital_Payment += $past_loan_payments->Capital_Payment ?? 0;
                $past_Interest_Payment += $past_loan_payments->Interest_Payment ?? 0;
                $past_Panelty_Payment += $past_loan_payments->Panelty_Payment ?? 0;
            }
        }

        // Calculate total amounts
        $current_loan_total = $current_loan_capital_amount + $current_loan_interest_amount + $current_loan_panelty_amount;
        $current_loan_total_payment = $Capital_Payment + $Interest_Payment + $Panelty_Payment;

        // Calculate total amounts
        $past_total = $past_capital_amount + $past_loan_interest_amount + $past_panelty_amount;
        $past_total_payment = $past_Capital_Payment + $past_Interest_Payment + $past_Panelty_Payment;












        // Fetch active loans
        $loans = tableWithBranch('customer_loan')
            ->where('Status', '=', '1')
            ->get();


        // Initialize variables
        $fully_paid_capital_amount = 0.00;
        $fully_paid_interest_amount = 0.00;
        $fully_paid_panelty_amount = 0.00;
        $fully_paid_Capital_Payment = 0.00;
        $fully_paid_Interest_Payment = 0.00;
        $fully_paid_Panelty_Payment = 0.00;

        foreach ($loans as $loan) {
            // Fetch the most recent installment
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderBy('idInstallments', 'desc')
                ->first();


            // Check if the maturity date is greater than the current date
            $Maturity_Date = $installment->Installment_Date ?? null;
            if ($Maturity_Date && $Maturity_Date > date('Y-m-d')) {
                // Calculate capital amount
                // Calculate fully paid capital amount
                $fully_paid_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount for fully paid loans
                $fully_paid_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $fully_paid_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $fully_paid_Capital_Payment += $loan_payments->Capital_Payment ?? 0;
                $fully_paid_Interest_Payment += $loan_payments->Interest_Payment ?? 0;
                $fully_paid_Panelty_Payment += $loan_payments->Panelty_Payment ?? 0;
            }
        }

        // Calculate total amounts
        $fully_paid_total = $fully_paid_capital_amount + $fully_paid_interest_amount + $fully_paid_panelty_amount;
        $fully_paid_total_payment = $fully_paid_Capital_Payment + $fully_paid_Interest_Payment + $fully_paid_Panelty_Payment;

        $selectedCustomer=0;
        $selectedLendingOfficer=0;
        $selectedRecoveryOfficer=0;

        $selectedRoute = 0;
        $selectedCenter = 0;
        $selectedGroup = 0;


        // Return data to view
        return view('pages.Accounting.loanStatus', compact(
            'route','center','group','selectedRoute','selectedCenter','selectedGroup',

            'selectedCustomer',
            'selectedLendingOfficer',
            'selectedRecoveryOfficer',
            'customer',
            'lending_officer',
            'recovery_officer',

            'current_loan_capital_amount',
            'current_loan_interest_amount',
            'current_loan_panelty_amount',
            'current_loan_total',
            'Capital_Payment',
            'Interest_Payment',
            'Panelty_Payment',
            'current_loan_total_payment',


            'past_capital_amount',
            'past_loan_interest_amount',
            'past_panelty_amount',
            'past_total',
            'past_Capital_Payment',
            'past_Interest_Payment',
            'past_Panelty_Payment',
            'past_total_payment',


            'fully_paid_capital_amount',
            'fully_paid_interest_amount',
            'fully_paid_panelty_amount',
            'fully_paid_total',
            'fully_paid_Capital_Payment',
            'fully_paid_Interest_Payment',
            'fully_paid_Panelty_Payment',
            'fully_paid_total_payment'
        ));
    }





    public function loanStatusView_2(Request $request) {

        $customer = tableWithBranch('customer')
            ->get();
        $lending_officer = tableWithBranch('user')->where('lending_officer','=','1')->get();
        $recovery_officer = tableWithBranch('user')->where('collector','=','1')->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $center = tableWithBranch('center')->get();
        $group = tableWithBranch('customer_group')->get();
        // Fetch active loans
        $loanQuery = tableWithBranch('customer_loan','customer_loan')
            ->where('customer_loan.Status', '=', '0')
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route');

// Add conditions for filtering
        if ($request->customer != '0') {
            $loanQuery->where('customer_loan.Customer_idCustomer', '=', $request->customer);
        }

        if ($request->lending != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $request->lending);
        }

        if ($request->recovery != '0') {
            $loanQuery->where('customer_loan.collector_id', '=', $request->recovery);
        }

        if ($request->route != '0') {
            $loanQuery->where('route.id_route', '=', $request->route);
        }

        if ($request->center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $request->center_details);
        }

        if ($request->group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $request->group);
        }

        $loans = $loanQuery->get();

        // Initialize variables
        $current_loan_capital_amount = 0.00;
        $current_loan_interest_amount = 0.00;
        $current_loan_panelty_amount = 0.00;
        $Capital_Payment = 0.00;
        $Interest_Payment = 0.00;
        $Panelty_Payment = 0.00;

        // Initialize variables
        $past_capital_amount = 0.00;
        $past_loan_interest_amount = 0.00;
        $past_panelty_amount = 0.00;
        $past_Capital_Payment = 0.00;
        $past_Interest_Payment = 0.00;
        $past_Panelty_Payment = 0.00;

        foreach ($loans as $loan) {
            // Fetch the most recent installment
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderBy('idInstallments', 'desc')
                ->first();


            // Check if the maturity date is greater than the current date
            $Maturity_Date = $installment->Installment_Date ?? null;
            if ($Maturity_Date && $Maturity_Date > date('Y-m-d')) {
                // Calculate capital amount
                $current_loan_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount
                $current_loan_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $current_loan_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $Capital_Payment += $loan_payments->Capital_Payment ?? 0;
                $Interest_Payment += $loan_payments->Interest_Payment ?? 0;
                $Panelty_Payment += $loan_payments->Panelty_Payment ?? 0;
            }else{
                // Calculate capital amount
                $past_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount
                $past_loan_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $past_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $past_loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $past_Capital_Payment += $past_loan_payments->Capital_Payment ?? 0;
                $past_Interest_Payment += $past_loan_payments->Interest_Payment ?? 0;
                $past_Panelty_Payment += $past_loan_payments->Panelty_Payment ?? 0;
            }
        }

        // Calculate total amounts
        $current_loan_total = $current_loan_capital_amount + $current_loan_interest_amount + $current_loan_panelty_amount;
        $current_loan_total_payment = $Capital_Payment + $Interest_Payment + $Panelty_Payment;

        // Calculate total amounts
        $past_total = $past_capital_amount + $past_loan_interest_amount + $past_panelty_amount;
        $past_total_payment = $past_Capital_Payment + $past_Interest_Payment + $past_Panelty_Payment;












        // Fetch active loans
        $loanQuery = tableWithBranch('customer_loan','customer_loan')
            ->where('Status', '=', '1')
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route');



// Add conditions for filtering
        if ($request->customer != '0') {
            $loanQuery->where('customer_loan.Customer_idCustomer', '=', $request->customer);
        }

        if ($request->lending != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $request->lending);
        }

        if ($request->recovery != '0') {
            $loanQuery->where('customer_loan.collector_id', '=', $request->recovery);
        }

        if ($request->route != '0') {
            $loanQuery->where('route.id_route', '=', $request->route);
        }

        if ($request->center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $request->center_details);
        }

        if ($request->group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $request->group);
        }

        $loans = $loanQuery->get();


        // Initialize variables
        $fully_paid_capital_amount = 0.00;
        $fully_paid_interest_amount = 0.00;
        $fully_paid_panelty_amount = 0.00;
        $fully_paid_Capital_Payment = 0.00;
        $fully_paid_Interest_Payment = 0.00;
        $fully_paid_Panelty_Payment = 0.00;

        foreach ($loans as $loan) {
            // Fetch the most recent installment
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderBy('idInstallments', 'desc')
                ->first();


            // Check if the maturity date is greater than the current date
            $Maturity_Date = $installment->Installment_Date ?? null;
            if ($Maturity_Date && $Maturity_Date > date('Y-m-d')) {
                // Calculate capital amount
                // Calculate fully paid capital amount
                $fully_paid_capital_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Amount');

// Calculate interest amount for fully paid loans
                $fully_paid_interest_amount += tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('Interest_Amount');

// Calculate penalty amount
                $fully_paid_panelty_amount += tableWithBranch('installments','installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->sum('installments.Panalty_Amount');

// Calculate payments
                $loan_payments = tableWithBranch('Loan_Log','Loan_Log')
                    ->join('customer_loan', 'Loan_Log.Loan_ID', '=', 'customer_loan.idCustomer_Loan')
                    ->where('customer_loan.idCustomer_Loan', '=', $loan->idCustomer_Loan)
                    ->selectRaw('SUM(Loan_Log.Capital_Payment) as Capital_Payment')
                    ->selectRaw('SUM(Loan_Log.Interest_Payment) as Interest_Payment')
                    ->selectRaw('SUM(Loan_Log.Panelty_Payment) as Panelty_Payment')
                    ->first();


                $fully_paid_Capital_Payment += $loan_payments->Capital_Payment ?? 0;
                $fully_paid_Interest_Payment += $loan_payments->Interest_Payment ?? 0;
                $fully_paid_Panelty_Payment += $loan_payments->Panelty_Payment ?? 0;
            }
        }

        // Calculate total amounts
        $fully_paid_total = $fully_paid_capital_amount + $fully_paid_interest_amount + $fully_paid_panelty_amount;
        $fully_paid_total_payment = $fully_paid_Capital_Payment + $fully_paid_Interest_Payment + $fully_paid_Panelty_Payment;



        $selectedRoute = $request->route;
        $selectedCenter = $request->center_details;
        $selectedGroup = $request->group;
        $selectedCustomer = $request->customer;
        $selectedLendingOfficer = $request->lending;
        $selectedRecoveryOfficer = $request->recovery;

        // Return data to view
        return view('pages.Accounting.loanStatus', compact(

            'route','center','group','selectedRoute','selectedCenter','selectedGroup',
            'selectedCustomer',
    'selectedLendingOfficer',
    'selectedRecoveryOfficer',

            'customer',
            'lending_officer',
            'recovery_officer',

            'current_loan_capital_amount',
            'current_loan_interest_amount',
            'current_loan_panelty_amount',
            'current_loan_total',
            'Capital_Payment',
            'Interest_Payment',
            'Panelty_Payment',
            'current_loan_total_payment',


            'past_capital_amount',
            'past_loan_interest_amount',
            'past_panelty_amount',
            'past_total',
            'past_Capital_Payment',
            'past_Interest_Payment',
            'past_Panelty_Payment',
            'past_total_payment',


            'fully_paid_capital_amount',
            'fully_paid_interest_amount',
            'fully_paid_panelty_amount',
            'fully_paid_total',
            'fully_paid_Capital_Payment',
            'fully_paid_Interest_Payment',
            'fully_paid_Panelty_Payment',
            'fully_paid_total_payment'
        ));
    }

    public function updateStatus(Request $request)
    {
        $user_id=(int) session('userid');
        // Validate the incoming request data
        $validatedData = $request->validate([
            'log_id' => 'required|integer',
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $logId = $validatedData['log_id'];
        $status = $validatedData['status'];
        $note = $validatedData['note'] ?? null;

        // Update the log status in the database
        updateWithBranch('company_bank_has_log', 'id', $logId, [
            'updated_status' => $status,
            'updated_note' => $note,
            'updated_date_time' => now(),
            'updated_user' => $user_id,
        ]);


        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function transfer(Request $request)
    {


        $bankId = $request->bankId;
        $selectedBankId = $request->selectedBankId;
        $amount = $request->amount;

        $bank=tableWithBranch('company_bank_accounts')->where('Idbank','=',$selectedBankId)->first();
        $description="Bank Name :".$bank->Bank_Name."- Account Name :".$bank->Account_Name."- Account Num :".$bank->Account_No;

        $this->bankLogController->index($bankId, "Return To Company", $description, "-", "credit", $amount,$selectedBankId);


        $bank_2=tableWithBranch('company_bank_accounts')->where('Idbank','=',$bankId)->first();
        $description_2="Account Name :".$bank_2->Account_Name."- Account Num :".$bank_2->Account_No;

        $this->bankLogController->index($selectedBankId, "Return From Collector", $description_2, "-", "debit", $amount,$bankId);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }


    public function transfer_to_collector(Request $request)
    {
        $bankId = $request->bankId;
        $selectedBankId = $request->selectedBankId;
        $amount = $request->amount;

        $bank=tableWithBranch('company_bank_accounts')->where('Idbank','=',$selectedBankId)->first();
        $description="Bank Name :".$bank->Bank_Name."- Account Name :".$bank->Account_Name."- Account Num :".$bank->Account_No;

        $this->bankLogController->index($bankId, "Return To Collector", $description, "-", "debit", $amount,$selectedBankId);


        $bank_2=tableWithBranch('company_bank_accounts')->where('Idbank','=',$bankId)->first();
        $description_2="Account Name :".$bank_2->Account_Name."- Account Num :".$bank_2->Account_No;

        $this->bankLogController->index($selectedBankId, "Return From Company", $description_2, "-", "credit", $amount,$bankId);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function topup(Request $request)
    {
        $bankId = $request->bankId;
        $amount = $request->amount;
        $note = $request->note??'-';
        $bank=tableWithBranch('company_bank_accounts')->where('Account_No','=',"Cash")->first();
        $this->bankLogController->index($bankId, "Cash Top up", "-", "-", "credit", $amount,$bank->Idbank);


        $bank_2=tableWithBranch('company_bank_accounts')->where('Idbank','=',$bankId)->first();
        $description_2="Account Name :".$bank_2->Account_Name."- Account Num :".$bank_2->Account_No;
        $this->bankLogController->index($bank->Idbank, "Cash Deposit", $description_2, $note, "debit", $amount,$bankId);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }


    public function searchReconciliation(Request $request)
    {

        $query = tableWithBranch('reconciliation','reconciliation')
            ->join('company_bank_accounts', 'reconciliation.account_id', '=', 'company_bank_accounts.Idbank')
            ->whereBetween('date', [$request->date_from, $request->date_to])
            ->select('reconciliation.*',  'company_bank_accounts.Bank_Name', 'company_bank_accounts.Account_Name', 'company_bank_accounts.Account_No')
            ->orderBy('date', 'desc');

        if ($request->account_id!=0){
            $query=$query->where('account_id', $request->account_id);
        }


        $query = $query->get();

        return response()->json($query);
    }

    public function getLastReconciliation(Request $request)
    {
        // Fetch the last reconciliation entry for the selected account
        $lastReconciliation = tableWithBranch('reconciliation')
            ->where('account_id', $request->account_id)
            ->orderBy('date', 'desc') // Get the most recent record
            ->first(); // Retrieve only one row

        if (!$lastReconciliation) {
            $lastReconciliation = tableWithBranch('company_bank_has_log')
                ->where('Bank_Account_Id', $request->account_id)
                ->orderBy('id', 'asc') // Order by the latest date-time
                ->select('company_bank_has_log.Balance as balance')
                ->first(); // Get only the first row
        }

        return response()->json($lastReconciliation);
    }

    public function storeReconciliation(Request $request)
    {
        try {
            DB::beginTransaction();

            $note=$request->note??'-';
            $user_id = (int)session('userid');
            $reconciliation = [
                'account_id' => $request->account_id,
                'date' => $request->date,
                'created_date_time' => Carbon::now(),
                'note' => $note,
                'balance' => $request->beginig_balance,
                'endingBalance' => $request->balance,
                'status' => '0',
                'user_id' => $user_id
            ];

            // Insert into reconciliation table
            $reconciliationId = insertWithBranch('reconciliation', $reconciliation);
            if (!empty($request->transactions) && is_array($request->transactions)) {
                // Insert transactions into reconciliation_has_data table
                foreach ($request->transactions as $transaction) {
                    $reconciliation_has_data = [
                        'id_reconciliation' => $reconciliationId,
                        'description' => $transaction['description'],
                        'date' => $transaction['date'],
                        'credit' => $transaction['credit'],
                        'debit' => $transaction['debit'],
                        'account_id' => $transaction['account_id'],
                    ];
                    insertWithBranch('reconciliation_has_data',$reconciliation_has_data);
                }

            }

            DB::commit();

            return response()->json(['status' => 'success', 'id' => $reconciliationId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function reconciliation($id,$status)
    {
        $reconciliation = tableWithBranch('reconciliation','reconciliation')
            ->join('company_bank_accounts', 'reconciliation.account_id', '=', 'company_bank_accounts.Idbank')
            ->where('id_reconciliation', '=', $id)
            ->select('reconciliation.*','company_bank_accounts.Idbank', 'company_bank_accounts.Bank_Name', 'company_bank_accounts.Account_Name', 'company_bank_accounts.Account_No', 'company_bank_accounts.code')
            ->first();

        if (!$reconciliation) {
            return redirect()->back()->with('error', 'Reconciliation record not found.');
        }

        if ($reconciliation->status == "-1") {
            if ($status == "edit") {
                return redirect()->route('bankReconciliation.reconciliation', ['id' => $id, 'status' => "view"]);
            }
        }

        $date = $reconciliation->date;

        $date_to = $date . ' 23:59:59';

        $bank_log = tableWithBranch('company_bank_has_log', 'company_bank_has_log')
            ->join('reconciliation', 'reconciliation.account_id', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('company_bank_has_log.Date_Time', '<=', $date_to) // ✅ Includes all past transactions up to this date
            ->where('company_bank_has_log.reconsilation_status', '=', "0") // ✅ Includes all past transactions up to this date
            ->whereRaw('CAST(reconciliation.status AS SIGNED) < 1')
            ->where('reconciliation.id_reconciliation', '=', $reconciliation->id_reconciliation)
            ->get();

        if ($status != "edit") {
            $bank_log = tableWithBranch('reconciliation_logs', 'reconciliation_logs')
                ->leftjoin('reconciliation', 'reconciliation.id_reconciliation', '=', 'reconciliation_logs.id_reconciliation')
                ->where('reconciliation_logs.id_reconciliation', '=', $reconciliation->id_reconciliation)
                ->get();
        }


        $bank = tableWithBranch('company_bank_accounts')->get();

        $reconciliation_log=tableWithBranch('reconciliation_has_data','reconciliation_has_data')
            ->join('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'reconciliation_has_data.account_id')
            ->where('reconciliation_has_data.id_reconciliation', '=', $id)
            ->select('reconciliation_has_data.*', 'company_bank_accounts.Bank_Name', 'company_bank_accounts.Account_Name', 'company_bank_accounts.Account_No')
            ->get();

        // Fetch the last reconciliation entry for the selected account
        $lastReconciliation = tableWithBranch('reconciliation')
            ->where('account_id', $reconciliation->account_id)
            ->orderBy('date', 'desc') // Get the most recent record
            ->first(); // Retrieve only one row

        if (!$lastReconciliation) {
            $lastReconciliation = tableWithBranch('company_bank_has_log')
                ->where('Bank_Account_Id', $reconciliation->account_id)
                ->orderBy('id', 'asc') // Order by the latest date-time
                ->first(); // Get only the first row
        }

// ✅ Normalize the balance value from the correct column
        $balance = $lastReconciliation ? ($lastReconciliation->balance ?? $lastReconciliation->Balance ?? 0) : 0;

        return view('pages.Accounting.BankReconsilationInside', compact('reconciliation','balance','reconciliation_log','bank', 'bank_log','id','status'));
    }


    public function Reconciliation_delete(Request $request)
    {

        try {
            DB::beginTransaction();

            // Delete transactions first (Foreign Key Dependency)
            deleteWithBranch('reconciliation_has_data','id_reconciliation', $request->id);

            // Delete reconciliation record
            deleteWithBranch('reconciliation','id_reconciliation', $request->id);

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function reconciliation_store(Request $request){
        DB::beginTransaction(); // ✅ Begin transaction

        try {
            $reco_id = $request->id;
            $main_bank_id = $request->bank_id;
            $endingBalance = $request->summary['endingBalance'];
            $clearedBalance = $request->summary['clearedBalance'];
            $difference = $request->summary['difference'];
            $note = $request->summary['note'];

            // ✅ 1. Update reconciliation table
            updateWithBranch('reconciliation', 'id_reconciliation', $reco_id, [
                'endingBalance' => $endingBalance,
                'clearedBalance' => $clearedBalance,
                'difference' => $difference,
                'note' => $note,
                'status' => "-1",
            ]);

            if (!empty($request->creditTransactions) && is_array($request->creditTransactions)) {
                // ✅ 2. Update checked credit transactions in company_bank_has_log
                foreach ($request->creditTransactions as $transaction) {
                    $bank_id = $transaction['id'];
                    $check = $transaction['creditTransactionsCheck'];
                    if ($check=="1"){
                        updateWithBranch('company_bank_has_log', 'id', $bank_id, [
                            'reconsilation_status' => $reco_id,
                        ]);
                    }

                    $reconciliation_log_credit = [
                        'log_id' => $bank_id,
                        'id_reconciliation' => $reco_id,
                        'description' => $transaction['description'],
                        'date' => $transaction['date'],
                        'type' => $transaction['type'],
                        'credit' => $transaction['amount'],
                        'debit' => '0.00',
                        'check_status' => $check,
                    ];
                    insertWithBranch('reconciliation_logs', $reconciliation_log_credit);



                }
            }

            if (!empty($request->debitTransactions) && is_array($request->debitTransactions)) {
                // ✅ 3. Update checked debit transactions in company_bank_has_log
                foreach ($request->debitTransactions as $transaction) {
                    $bank_id = $transaction['id'];
                    $check = $transaction['debitTransactionsCheck'];
                    if ($check=="1"){
                        updateWithBranch('company_bank_has_log', 'id', $bank_id, [
                            'reconsilation_status' => $reco_id,
                        ]);
                    }


                    $reconciliation_log_credit = [
                        'log_id' => $bank_id,
                        'id_reconciliation' => $reco_id,
                        'description' => $transaction['description'],
                        'date' => $transaction['date'],
                        'type' => $transaction['type'],
                        'credit' => '0.00',
                        'debit' => $transaction['amount'],
                        'check_status' => $check,
                    ];
                    insertWithBranch('reconciliation_logs', $reconciliation_log_credit);
                }
            }

            if (!empty($request->transactionEntries) && is_array($request->transactionEntries)) {
                // ✅ 4. Delete old reconciliation_has_data records
                deleteWithBranch('reconciliation_has_data', 'id_reconciliation', $reco_id);

                // ✅ 5. Insert new reconciliation_has_data records
                foreach ($request->transactionEntries as $entry) {
                    $bank_id = $entry['id'];

                    $reconciliation_has_data = [
                        'id_reconciliation' => $reco_id,
                        'description' => $entry['description'],
                        'date' => $entry['date'],
                        'credit' => $entry['credit'],
                        'debit' => $entry['debit'],
                        'account_id' => $bank_id,
                    ];
                    insertWithBranch('reconciliation_has_data', $reconciliation_has_data);

                    $bank=tableWithBranch('company_bank_accounts')->where('Idbank','=',$bank_id)->first();
                    $description=$entry['description'];

                    if ($entry['credit']>0){
                        $this->bankLogController->index($bank->Idbank, "Bank Reconciliation", $description, "-", "credit", $entry['credit'],$main_bank_id,0,$reco_id);
                        $this->bankLogController->index($main_bank_id, "Bank Reconciliation", $description, "-", "debit", $entry['credit'],$bank->Idbank,0,$reco_id);
                    }

                    if ($entry['debit']>0){
                        $this->bankLogController->index($bank->Idbank, "Bank Reconciliation", $description, "-", "debit", $entry['credit'],$main_bank_id,0,$reco_id);
                        $this->bankLogController->index($main_bank_id, "Bank Reconciliation", $description, "-", "credit", $entry['credit'],$bank->Idbank,0,$reco_id);
                    }
                }
            }


            DB::commit(); // ✅ Commit transaction if all operations succeed
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollback(); // ❌ Rollback if any operation fails
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function ReconciliationDetails($id){
        $reconciliation=tableWithBranch('reconciliation','reconciliation')
            ->join('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'reconciliation.account_id')
            ->join('user', 'user.id', '=', 'reconciliation.user_id')
            ->where('id_reconciliation','=',$id)->first();



        $Checks_and_Payments_count=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->sum('credit');

        $Deposits_and_Credits_count=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->sum('debit');

        $Checks_and_Payments=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->get();



        $Checks_and_Payments_count_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->sum('credit');

        $Deposits_and_Credits_count_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->sum('debit');

        $Checks_and_Payments_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->get();


        $Checks_and_Payments_count_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->sum('credit');

        $Deposits_and_Credits_count_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->sum('debit');

        $Checks_and_Payments_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->get();


        return view('pages.Accounting.ReconciliationsDetails',compact('Checks_and_Payments_new','Checks_and_Payments_uncleared','Checks_and_Payments','Deposits_and_Credits_sum_new','Deposits_and_Credits_count_new','Checks_and_Payments_sum_new','Checks_and_Payments_count_new','Deposits_and_Credits_sum_uncleared','Deposits_and_Credits_count_uncleared','Checks_and_Payments_sum_uncleared','Checks_and_Payments_count_uncleared','Deposits_and_Credits_sum','Deposits_and_Credits_count','reconciliation','Checks_and_Payments_count','Checks_and_Payments_sum'));
    }

    public function ReconciliationSummary($id){
        $reconciliation=tableWithBranch('reconciliation','reconciliation')
            ->join('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'reconciliation.account_id')
            ->join('user', 'user.id', '=', 'reconciliation.user_id')
            ->where('id_reconciliation','=',$id)->first();

        $Checks_and_Payments_count=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->sum('credit');

        $Deposits_and_Credits_count=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','1')
            ->sum('debit');




        $Checks_and_Payments_count_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->sum('credit');

        $Deposits_and_Credits_count_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum_uncleared=tableWithBranch('reconciliation_logs')
            ->where('id_reconciliation','=',$id)
            ->where('check_status','=','0')
            ->sum('debit');



        $Checks_and_Payments_count_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->where('credit','>',0)
            ->count();
        $Checks_and_Payments_sum_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->sum('credit');

        $Deposits_and_Credits_count_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->where('debit','>',0)
            ->count();
        $Deposits_and_Credits_sum_new=tableWithBranch('reconciliation_has_data')
            ->where('id_reconciliation','=',$id)
            ->sum('debit');



        return view('pages.Accounting.ReconciliationsSummary',compact('Deposits_and_Credits_sum_new','Deposits_and_Credits_count_new','Checks_and_Payments_sum_new','Checks_and_Payments_count_new','Deposits_and_Credits_sum_uncleared','Deposits_and_Credits_count_uncleared','Checks_and_Payments_sum_uncleared','Checks_and_Payments_count_uncleared','Deposits_and_Credits_sum','Deposits_and_Credits_count','reconciliation','Checks_and_Payments_count','Checks_and_Payments_sum'));
    }

}
