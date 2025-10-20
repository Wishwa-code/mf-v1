<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BankLogController;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userData= tableWithBranch('user')->get();
        $designation= tableWithBranch('designation')->get();
        $branch=DB::table('branch')->where('branch_id','=',session('branch_id'))->get();
        if (session('branch_access')==1){
            $branch=DB::table('branch')->get();
        }

        return view('pages.User',compact('userData','designation','branch'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'full_name'=>'required',
            'email'=>'required',
            'password'=>'required'
        ]);

        $tp=$request->tp;
        if ($tp===null){
            $tp="-";
        }

        if (DB::table('user')->where('email', '=', $request->email)->exists()) {
            return redirect()->intended(route('pages.user'))->with("error","This user is already exist !");
        }else{

            // Generate OTP
            $otp = Str::random(6); // Or use a more secure method to generate OTP




            $data['Full_Name']=$request->full_name;
            $data['email']=$request->email;
            $data['password']=Hash::make($request->password);
            $data['TP']=$tp;
            $data['Designation']=$request->desi;
            $data['Epf_no']=$request->epf_no;
            $data['Nic']=$request->nic;
            $data['lending_officer']=$request->has('lending_officer') ? 1 : 0;
            $data['otp']=$otp;
            $data['branch_id']=$request->branches[0] ?? session('branch_id');
            $data['branch_access']=$request->has('branch_access') ? 1 : 0;
            $data['cashier']=$request->has('cashier') ? 1 : 0;
            $data['collector']=$request->has('collecting_officer') ? 1 : 0;

            // Store request data for approval
            $requestData = [
                'user_data' => $data,
                'branches' => $request->branches ?? [],
                'account_number' => $request->account_number ?? null
            ];

            // Create approval request
            DB::table('approval_request')->insert([
                'type' => 'User Creation',
                'typeid' => 101,
                'description' => 'User Creation: ' . $request->full_name . ' (' . $request->email . ')',
                'data' => json_encode($requestData),
                'userid' => session('userid'),
                'branch_id' => session('branch_id'),
                'data_time' => now(),
                'status' => 0
            ]);

            return redirect()->intended(route('pages.user'))->with("success", "User creation request sent for approval!");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Store $session)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // Authentication successful
            $session->put('username', $request->email);
            $user = DB::table('user')->where('email', $request->email)->get();
            foreach ($user as $item) {



                // Store user information in session

                $session->put('userid',(int) $item->id);
                $session->put('Full_Name', $item->Full_Name);
                $session->put('designation', $item->Designation);
                $session->put('branch_id',(int) $item->branch_id);
                $session->put('branch_access',(int) $item->branch_access);
                $company = DB::table('company')->first();
                $session->put('company_name', $company->company_name);
                // Get branch information
                $branch = DB::table('branch')->where('branch_id', '=', $item->branch_id)->first();
                $session->put('branch_name', $branch->Name);

                // Check if the user is deactivated
                if ($item->Status === "0") {
                    return redirect()->route('login')->with("error", "Please contact Admin!");
                }
            }
            // Check if 'log_tracking_no' column exists in 'company_bank_has_log'
            if (!Schema::hasColumn('company_bank_has_log', 'log_tracking_no')) {
                DB::statement("ALTER TABLE `company_bank_has_log` ADD `log_tracking_no` VARCHAR(10) NULL");
            }
            // Dynamically add 'status' column if it does not exist
            if (!Schema::hasColumn('loan_category', 'status')) {
                DB::statement("ALTER TABLE loan_category ADD COLUMN status TINYINT DEFAULT 1");
            }



//            $loanLogs = DB::table('Loan_Log')
//                ->orderBy('Loan_ID')
//                ->orderBy('Loan_Log_ID')
//                ->get();
//
//            $prevLogs = [];
//
//            foreach ($loanLogs as $log) {
//                $loanId = $log->Loan_ID;
//
//                // Initialize previous log if not exists
//                if (!isset($prevLogs[$loanId])) {
//                    $prevLogs[$loanId] = (object)[
//                        'Capital_Balance' => 0,
//                        'Interest_Balance' => 0,
//                        'Panelty_Balance' => 0,
//                        'Total_Pending_Balance' => 0,
//                    ];
//                }
//
//                $prev = $prevLogs[$loanId];
//                $update = [];
//
//                if ($log->Type === 'Issue Loan') {
//                    // Do nothing, just carry over
//                    $update = $prev;
//
//                } elseif ($log->Type === 'Customer Payment') {
//                    $update = (object)[
//                        'Capital_Balance' => $prev->Capital_Balance - $log->Capital_Payment,
//                        'Interest_Balance' => $prev->Interest_Balance - $log->Interest_Payment,
//                        'Panelty_Balance' => $prev->Panelty_Balance - $log->Panelty_Payment,
//                        'Total_Pending_Balance' => $prev->Total_Pending_Balance - $log->Amount,
//                    ];
//
//                } elseif ($log->Type === 'Penalty') {
//                    $update = (object)[
//                        'Capital_Balance' => $prev->Capital_Balance,
//                        'Interest_Balance' => $prev->Interest_Balance,
//                        'Panelty_Balance' => $prev->Panelty_Balance + $log->Amount,
//                        'Total_Pending_Balance' => $prev->Total_Pending_Balance + $log->Amount,
//                    ];
//
//                } elseif ($log->Type === 'Payment Undo') {
//                    $original = DB::table('Loan_Log')
//                        ->where('Loan_Log_ID', $log->Type_ID)
//                        ->where('Type', 'Customer Payment')
//                        ->first();
//
//                    if ($original) {
//                        $update = (object)[
//                            'Capital_Balance' => $prev->Capital_Balance + $original->Capital_Payment,
//                            'Interest_Balance' => $prev->Interest_Balance + $original->Interest_Payment,
//                            'Panelty_Balance' => $prev->Panelty_Balance + $original->Panelty_Payment,
//                            'Total_Pending_Balance' => $prev->Total_Pending_Balance + $original->Amount,
//                        ];
//                    } else {
//                        // If original not found, keep previous balances
//                        $update = $prev;
//                    }
//                }
//
//                // Update the Loan_Log row
//                DB::table('Loan_Log')
//                    ->where('Loan_Log_ID', $log->Loan_Log_ID)
//                    ->update([
//                        'Capital_Balance' => $update->Capital_Balance,
//                        'Interest_Balance' => $update->Interest_Balance,
//                        'Panelty_Balance' => $update->Panelty_Balance,
//                        'Total_Pending_Balance' => $update->Total_Pending_Balance,
//                    ]);
//
//                // Set as previous for next loop
//                $prevLogs[$loanId] = $update;
//            }



            return redirect()->intended(route('home'));
        }

        // Authentication failed
        return redirect()->route('login')->with("error", "Login details are not valid!");
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $getuser = tableWithBranch('user')->where('id', $id)->first();

        if ($getuser) {
            $newStatus = $getuser->Status == "1" ? "0" : "1"; // Toggle the Status
            $actionType = $newStatus == "1" ? "Activate User" : "Deactivate User";
            
            // Store user status change data for approval
            $requestData = [
                'user_id' => $id,
                'old_data' => (array)$getuser,
                'new_data' => ['Status' => $newStatus],
                'action_type' => $actionType,
            ];

            // Create approval request (use type 102 - User Details Update)
            DB::table('approval_request')->insert([
                'type' => 'User Details Update',
                'typeid' => 102,
                'description' => $actionType . ': ' . $getuser->Full_Name . ' (ID: ' . $id . ')',
                'data' => json_encode($requestData),
                'userid' => session('userid'),
                'branch_id' => session('branch_id'),
                'data_time' => now(),
                'status' => 0
            ]);
            
            return response()->json(['message' => 'User status change request sent for approval!'], 200);
            
            // OLD CODE - keeping for approval handler reference
            /*
            DB::table('user')->where('id', $id)->update(['Status' => $newStatus]);
            return response()->json(['message' => 'Data updated successfully'], 200);
            */
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $user= tableWithBranch('user')->where('idUser', $request->user_id)->get();
        foreach ($user as $item){
            $data['password']=Hash::make($request->c_pass);
            DB::table('user')->where('idUser', $request->user_id)->update($data);
            return redirect()->intended(route('pages.user'))->with("success", "Password updated !");
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function showdashboard(Store $session){


        if (!Auth::check()) {
            return redirect()->route('login')->with("error", "Session expired! Please Login");
        }

        // Head Office aggregated dashboard: show all branches overview
        if ((int)session('branch_id') === -1) {
            // Fetch active branches excluding head office itself
            $branches = DB::table('branch')->where('status',1)->where('branch_id','!=',-1)->get();

            $branchMetrics = [];
            foreach ($branches as $b) {
                $branchId = $b->branch_id;
                // Helper closure forcing branch scope manually
                $scoped = function($table) use ($branchId) {
                    return DB::table($table)->where($table.'.branch_id',$branchId);
                };

                $customers = $scoped('customer')->count();
                $loanPendingQ = $scoped('customer_loan')->where('Status','-1');
                $loanCurrentQ = $scoped('customer_loan')->where('Status','0');
                $loanSettledQ = $scoped('customer_loan')->where('Status','1');
                $pendingCount = $loanPendingQ->count();
                $pendingAmount = $scoped('customer_loan')->where('Status','-1')->sum('Amount');
                $currentCount = $loanCurrentQ->count();
                $currentAmount = $scoped('customer_loan')->where('Status','0')->sum('Amount');
                $settledCount = $loanSettledQ->count();
                $portfolio = $scoped('installments')->sum('capital_balance');
                $todayInstallment = DB::table('installments')
                    ->join('customer_loan','installments.Customer_Loan_idCustomer_Loan','=','customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id',$branchId)
                    ->where('customer_loan.branch_id',$branchId)
                    ->whereDate('installments.Installment_Date',date('Y-m-d'))
                    ->where('customer_loan.Status','0')
                    ->sum('installments.Total_Balance');
                $todayCollected = $scoped('customer_payments')->where('Date',date('Y-m-d'))->sum('Amount');
                // arrears: overdue installments (date < today) still active
                $arrears = DB::table('installments')
                    ->join('customer_loan','installments.Customer_Loan_idCustomer_Loan','=','customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id',$branchId)
                    ->where('customer_loan.branch_id',$branchId)
                    ->where('customer_loan.Status','0')
                    ->whereDate('installments.Installment_Date','<',date('Y-m-d'))
                    ->sum('installments.Total_Balance');

                $branchMetrics[] = [
                    'id' => $branchId,
                    'name' => $b->Name,
                    'customers' => $customers,
                    'pending_loans_count' => $pendingCount,
                    'pending_loans_amount' => (float)$pendingAmount,
                    'current_loans_count' => $currentCount,
                    'current_loans_amount' => (float)$currentAmount,
                    'settled_loans_count' => $settledCount,
                    'portfolio' => (float)$portfolio,
                    'today_installment' => (float)$todayInstallment,
                    'today_collected' => (float)$todayCollected,
                    'arrears' => (float)$arrears,
                ];
            }

            return view('ho-dashboard', [
                'branchMetrics' => $branchMetrics,
            ]);
        }

        if (!Schema::hasColumn('installments', 'Panelty_count')) {
            DB::statement(
                "ALTER TABLE `installments`
         ADD COLUMN `Panelty_count` VARCHAR(45) NOT NULL
         DEFAULT '0'"
            );
        }


        if (!Schema::hasColumn('loan_category', 'collection_date_type')) {
            DB::statement(
                "ALTER TABLE `loan_category`
         ADD COLUMN `collection_date_type` VARCHAR(45) NOT NULL
         DEFAULT 'same_as_installment'"
            );
        }


        if (!Schema::hasColumn('route', 'collection_type')) {
            DB::statement(
                "ALTER TABLE `route`
         ADD COLUMN `collection_type` VARCHAR(45) NOT NULL
         DEFAULT 'customizable'"
            );
        }

        if (!Schema::hasColumn('route', 'collection_date')) {
            DB::statement(
                "ALTER TABLE `route`
         ADD COLUMN `collection_date` VARCHAR(45) NOT NULL
         DEFAULT 'Monday'"
            );
        }



        $loan=tableWithBranch('customer_loan')->where('Status','!=','1')->get();
        $CapitalBalanceController = new CapitalBalanceController();
        foreach ($loan as $loans){
            $CapitalBalanceController->create($loans->idCustomer_Loan);
        }


//        $CapitalBalanceController->panelty_remove();


        // Call to the penalty creation function
        // $this->create_panelty();


        $customerCount = tableWithBranch('customer')->count();
        $customer_loan_pending_Count = tableWithBranch('customer_loan')->where('Status','=','-1')->count();
        $customer_loan_pending_Amount = tableWithBranch('customer_loan')->where('Status','=','-1')->sum('Amount');
        $customer_loan_current_Count = tableWithBranch('customer_loan')->where('Status','=','0')->count();
        $customer_loan_current_Amount = tableWithBranch('customer_loan')->where('Status','=','0')->sum('Amount');
        $setteled_loan_Count = tableWithBranch('customer_loan')->where('Status','=','1')->count();
        $deleted_loan_Count = tableWithBranch('customer_loan')->where('Status','=','-2')->count();
        $setteled_loan_current_Amount = tableWithBranch('customer_loan')->where('Status','=','1')->sum('Amount');
        $portfolio = tableWithBranch('installments')->sum('capital_balance');

        // Current month lending amount - from 1st of current month to today
        $currentMonthStart = date('Y-m-01'); // First day of current month
        $today = date('Y-m-d');
        $currentMonthLending = tableWithBranch('customer_loan')
            ->whereBetween('Date_Time', [$currentMonthStart, $today])
            ->where('Status', '0') // Only disbursed loans
            ->sum('Amount');

        $todayinstallment = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('installments.Installment_Date', '=', date('Y-m-d'))
            ->where('customer_loan.Status', '=', '0')
            ->sum('installments.Total_Balance');
        $todaycollected = tableWithBranch('customer_payments')->where('Date',date('Y-m-d'))->sum('Amount');



        $checqueamount = tableWithBranch('Cheque_payment')->where('payment_date',date('Y-m-d'))->where('chq_status','=','0')->sum('payment_amount');
        $shortcut=tableWithBranch('shortcut')->get();
        $shortcut_count=tableWithBranch('shortcut')->count();

        $all_loan=$customer_loan_current_Count+$setteled_loan_Count;



        $todaycollection=tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->select('customer.*','customer_group.Name as group_name','installments.*','customer_loan.*','loan_category.Name as loan_name')
            ->whereDate('Installment_Date','=',date('Y-m-d'))
            ->where('installments.Status','=','0')
            ->where('customer_loan.Status', '=', '0')
            ->get();


        $loanQuery_2 = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();  // Try without grouping for now

// Assign the values to variables
        $todayInstallment = $loanQuery_2->Today_installment;
        $arrease = $loanQuery_2->arrease;
        $totalBalanceUntil = $loanQuery_2->Total_Balance_until;
        $totalBalanceUntil=$totalBalanceUntil+$checqueamount;

        // Total Outstanding: capital balance + interest balance where status = 0
        $totalOutstanding = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $totalOutstanding = $totalOutstanding->total_outstanding ?? 0;

        // Penalty Balance: sum of penalty balance where status = 0
        $penaltyBalance = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.Panalty_Balance) as penalty_balance')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $penaltyBalance = $penaltyBalance->penalty_balance ?? 0;

        $userid=session('userid');

        $getuser = DB::table('user_privileges_has_user')->where('user_id', $userid)->where('permission_key','=','dashboard')->first();
        $dashboard=0;
        if ($getuser){
            $dashboard=$getuser->value;
        }

        $currentYear = date('Y');

        $monthlyRevenue = tableWithBranch('customer_payments')
            ->select(
                DB::raw('MONTH(Date) as month'),
                DB::raw('SUM(Amount) as total')
            )
            ->whereYear('Date', $currentYear) // Filter by current year
            ->groupBy(DB::raw('MONTH(Date)'))
            ->orderBy(DB::raw('MONTH(Date)'))
            ->get();

        $monthlyData = array_fill(0, 12, 0); // Initialize with 12 zeros

        foreach ($monthlyRevenue as $item) {
            $monthlyData[$item->month - 1] = (float) $item->total;
        }


        $startOfWeek = Carbon::now()->startOfWeek(); // Mon 2025-04-21 00:00:00
        $endOfWeek = Carbon::now()->endOfWeek();     // Sun 2025-04-27 23:59:59
        $startOfLastWeek = $startOfWeek->copy()->subWeek();
        $endOfLastWeek = $endOfWeek->copy()->subWeek();


        $getPaymentsPerDay = function ($start, $end) {
            $results = tableWithBranch('customer_payments')
                ->select('Date', DB::raw('SUM(Amount) as total'))
                ->whereBetween('Date', [$start->toDateString(), $end->toDateString()])
                ->groupBy('Date')
                ->get();

            $week = array_fill(0, 7, 0);
            foreach ($results as $row) {
                $dayIndex = Carbon::parse($row->Date)->dayOfWeek; // 0 = Sun, ..., 6 = Sat
                $week[$dayIndex] += (float) $row->total;
            }

            return $week;
        };



        $weeklyComparison = [
            'current' => $getPaymentsPerDay($startOfWeek, $endOfWeek),
            'last' => $getPaymentsPerDay($startOfLastWeek, $endOfLastWeek),
        ];



        $profit = 907195;
        $profitTarget = 1000000; // 1 million


        $user=tableWithBranch('user')->where('id','=',$userid)->first();


        if ($user){
            if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $userid)->exists()) {

            }else {

                $Bank = [
                    'Bank_Type' => "Collector",
                    'code' => $user->id.'/Collector',
                    'Bank_Name' => "Collector",
                    'Account_Name' => $user->Full_Name,
                    'Account_No' => $user->id,
                    'Bank_Branch' => '-',
                    'Account_Balance' => "0.00",
                    'type' => "Cash and Bank",
                    'cashflow' => "Non Applicable",
                    'User' => $user->id,
                    'branch_id' => session('branch_id'),
                ];


                $insertedId = insertWithBranch('company_bank_accounts', $Bank);
// Convert the BankLog object to an array for insertion
                $bankLogData = [
                    'Bank_Account_Id' => $insertedId,
                    'Date_Time' => date('Y-m-d H:i:s'),
                    'Type' => "Account Creation",
                    'Description' => "Collector Account",
                    'Note' => "",
                    'Credit' => "0.00",
                    'Debit' => "0.00",
                    'Balance' => "0.00",
                    'User' => $user->id,
                    'branch_id' => session('branch_id'),
                ];

// Insert the BankLog entry using the helper function
                insertWithBranch('company_bank_has_log', $bankLogData);
            }
        }


// Check if 'log_tracking_no' column exists in 'company_bank_has_log'
        if (!Schema::hasColumn('company_bank_has_log', 'log_tracking_no')) {
            DB::statement("ALTER TABLE `company_bank_has_log` ADD `log_tracking_no` VARCHAR(10) NULL");
        }

        // Weekly unpaid (active loans)
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekToday = date('Y-m-d');

        $weeklyUnpaidQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('installments.Total_Balance', '>', 0)
            ->whereBetween('installments.Installment_Date', [$weekStart, $weekToday]);

        $weeklyUnpaidCount = (clone $weeklyUnpaidQuery)->count();
        $weeklyUnpaidAmount = (clone $weeklyUnpaidQuery)->sum('installments.Total_Balance');
        // Distinct customers with unpaid installments (head count)
        $weeklyUnpaidCustomerCount = (clone $weeklyUnpaidQuery)
            ->distinct()
            ->count('customer_loan.Customer_idCustomer');


        return view('home',compact(
            'currentMonthLending','portfolio','profit','todaycollected','profitTarget','weeklyComparison',
            'deleted_loan_Count','all_loan','monthlyData','dashboard','checqueamount','totalBalanceUntil','arrease',
            'todayInstallment','setteled_loan_current_Amount','customer_loan_pending_Amount','customer_loan_current_Amount',
            'setteled_loan_Count','shortcut_count','shortcut','customerCount','customer_loan_pending_Count',
            'customer_loan_current_Count','todayinstallment','todaycollection',
            'weeklyUnpaidCount','weeklyUnpaidAmount','weeklyUnpaidCustomerCount','totalOutstanding','penaltyBalance'
        ));
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        Session::forget('token');
        return redirect()->intended(route('login'));
    }


    public function totalOutstandingData()
    {
        $totalOutstandingData = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',
                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where(function($query) {
                $query->where('installments.capital_balance', '>', 0)
                      ->orWhere('installments.Interest_Balance', '>', 0);
            })
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('total_outstanding > 0')
            ->orderBy('total_outstanding', 'DESC')
            ->get();

        return response()->json(['data' => $totalOutstandingData]);
    }

        return response()->json(['data' => $weeklyNotPaidData]);
    }

    public function penaltyBalanceData()
    {
        $penaltyBalanceData = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',
                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding'),
                DB::raw('SUM(installments.Panalty_Balance) as penalty_balance')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Panalty_Balance', '>', 0)
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('penalty_balance > 0')
            ->orderBy('penalty_balance', 'DESC')
            ->get();

        return response()->json(['data' => $penaltyBalanceData]);
    public function weeklyNotPaidData()
    {
        // Get current week date range
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekToday = date('Y-m-d');

        $weeklyNotPaidData = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',
                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                // This week not paid amount (installments between week start and today)
                DB::raw('SUM(CASE WHEN installments.Installment_Date BETWEEN "'.$weekStart.'" AND "'.$weekToday.'" THEN installments.Total_Balance ELSE 0 END) as this_week_not_paid'),
                // Total arrears (all overdue installments)
                DB::raw('SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END) as total_arrears'),
                // Not paid installment count
                DB::raw('COUNT(CASE WHEN installments.Total_Balance > 0 AND installments.Status = 0 THEN 1 END) as not_paid_installment_count')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('installments.Total_Balance', '>', 0)
            ->whereBetween('installments.Installment_Date', [$weekStart, $weekToday])
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('this_week_not_paid > 0')
            ->orderBy('this_week_not_paid', 'DESC')
            ->get();

        return response()->json(['data' => $weeklyNotPaidData]);
    }

    public function penaltyBalanceData()
    {
        $penaltyBalanceData = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',
                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding'),
                DB::raw('SUM(installments.Panalty_Balance) as penalty_balance')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Panalty_Balance', '>', 0)
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('penalty_balance > 0')
            ->orderBy('penalty_balance', 'DESC')
            ->get();

        return response()->json(['data' => $penaltyBalanceData]);
    }



    /*
    public function create_panelty()
    {
//        $date=date('Y-m-d');
//
//
//        $installment=tableWithBranch('installments','installments')
//            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
//            ->where('customer_loan.Status', '=', '0')
//            ->where('installments.Status', '=', '0')
//            ->whereDate('Panelty_date', '<=', $date)
//            ->select('installments.*', 'customer_loan.Panalty_Rate','customer_loan.Panelty_period as Loan_Panelty_period','customer_loan.panelty_method','customer_loan.Panelty_period','customer_loan.idCustomer_Loan','customer_loan.Customer_idCustomer')
//            ->get();
//
//        $today  = Carbon::today('Asia/Colombo');
//        foreach ($installment as $item){
//
//            $Panelty_period=$item->Loan_Panelty_period;
//            $penaltyDate = Carbon::parse($item->Panelty_date);
//            $days = max(0, $penaltyDate->diffInDays($today, false));
//
//            if ($Panelty_period == "Weekly") {
//                // Convert to full weeks (round down)
//                $days = intdiv($days, 7);
//                $days++;
//            }
//
//
//            $paneltyCount = (int) ($item->Panelty_count ?? 0);
//            $missing      = max(0, $days - $paneltyCount);
//
//            if ($missing > 0) {
//
//                $ins_amount=$item->capital_balance + $item->Interest_Balance;
//                $panelty_amount=($ins_amount*$item->Panalty_Rate)/100;
//
//                $count=$paneltyCount;
//
//                for ($i = 1; $i <= $missing; $i++) {
//                    $count++;
//                    $amt = number_format((float) $panelty_amount, 2, '.', ''); // sanitize to 2dp
//
//                    DB::table('installments')
//                        ->where('idInstallments', $item->idInstallments)
//                        ->where('branch_id', session('branch_id'))
//                        ->update([
//                            'Panalty_Amount'  => DB::raw("ROUND(Panalty_Amount + {$amt}, 2)"),
//                            'Panalty_Balance' => DB::raw("ROUND(Panalty_Balance + {$amt}, 2)"),
//                            'Total_Amount'    => DB::raw("ROUND(Total_Amount + {$amt}, 2)"),
//                            'Total_Balance'   => DB::raw("ROUND(capital_balance + Interest_Balance + Panalty_Balance, 2)"),
//                            'Panelty_status'  => 1,
//                            'Panelty_count'   => DB::raw('COALESCE(Panelty_count,0) + 1'),
//                        ]);
//
//
//                    $user_id= session('userid');
//                    $date=date('Y-m-d');
//                    $time=date('H:i:s');
//
//                    $customer_table=tableWithBranch('customer')
//                        ->where('idCustomer','=',$item->Customer_idCustomer)
//                        ->first();
//
//
//                    $panelty_amount=number_format($panelty_amount, 2,'.','');
//
//                    DB::table('customer_log')->insert([
//                        'customer_id' => $item->Customer_idCustomer,
//                        'customer_name' => $customer_table->First_Name.' '.$customer_table->Last_Name,
//                        'date' => $date,
//                        'time' => $time,
//                        'description' => "{$panelty_amount} LKR Penalty added for ({$item->idCustomer_Loan})\nInstallment No : {$item->idInstallments}",
//                        'description_id' => $item->idInstallments,
//                        'comment' => ' ',
//                        'type' => 'Penalty',
//                        'user' => $user_id,
//                        'branch_id' => session('branch_id')
//                    ]);
//
//
//                    $loanLogController = new LoanLogController();
//
//                    $last_log = DB::table('Loan_Log')->where('Loan_ID','=',$item->idCustomer_Loan)->orderBy('Loan_Log_ID', 'desc')->first();
//                    $Panelty_Balance = number_format((float)$last_log->Panelty_Balance + (float)$panelty_amount, 2, '.', '');
//                    $Total_Pending_Balance = number_format((float)$last_log->Total_Pending_Balance + (float)$panelty_amount, 2, '.', '');
//                    $loanLogController->index(
//                        $item->idCustomer_Loan, 'Penalty', $item->idInstallments,
//                        'Penalty-Installment No : '.$item->idInstallments.' Penalty Count : '.$count, $panelty_amount,
//                        '0.00', '0.00',
//                        '0.00','0.00', $Panelty_Balance,
//                        $last_log->Interest_Balance, $last_log->Capital_Balance, $Total_Pending_Balance, $last_log->Saving_Account_Balance
//                    );
//
//                    Log::info($item->idCustomer_Loan.'-'.$count);
//
//                    $bankLogController = new BankLogController();
//
//                    $System_default_5=tableWithBranch('company_bank_accounts')
//                        ->where('Bank_Type','=','System_default_5')
//                        ->first();
//                    $System_default_6=tableWithBranch('company_bank_accounts')
//                        ->where('Bank_Type','=','System_default_6')
//                        ->first();
//                    $bankLogController->index($System_default_5->Idbank,"Penalty","Penalty","-","debit",$panelty_amount,$System_default_6->Idbank);
//                    $bankLogController->index($System_default_6->Idbank,"Penalty","Penalty","-","credit",$panelty_amount,$System_default_5->Idbank);
//
//                }
//            }
//        }













//        $date=date('Y-m-d');
//
//        $poya=tableWithBranch('holidays')->get();
//        $poyaDates = $poya->pluck('date')->toArray(); // Extract only the dates
//
//
//        $installment=tableWithBranch('installments','installments')
//            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
//            ->where('customer_loan.Status', '=', '0')
//            ->where('installments.Status', '=', '0')
//            ->where('Panelty_status', '=', '0')
//            ->whereDate('Panelty_date', '<=', $date)
//            ->whereNotIn('Panelty_date', $poyaDates) // Exclude dates in $poya
//            ->select('installments.*', 'customer_loan.Panalty_Rate','customer_loan.idCustomer_Loan','customer_loan.Customer_idCustomer')
//            ->get();
//
//
//        foreach ($installment as $item){
//            $total_balance=$item->Total_Balance;
//            $panelty_amount=($total_balance*$item->Panalty_Rate)/100;
//            $newPanaltyBalance = str_replace(',', '', number_format($item->Panalty_Balance + $panelty_amount, 2));
//            $tot_balance = str_replace(',', '', number_format($total_balance + $panelty_amount, 2));
//
//
//            DB::table('installments')
//                ->where('idInstallments', $item->idInstallments)
//                ->where('branch_id', session('branch_id'))
//                ->update([
//                    'Panalty_Amount' => number_format($panelty_amount, 2, '.', ''),
//                    'Panalty_Balance' => number_format($newPanaltyBalance, 2, '.', ''),
//                    'Total_Amount' => DB::raw('ROUND(Total_Amount + ' . $panelty_amount . ', 2)'),
//                    'Total_Balance' => number_format($tot_balance, 2, '.', ''),
//                    'Panelty_status' => '1'
//                ]);
//
//            $user_id= session('userid');
//            $date=date('Y-m-d');
//            $time=date('H:i:s');
//
//            $customer_table=tableWithBranch('customer')
//                ->where('idCustomer','=',$item->Customer_idCustomer)
//                ->first();
//
//            $panelty_amount=number_format($panelty_amount, 2,'.','');
//
//            DB::table('customer_log')->insert([
//                'customer_id' => $item->Customer_idCustomer,
//                'customer_name' => $customer_table->First_Name.' '.$customer_table->Last_Name,
//                'date' => $date,
//                'time' => $time,
//                'description' => "{$panelty_amount} LKR Penalty added for ({$item->idCustomer_Loan})\nInstallment No : {$item->idInstallments}",
//                'description_id' => $item->idInstallments,
//                'comment' => ' ',
//                'type' => 'Penalty',
//                'user' => $user_id,
//                'branch_id' => session('branch_id')
//            ]);
//            $loanLogController = new LoanLogController();
//
//            $last_log = DB::table('Loan_Log')->where('Loan_ID','=',$item->idCustomer_Loan)->orderBy('Loan_Log_ID', 'desc')->first();
//            $Panelty_Balance = number_format((float)$last_log->Panelty_Balance + (float)$panelty_amount, 2, '.', '');
//            $Total_Pending_Balance = number_format((float)$last_log->Total_Pending_Balance + (float)$panelty_amount, 2, '.', '');
//            $loanLogController->index(
//                $item->Customer_idCustomer, 'Penalty', $item->idInstallments,
//                'Penalty-Installment No : '.$item->idInstallments, $panelty_amount,
//                '0.00', '0.00',
//                '0.00','0.00', $Panelty_Balance,
//                $last_log->Interest_Balance, $last_log->Capital_Balance, $Total_Pending_Balance, $last_log->Saving_Account_Balance
//            );
//
//            $bankLogController = new BankLogController();
//
//            $System_default_5=tableWithBranch('company_bank_accounts')
//                ->where('Bank_Type','=','System_default_5')
//                ->first();
//            $System_default_6=tableWithBranch('company_bank_accounts')
//                ->where('Bank_Type','=','System_default_6')
//                ->first();
//            $bankLogController->index($System_default_5->Idbank,"Penalty","Penalty","-","debit",$panelty_amount,$System_default_6->Idbank);
//            $bankLogController->index($System_default_6->Idbank,"Penalty","Penalty","-","credit",$panelty_amount,$System_default_5->Idbank);
//
//
//        }







    }
    */


    public function privileges(Request $request,Store $session)
    {
        $userId = $request->input('userId');
        $privileges = $request->input('privileges', []);

        // Get user details for description
        $user = DB::table('user')->where('id', $userId)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }

        // Store privilege change data for approval
        $requestData = [
            'user_id' => $userId,
            'privileges' => $privileges,
            'user_email' => $user->email
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'User Privilege Change',
            'typeid' => 103,
            'description' => 'User Privilege Change: ' . $user->Full_Name . ' (' . $user->email . ')',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['status' => 'success', 'message' => 'Privilege change request sent for approval!']);
    }

    public function showprivileges($id)
    {
        $permissions = DB::table('user_privileges_has_user')
            ->where('user_id', $id)
            ->select('permission_key', 'value')
            ->get();

        return response()->json(['privileges' => $permissions]);
    }


    public function check_mail(Request $request){

        $email = $request->email;
        $user = DB::table('user')->where('email', $email)->first();

        if ($user) {

            $branch_id=$user->branch_id;
            $client = new Client([
                'base_uri' => 'https://e-sms.dialog.lk/api/v1/',
            ]);

            $response = $client->post('login', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'username' => 'ASIPIYA',
                    'password' => 'Dialog@123',
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            $token=$responseData['token'];
            if (!$token) {
                return response()->json(['error' => 'Token not found in session'], 401);
            }


            $newTransactionId = random_int(1, 999999999999999999);


            $otp = random_int(100000, 999999);

            // Update OTP in the database
            DB::table('user')
                ->where('email', $email)
                ->update(['otp' => $otp]);

            $message="Your OTP is ".$otp;

            $company=DB::table('company')->where('branch_id','=',$branch_id)->first();

            $client_data = new Client([
                'base_uri' => 'https://e-sms.dialog.lk/api/v1/',
            ]);


            $response_data = $client_data->post('sms', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'msisdn' => [
                        [
                            'mobile' => $user->TP,
                        ]
                    ],
                    'message' => $message,
                    'transaction_id' => $newTransactionId,
                    'payment_method' => 0,
                    'sourceAddress' => $company->mask,
                ],
            ]);
            $responseData_result = json_decode($response_data->getBody()->getContents(), true);

            if ($responseData_result['status']==="success") {

                $user_details=DB::table('user')->where('email',$email)->first();

                DB::table('sms')->insert([
                    'cus_id' => $user_details->id,
                    'cus_name' => $user_details->Full_Name,
                    'contact_no' => $user->TP,
                    'message' => $message,
                    'type' => "OTP",
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'branch_id' => $branch_id
                ]);
                return view('recover_password',compact('email'));
            }else{
                return redirect()->route('forget_password')->with("error", "Please contact your provider !");
            }


        } else {
            return redirect()->route('forget_password')->with("error", "Please check your email address !");
        }
    }


    public function recover_password(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,email',
            'otp' => 'required|numeric',
            'password' => 'required|confirmed', // 'confirmed' requires a matching 'password_confirmation' field
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $password = $request->password;

        if ($validator->fails()) {
            return view('recover_password',compact('email'));
        }

        // Check OTP
        $user = DB::table('user')->where('email', $email)->where('otp', $otp)->first();

        if (!$user) {
            return redirect()->route('user.recover_password')->with('error', 'Invalid OTP.');
        }
        $otp = random_int(100000, 999999);
        // Update password
        DB::table('user')->where('email', $email)->update([
            'password' => Hash::make($password),
            'otp' => $otp
        ]);

        return redirect()->route('login')->with('success', 'Password updated successfully. Please login.');
    }


    public function designation(Request $request){
        $designation=DB::table('designation')->insert([
            'name' => $request->designation,
            'desi_level' => $request->desi_level,
            'loan_creat' => $request->loan_create,
            'loan_issue' => $request->loan_approve,
            'max_create_amount' => str_replace(',', '', $request->max_amount_create),
            'max_issue_amount' => str_replace(',', '', $request->max_amount_approve),
            'branch_id' => session('branch_id')
        ]);
        if ($designation){
            return response()->json(['data' => $designation], 200);
        }
        return response()->json(['data' => $designation], 404);
    }

    public function updatedesignation(Request $request){
        // Get current designation for comparison
        $designation = DB::table('designation')
            ->where('idDesignation', '=', $request->id)
            ->where('branch_id', session('branch_id'))
            ->first();

        if (!$designation){
            return response()->json(['error' => 'Designation not found'], 404);
        }

        // Store old values for comparison
        $oldData = [
            'name' => $designation->name,
            'desi_level' => $designation->desi_level,
            'loan_creat' => $designation->loan_creat,
            'loan_issue' => $designation->loan_issue,
            'max_create_amount' => $designation->max_create_amount,
            'max_issue_amount' => $designation->max_issue_amount,
        ];

        // Store new values
        $newData = [
            'name' => $request->designation,
            'desi_level' => $request->desiLevel,
            'loan_creat' => $request->loanCreate,
            'loan_issue' => $request->loanApprove,
            'max_create_amount' => $request->maxCreateAmount,
            'max_issue_amount' => $request->maxIssueAmount,
        ];

        // Store designation details update data for approval
        $requestData = [
            'designation_id' => $request->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'update_type' => 'details'
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'Designation Privileges Update',
            'typeid' => 201,
            'description' => 'Designation Details Update: ' . $request->designation . ' (Max Create: ' . $request->maxCreateAmount . ', Max Approve: ' . $request->maxIssueAmount . ')',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['status' => 'success', 'message' => 'Designation update request sent for approval!'], 200);
    }

    // Save designation privileges JSON
    public function saveDesignationPrivileges(Request $request)
    {
        $designationId = $request->input('designationId');
        $privileges = $request->input('privileges', []);

        if(!$designationId){
            return response()->json(['error' => 'Invalid designation id'], 422);
        }

        // ensure designation belongs to current branch
        $designation = DB::table('designation')
            ->where('idDesignation', $designationId)
            ->where('branch_id', session('branch_id'))
            ->first();

        if(!$designation){
            return response()->json(['error' => 'Designation not found'], 404);
        }

        // Get old privileges for comparison
        $oldPrivileges = $designation->privileges ? json_decode($designation->privileges, true) : [];

        // Store designation privilege change data for approval
        $requestData = [
            'designation_id' => $designationId,
            'privileges' => $privileges,
            'old_privileges' => $oldPrivileges,
            'designation_name' => $designation->name
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'Designation Privileges Update',
            'typeid' => 201,
            'description' => 'Designation Privileges Update: ' . $designation->name,
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['status' => 'success', 'message' => 'Designation privilege change request sent for approval!']);
    }

    // Load designation privileges JSON
    public function loadDesignationPrivileges($id)
    {
        $designation = DB::table('designation')
            ->where('idDesignation', $id)
            ->where('branch_id', session('branch_id'))
            ->select('privileges')
            ->first();

        if(!$designation){
            return response()->json(['privileges' => (object)[]]);
        }

        $privileges = [];
        if($designation->privileges){
            $decoded = json_decode($designation->privileges, true);
            if(is_array($decoded)){
                $privileges = $decoded;
            }
        }
        return response()->json(['privileges' => $privileges]);
    }

    // Check if a designation exists in a specific branch
    public function designationExists(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|integer',
        ]);

        $exists = DB::table('designation')
            ->where('branch_id', $request->branch_id)
            ->where('name', $request->name)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    // Create designation in a specific branch (optionally clone privileges from same-name in current branch)
    public function createDesignationForBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|integer',
        ]);

        // If already exists, return success
        $exists = DB::table('designation')
            ->where('branch_id', $request->branch_id)
            ->where('name', $request->name)
            ->exists();
        if ($exists) {
            return response()->json(['success' => true, 'message' => 'Designation already exists']);
        }

        // Try to clone privileges from same-name designation in current session branch if available
        $source = DB::table('designation')
            ->where('branch_id', session('branch_id'))
            ->where('name', $request->name)
            ->first();

        $data = [
            'name' => $request->name,
            'desi_level' => $source->desi_level ?? 1,
            'loan_creat' => $source->loan_creat ?? 0,
            'loan_issue' => $source->loan_issue ?? 0,
            'max_create_amount' => $source->max_create_amount ?? 0,
            'max_issue_amount' => $source->max_issue_amount ?? 0,
            'privileges' => $source->privileges ?? null,
            'branch_id' => $request->branch_id,
        ];

        DB::table('designation')->insert($data);

        return response()->json(['success' => true]);
    }



    public function holidays(){
        $year = date('Y');

        $loans = tableWithBranch('customer_loan')->where('Status', '0')->get();
        $branches = DB::table('branch')->where('Status', '1')->get();
        $centers = tableWithBranch('center')->get();
        $products = tableWithBranch('loan_category')->get();

// Get holidays for the year
        $holidays = tableWithBranch('holidays')->get();

// Format holiday dates to Y-m-d (in case they include time)
        $holidayDates = $holidays->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        })->unique();

// Get all installment dates (formatted)
        $installmentDates = tableWithBranch('installments','installments')
            ->join('customer_loan','customer_loan.idCustomer_Loan','=','installments.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.Status','=','0')
            ->pluck('installments.Installment_Date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })->unique();

// Intersect to find how many holiday dates have installments
        $matchingDates = $holidayDates->intersect($installmentDates);
        $holidayWithInstallmentsCount = $matchingDates->count();



        return view('pages.Holidays', compact('holidays','year','loans','branches','centers','products','holidayWithInstallmentsCount'));
    }


    public function holidays_save(Request $request)
    {
        $holidayDate = $request->date;
        $reason = $request->reason;

        // Check if the date is already a holiday
        if (tableWithBranch('holidays')->where('date', $holidayDate)->exists()) {
            return response()->json(["id" => "0"], 200);
        }

        // Insert the new holiday
        $Holidays = [
            'date' => $holidayDate,
            'reason' => $reason,
            'created_at' => now(),
        ];
        insertWithBranch('holidays', $Holidays);

        return response()->json(["id" => "1"], 200);
    }


    public function poya_days_save(Request $request)
    {
        $poyaDays = $request->input('poyaDays'); // Expecting an array of Poya Days

        foreach ($poyaDays as $poyaDay) {
            // Check if the date already exists in the holidays table
            if (!tableWithBranch('holidays')->where('date', '=', $poyaDay['date'])->exists()) {
                // Prepare the holiday data
                $Holidays = [
                    'date' => $poyaDay['date'],
                    'reason' => $poyaDay['description'],
                    'created_at' => now(),
                ];

                // Insert the Poya Day
                insertWithBranch('holidays', $Holidays);
            }
        }


        return response()->json(["message" => "Poya Days saved successfully!"], 200);
    }



    public function deleteHoliday($id)
    {
        // Fetch the holiday by ID using DB::table
        $holiday = tableWithBranch('holidays')->where('id_holidays', $id)->first();

        // Check if the holiday exists
        if (!$holiday) {
            return response()->json(['success' => false, 'message' => 'Holiday not found.'], 404);
        }

        // Check if the holiday date is today or in the past
        if (Carbon::parse($holiday->date)->lte(Carbon::today())) {
            return response()->json(['success' => false, 'message' => 'Cannot delete holidays that are today or in the past.'], 400);
        }


        // Delete the holiday using DB::table
        tableWithBranch('holidays')->where('id_holidays', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Holiday deleted successfully.']);
    }


    public function generateDueSkip(Request $request)
    {
        $skipFor = $request->skip_for;
        $targetId = $request->target_id;
        $skipType = $request->skip_type;
        $holiday=new HolidayController();
        $holiday->index($skipFor,$targetId,$skipType);

    }


    public function getUserDetails($id)
    {
        $user = DB::table('user')->where('id', $id)->first();
        $user->branches = DB::table('user_has_branches')->where('user_id', $id)->pluck('branch_id')->toArray();
        return response()->json($user);
    }


    public function updateUser(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'epf_no' => 'required',
            'desi' => 'required',
            'nic' => 'required',
            'full_name' => 'required',
            'email' => 'required|email',
            'tp' => 'required',
        ]);

        // Load existing user and branches
        $user = DB::table('user')->where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $existingBranches = DB::table('user_has_branches')
            ->where('user_id', $user->id)
            ->pluck('branch_id')
            ->map(fn($v) => (string)$v)
            ->toArray();

        $newBranches = collect($request->input('branches', []))
            ->map(fn($v) => (string)$v)
            ->toArray();

        // Determine if branches actually changed (order-insensitive)
        sort($existingBranches);
        sort($newBranches);
        $branchesChanged = ($existingBranches !== $newBranches);

        // Determine primary branch: first of new list if present, else keep current
        $primaryBranch = count($newBranches) > 0 ? (int)$newBranches[0] : (int)$user->branch_id;

        // Store update data for approval
        $updateData = [
            'user_id' => $user->id,
            'email' => $request->email,
            'Epf_no' => $request->epf_no,
            'Designation' => $request->desi,
            'Nic' => $request->nic,
            'Full_Name' => $request->full_name,
            'TP' => $request->tp,
            'lending_officer' => $request->boolean('editLendingOfficer') ? 1 : 0,
            'collector' => $request->boolean('editCollectingOfficer') ? 1 : 0,
            'branch_id' => $primaryBranch,
            'branch_access' => $request->boolean('branch_access') ? 1 : 0,
            'cashier' => $request->boolean('editcashier') ? 1 : 0,
        ];

        $requestData = [
            'update_data' => $updateData,
            'new_branches' => $newBranches,
            'branches_changed' => $branchesChanged
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'User Details Update',
            'typeid' => 102,
            'description' => 'User Details Update: ' . $request->full_name . ' (' . $request->email . ')',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['success' => true, 'message' => 'User update request sent for approval!']);
    }

    public function resetPassword($id,Request $request)
    {
        $updated = DB::table('user')
            ->where('id', $id)
            ->update(['password' => Hash::make($request->newPassword)]);

        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    public function getHolidays()
    {
        // Retrieve all holiday dates from the database using DB::table()
        $holidays = DB::table('holidays') // Replace 'holidays' with your actual table name
        ->select('date')
            ->get()
            ->pluck('date')
            ->toArray(); // Convert the collection to an array

        return response()->json($holidays); // Return the dates as JSON
    }

    /**
     * Apply designation privileges to a new user
     */
    private function applyDesignationPrivilegesToUser($userId, $designationIdentifier)
    {
        if (!$designationIdentifier) {
            return; // No designation provided
        }

        // Try to find designation by name first, then by ID as fallback
        $designation = DB::table('designation')
            ->where('branch_id', session('branch_id'))
            ->where(function($query) use ($designationIdentifier) {
                $query->where('name', $designationIdentifier)
                      ->orWhere('idDesignation', $designationIdentifier);
            })
            ->first();

        if (!$designation || !$designation->privileges) {
            return; // No designation found or no privileges set
        }

        // Parse the JSON privileges
        $privileges = json_decode($designation->privileges, true);
        
        if (!is_array($privileges)) {
            return; // Invalid JSON or not an array
        }

        // Insert each privilege for the user
        foreach ($privileges as $permissionKey => $value) {
            DB::table('user_privileges_has_user')->updateOrInsert(
                ['user_id' => $userId, 'permission_key' => $permissionKey],
                ['value' => $value]
            );
        }

        Log::info("Applied designation privileges for user {$userId} from designation '{$designation->name}': " . count($privileges) . " permissions applied");
    }

    /**
     * Delete a designation
     */
    public function deleteDesignation(Request $request)
    {
        try {
            $designationId = $request->id;
            $branchId = session('branch_id');
            
            // Check if designation exists in current branch
            $designation = DB::table('designation')
                ->where('idDesignation', $designationId)
                ->where('branch_id', $branchId)
                ->first();

            if (!$designation) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Designation not found in current branch'
                ], 404);
            }

            // Check if any users are using this designation
            $usersCount = DB::table('user')
                ->where('Designation', $designation->name)
                ->where('branch_id', $branchId)
                ->count();

            if ($usersCount > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => "Cannot delete designation '{$designation->name}'. It is currently assigned to {$usersCount} user(s). Please reassign users before deleting."
                ], 400);
            }

            // Delete the designation
            $deleted = DB::table('designation')
                ->where('idDesignation', $designationId)
                ->where('branch_id', $branchId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => "Designation '{$designation->name}' has been successfully deleted."
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete designation.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting designation: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while deleting the designation.'
            ], 500);
        }
    }


}
