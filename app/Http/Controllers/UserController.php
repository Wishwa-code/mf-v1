<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BankLogController;
use App\Jobs\GenerateDueSkipJob;
use App\Jobs\RunRecoverySweepJob;
use App\Models\LoginUser;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
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
        $userData = tableWithBranch('user')->get();
        $designation = tableWithBranch('designation')->get();
        $branch = DB::table('branch')->where('branch_id', '=', session('branch_id'))->get();
        if (session('branch_access') == 1) {
            $branch = DB::table('branch')->get();
        }

        return view('pages.User', compact('userData', 'designation', 'branch'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $tp = $request->tp;
        if ($tp === null) {
            $tp = "-";
        }

        if (DB::table('user')->where('email', '=', $request->email)->exists()) {
            return redirect()->intended(route('pages.user'))->with("error", "This user is already exist !");
        } else {

            // Generate OTP
            $otp = Str::random(6); // Or use a more secure method to generate OTP




            $data['Full_Name'] = $request->full_name;
            $data['email'] = $request->email;
            $data['password'] = Hash::make($request->password);
            $data['TP'] = $tp;
            $data['Designation'] = $request->desi;
            $data['Epf_no'] = $request->epf_no;
            $data['Nic'] = $request->nic;
            $data['lending_officer'] = $request->has('lending_officer') ? 1 : 0;
            $data['otp'] = $otp;
            $data['branch_id'] = $request->branches[0] ?? session('branch_id');
            $data['branch_access'] = $request->has('branch_access') ? 1 : 0;
            $data['cashier'] = $request->has('cashier') ? 1 : 0;
            $data['collector'] = $request->has('collecting_officer') ? 1 : 0;

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
                'userid' => user_data('idUser'),
                'branch_id' => session('branch_id'),
                'data_time' => now(),
                'status' => 0
            ]);

            return redirect()->intended(route('pages.user'))->with("success", "User creation request sent for approval!");
        }
    }

    //

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

                $session->put('userid', (int) $item->id);
                $session->put('Full_Name', $item->Full_Name);
                $session->put('designation', $item->Designation);
                $session->put('branch_id', (int) $item->branch_id);
                $session->put('branch_access', (int) $item->branch_access);
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

            // Call to the penalty creation function
            $this->create_panelty();
            return redirect()->intended(route('home'));
        }

        // Authentication failed
        return redirect()->route('login')->with("error", "Login details are not valid!");
    }

//    public function store(Request $request, Store $session)
//    {
//        $request->validate([
//            'email'    => 'required|email',
//            'password' => 'required',
//        ]);
//
//        $email    = $request->email;
//        $password = $request->password;
//
//        /**
//         * 1) Read login mapping from MAIN DB (asipiya_main)
//         *    Here LoginUser uses the default "mysql" connection which should point to asipiya_main.
//         *    If you gave LoginUser a $connection = 'main', then make sure 'main' exists in config/database.php
//         */
//        $loginUser = LoginUser::where('email', $email)
//            ->where('active', 1)
//            ->first();
//
//        if (! $loginUser) {
//            return back()->with('error', 'Login details are not valid! (Email not registered)');
//        }
//
//        // Tenant DB name is stored in db_name column of login_users
//        $tenantDbName = $loginUser->db_name;
//
//        /**
//         * 2) Save tenant DB name into session for future requests
//         *    (SetTenantConnection middleware will read this)
//         */
//        $session->put('tenant_db', $tenantDbName);
//        $session->put('login_email', $email);
//        $session->save();
//
//        /**
//         * 3) Configure TENANT connection for THIS request
//         *    (we only change the database name; host/user/pass come from env)
//         */
//        Config::set('database.connections.tenant.database', $tenantDbName);
//        Config::set('database.default', 'tenant');   // so DB::table() uses tenant
//
//        DB::purge('tenant');        // clear old connection cache
//        DB::reconnect('tenant');    // reconnect with new DB
//
//        // Make sure the default guard is web (uses App\Models\User with $connection='tenant')
//        Auth::shouldUse('web');
//
//        /**
//         * 4) Log in using TENANT DB `user` table
//         */
//        $credentials = [
//            'email'    => $email,
//            'password' => $password,
//        ];
//
//        if (! Auth::attempt($credentials)) {
//            return back()->with('error', 'Login details are not valid!');
//        }
//
//        // Regenerate session ID after login for security
//        $request->session()->regenerate();
//
//        /**
//         * 5) Load extra user/session info from TENANT DB
//         *    Now DB::table() hits the tenant database.
//         */
//        $userRows = DB::table('user')->where('email', $email)->get();
//
//        foreach ($userRows as $item) {
//            $session->put('userid',        (int) $item->id);
//            $session->put('username',      $item->email);
//            $session->put('Full_Name',     $item->Full_Name);
//            $session->put('designation',   $item->Designation);
//            $session->put('branch_id',     (int) $item->branch_id);
//            $session->put('branch_access', (int) $item->branch_access);
//
//            // These helpers now also use tenant DB because default is 'tenant'
//            $company = tableWithBranch('company')->first();
//            if ($company) {
//                $session->put('company_name', $company->company_name);
//            }
//
//            $branch = DB::table('branch')
//                ->where('branch_id', '=', $item->branch_id)
//                ->first();
//
//            if ($branch) {
//                $session->put('branch_name', $branch->Name);
//            }
//
//            if ($item->Status === "0") {
//                Auth::logout();
//                $request->session()->invalidate();
//                $request->session()->regenerateToken();
//
//                return redirect()->route('login')
//                    ->with('error', 'Please contact Admin! (User deactivated)');
//            }
//        }
//
//        /**
//         * 6) Example column checks on TENANT DB
//         */
//        if (! Schema::connection('tenant')->hasColumn('company_bank_has_log', 'log_tracking_no')) {
//            DB::statement("ALTER TABLE company_bank_has_log ADD log_tracking_no VARCHAR(10) NULL");
//        }
//
//        if (! Schema::connection('tenant')->hasColumn('loan_category', 'status')) {
//            DB::statement("ALTER TABLE loan_category ADD COLUMN status TINYINT DEFAULT 1");
//        }
//
//        // 7) Go to dashboard – from now on, SetTenantConnection middleware
//        //    will read session('tenant_db') and switch DB for each request
//        return redirect()->intended(route('home'));
//    }

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
                'userid' => user_data('idUser'),
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

        $user = tableWithBranch('user')->where('idUser', $request->user_id)->get();
        foreach ($user as $item) {
            $data['password'] = Hash::make($request->c_pass);
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

    function syncRecoveryAccountsForBranch()
    {
        $branchId = session('branch_id');        // you already use session('branch_id') everywhere in your system
        $userId   = user_data('idUser');           // who is doing this sync
        $now      = Carbon::now();

        // 1. get all active customers in this branch
        //    adjust "Status" column value if your active is '1'
        $customers = DB::table('customer')
            ->select('idCustomer')
            ->where('branch_id', $branchId)
            ->where('Status', '1')
            ->get();

        $createdCount = 0;

        foreach ($customers as $cus) {
            // 2. check if recovery_account already exists
            $existing = DB::table('recovery_account')
                ->where('customer_id', $cus->idCustomer)
                ->where('branch_id', $branchId)
                ->first();

            if ($existing) {
                continue; // already has account, skip
            }

            try {
                DB::beginTransaction();

                // 3. create recovery_account with 0 balance
                $recoveryAccountId = DB::table('recovery_account')->insertGetId([
                    'customer_id'      => $cus->idCustomer,
                    'current_balance'  => 0.00,
                    'status'           => 'Active',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                    'created_by'       => $userId,
                    'updated_by'       => $userId,
                    'branch_id'        => $branchId,
                ]);

                // 4. insert opening log row (action_type = OPEN)
                DB::table('recovery_account_log')->insert([
                    'recovery_account_id' => $recoveryAccountId,
                    'loan_id'             => '0',
                    'customer_id'         => $cus->idCustomer,
                    'action_type'         => 'OPEN',
                    'description'         => 'Recovery account created with opening balance 0.00',
                    'amount'              => 0.00,
                    'balance_after'       => 0.00,
                    'created_at'          => $now,
                    'created_by'          => $userId,
                    'branch_id'           => $branchId,
                ]);

                DB::commit();

                $createdCount++;
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('Failed to create recovery account', [
                    'customer_id' => $cus->idCustomer,
                    'branch_id'   => $branchId,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return $createdCount;
    }



    public function showdashboard(Store $session)
    {

        // Head Office aggregated dashboard: show all branches overview
        if ((int)session('head_branch') == session('branch_id')) {
            // Fetch active branches from session (excluding head office which is filtered in login)
            $branches = user_data('branches') ?? [];

            $branchMetrics = [];
            foreach ($branches as $b) {
                // Ensure array access for session data
                $branchId = $b['idBranch'];
                $branchName = $b['Name'];

                // Helper closure forcing branch scope manually
                $scoped = function ($table) use ($branchId) {
                    // dd($table,  $branchId);
                    return DB::table($table)->where($table . '.branch_id', $branchId);
                };

                $customers = $scoped('customer')->count();
                $loanPendingQ = $scoped('customer_loan')->where('Status', '-1');
                $loanCurrentQ = $scoped('customer_loan')->where('Status', '0');
                $loanSettledQ = $scoped('customer_loan')->where('Status', '1');
                $pendingCount = $loanPendingQ->count();
                $pendingAmount = $scoped('customer_loan')->where('Status', '-1')->sum('Amount');
                $currentCount = $loanCurrentQ->count();
                $currentAmount = $scoped('customer_loan')->where('Status', '0')->sum('Amount');
                $settledCount = $loanSettledQ->count();
                $portfolio = $scoped('installments')->sum('capital_balance');
                $todayInstallment = DB::table('installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id', $branchId)
                    ->where('customer_loan.branch_id', $branchId)
                    ->whereDate('installments.Installment_Date', date('Y-m-d'))
                    ->where('customer_loan.Status', '0')
                    ->sum('installments.Total_Balance');
                $todayCollected = $scoped('customer_payments')->where('Date', date('Y-m-d'))->sum('Amount');
                // arrears: overdue installments (date < today) still active
                $arrears = DB::table('installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id', $branchId)
                    ->where('customer_loan.branch_id', $branchId)
                    ->where('customer_loan.Status', '0')
                    ->whereDate('installments.Installment_Date', '<', date('Y-m-d'))
                    ->sum('installments.Total_Balance');

                $branchMetrics[] = [
                    'id' => $branchId,
                    'name' => $branchName,
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

        if (!Schema::hasColumn('company', 'inv_customer_number')) {
            DB::statement("
        ALTER TABLE `company`
        ADD COLUMN `inv_customer_number` VARCHAR(45) NOT NULL DEFAULT '1'
    ");
        }



        $loan = tableWithBranch('customer_loan')->where('Status', '!=', '1')->get();
        $CapitalBalanceController = new CapitalBalanceController();
        foreach ($loan as $loans) {
            $CapitalBalanceController->create($loans->idCustomer_Loan);
        }


        //        $CapitalBalanceController->panelty_remove();


        // Call to the penalty creation function
        // $this->create_panelty();


        $customerCount = tableWithBranch('customer')->count();
        $customer_loan_pending_Count = tableWithBranch('customer_loan')->where('Status', '=', '-1')->count();
        $customer_loan_pending_Amount = tableWithBranch('customer_loan')->where('Status', '=', '-1')->sum('Amount');
        $customer_loan_current_Count = tableWithBranch('customer_loan')->where('Status', '=', '0')->count();
        $customer_loan_current_Amount = tableWithBranch('customer_loan')->where('Status', '=', '0')->sum('Amount');
        $setteled_loan_Count = tableWithBranch('customer_loan')->where('Status', '=', '1')->count();
        $deleted_loan_Count = tableWithBranch('customer_loan')->where('Status', '=', '-2')->count();
        $setteled_loan_current_Amount = tableWithBranch('customer_loan')->where('Status', '=', '1')->sum('Amount');
        $portfolio = tableWithBranch('installments', 'installments')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('cl.Status', 0)
            ->sum('installments.capital_balance');

        $currentMonthStart = date('Y-m-01 00:00:00'); // Start of month
        $todayEnd = date('Y-m-d 23:59:59');          // End of today

        $currentMonthLending = tableWithBranch('customer_loan')
            ->whereBetween('Date_Time', [$currentMonthStart, $todayEnd])
            ->where('Status', '0')
            ->sum('Amount');


        $todayinstallment = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('installments.Installment_Date', '=', date('Y-m-d'))
            ->where('customer_loan.Status', '=', '0')
            ->sum('installments.Installment_Amount');

        $todayinstallment_balance = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('installments.Installment_Date', '=', date('Y-m-d'))
            ->where('customer_loan.Status', '=', '0')
            ->sum('installments.Total_Balance');

        $todayNotPaid = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('installments.Installment_Date', '=', date('Y-m-d'))
            ->where('customer_loan.Status', '=', '0')
            ->sum('installments.Total_Balance');
        $todaycollected = tableWithBranch('customer_payments')->where('Date', date('Y-m-d'))->sum('Amount');



        $checqueamount = tableWithBranch('Cheque_payment')->where('payment_date', date('Y-m-d'))->where('chq_status', '=', '0')->sum('payment_amount');
        $shortcut = tableWithBranch('shortcut')->get();
        $shortcut_count = tableWithBranch('shortcut')->count();

        $all_loan = $customer_loan_current_Count + $setteled_loan_Count;



        $todaycollection = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->select('customer.*', 'customer_group.Name as group_name', 'installments.*', 'customer_loan.*', 'loan_category.Name as loan_name')
            ->whereDate('Installment_Date', '=', date('Y-m-d'))
            ->where('installments.Status', '=', '0')
            ->where('customer_loan.Status', '=', '0')
            ->get();


        $loanQuery_2 = tableWithBranch('installments', 'installments')
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
        $totalBalanceUntil = $totalBalanceUntil + $checqueamount;

        // Total Outstanding: capital balance + interest balance where status = 0
        $totalOutstanding = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $totalOutstanding = $totalOutstanding->total_outstanding ?? 0;

        // Penalty Balance: sum of penalty balance where status = 0
        $penaltyBalance = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.Panalty_Balance) as penalty_balance')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $penaltyBalance = $penaltyBalance->penalty_balance ?? 0;

        $userid = user_data('idUser');

        $dashboard = 0;
        $privileges = user_data('privileges') ?? [];
        if (is_array($privileges)) {
            foreach ($privileges as $priv) {
                if (isset($priv['Description']) && strtolower($priv['Description']) === 'dashboard') {
                    $dashboard = strtolower($priv['Description']);
                    break;
                }
            }
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


        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY); // Sun 2025-04-20 00:00:00
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY);     // Sat 2025-04-26 23:59:59
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


        $user = tableWithBranch('user')->where('id', '=', $userid)->first();


        if ($user) {
            if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $userid)->exists()) {
            } else {

                $Bank = [
                    'Bank_Type' => "Collector",
                    'code' => $user->id . '/Collector',
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

        // ----------------------
        // Date boundaries
        // ----------------------
        $today      = Carbon::today();
        $todayDate  = $today->toDateString();
        $weekStart  = $today->copy()->startOfWeek(Carbon::SUNDAY)->toDateString();   // Sunday
        $weekEnd    = $today->copy()->endOfWeek(Carbon::SATURDAY)->toDateString();   // Saturday

        // ----------------------
        // 1) THIS WEEK ARREARS
        //    (Sunday → yesterday)
        // ----------------------
        if ($todayDate > $weekStart) {
            $arrearsEnd = $today->toDateString();


            $weeklyUnpaidQuery = tableWithBranch('installments', 'installments')
                ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->where('customer_loan.Status', '=', '0')
                ->where('installments.Status', '=', '0')
                ->where('installments.Total_Balance', '>', 0)
                ->whereBetween('installments.Installment_Date', [$weekStart, $arrearsEnd]);

            $weeklyUnpaidCount = (clone $weeklyUnpaidQuery)->count();
            $weeklyUnpaidAmount = (clone $weeklyUnpaidQuery)->sum('installments.Total_Balance');
            $weeklyUnpaidCustomerCount = (clone $weeklyUnpaidQuery)
                ->distinct()
                ->count('customer_loan.Customer_idCustomer');
        } else {
            // If today is Sunday – no arrears yet for "this week"
            $weeklyUnpaidCount = 0;
            $weeklyUnpaidAmount = 0;
            $weeklyUnpaidCustomerCount = 0;
        }

        // ----------------------
        // 2) CURRENT WEEK PENDING
        //    (today → Saturday)
        // ----------------------
        $currentWeekPendingQuery = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('installments.Total_Balance', '>', 0)
            ->whereBetween('installments.Installment_Date', [$weekStart, $weekEnd]);

        $currentWeekPendingCount = (clone $currentWeekPendingQuery)->count();
        $currentWeekPendingAmount = (clone $currentWeekPendingQuery)->sum('installments.Total_Balance');
        $currentWeekPendingCustomerCount = (clone $currentWeekPendingQuery)
            ->distinct()
            ->count('customer_loan.Customer_idCustomer');




        return view('home', compact(
            'currentMonthLending',
            'portfolio',
            'profit',
            'todaycollected',
            'profitTarget',
            'weeklyComparison',
            'deleted_loan_Count',
            'all_loan',
            'monthlyData',
            'dashboard',
            'checqueamount',
            'totalBalanceUntil',
            'arrease',
            'todayInstallment',
            'setteled_loan_current_Amount',
            'customer_loan_pending_Amount',
            'customer_loan_current_Amount',
            'setteled_loan_Count',
            'shortcut_count',
            'shortcut',
            'customerCount',
            'customer_loan_pending_Count',
            'customer_loan_current_Count',
            'todayinstallment',
            'todaycollection',
            'todayNotPaid',
            'weeklyUnpaidCount',
            'weeklyUnpaidAmount',
            'weeklyUnpaidCustomerCount',
            'totalOutstanding',
            'penaltyBalance',
            'currentWeekPendingCount',
            'currentWeekPendingAmount',
            'currentWeekPendingCustomerCount',
            'todayinstallment_balance'
        ));
    }

    public function currentWeekPendingData()
    {
        // Get current week date range (Sunday to Saturday)
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SATURDAY)->toDateString();

        $currentWeekPendingData = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',
                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),

                // ✅ IMPORTANT: DO NOT SELECT center.No / center.Name directly
                DB::raw('MAX(center.idCenter) as center_id'),
                DB::raw('IFNULL(MAX(CONCAT(center.No, " - ", center.Name)), "-") as center_name'),



                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                // Current week pending amount (installments between Sunday and Saturday of current week)
                DB::raw('SUM(CASE WHEN installments.Installment_Date BETWEEN "' . $weekStart . '" AND "' . $weekEnd . '" THEN installments.Total_Balance ELSE 0 END) as current_week_pending'),
                // Total arrears (all overdue installments)
                DB::raw('SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END) as total_arrears'),
                // Not paid installment count
                DB::raw('COUNT(CASE WHEN installments.Total_Balance > 0 AND installments.Status = 0 THEN 1 END) as not_paid_installment_count')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('installments.Total_Balance', '>', 0)
            ->whereBetween('installments.Installment_Date', [$weekStart, $weekEnd])
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('current_week_pending > 0')
            ->orderBy('current_week_pending', 'DESC')
            ->get();

        return response()->json(['data' => $currentWeekPendingData]);
    }

    public function fixLoanInstallmentsOnce(int $loanId): void
    {
        DB::transaction(function () use ($loanId) {

            // 1. Read current installments for this loan in No ASC
            $rows = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->orderBy('No', 'asc')
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) {
                return;
            }

            // --- insert missing No=2 if No=3 exists ---
            $hasNo2 = $rows->firstWhere('No', 2);
            if (!$hasNo2) {
                $row3 = $rows->firstWhere('No', 3);
                if ($row3) {
                    $row1 = $rows->firstWhere('No', 1);

                    $intendedDate = Carbon::parse($row3->Installment_Date)->subDays(7);
                    $paneltyDate  = $intendedDate->copy()->addDays(14);

                    if ($row1) {
                        $d1 = Carbon::parse($row1->Installment_Date);
                        if ($intendedDate->lte($d1)) {
                            $intendedDate = $d1->copy()->addDays(7);
                            $paneltyDate  = $intendedDate->copy()->addDays(14);
                        }
                    }

                    $d3 = Carbon::parse($row3->Installment_Date);
                    if ($intendedDate->gte($d3)) {
                        $intendedDate = $d3->copy()->subDays(7);
                        $paneltyDate  = $intendedDate->copy()->addDays(14);
                    }

                    $existingDates = tableWithBranch('installments')
                        ->where('Customer_Loan_idCustomer_Loan', $loanId)
                        ->pluck('Installment_Date')
                        ->map(fn($d) => Carbon::parse($d)->toDateString())
                        ->toArray();

                    while (in_array($intendedDate->toDateString(), $existingDates, true)) {
                        $intendedDate->addDays(7);
                        $paneltyDate  = $intendedDate->copy()->addDays(14);
                    }

                    tableWithBranch('installments')->insert([
                        'Customer_Loan_idCustomer_Loan' => $row3->Customer_Loan_idCustomer_Loan,
                        'branch_id'                     => $row3->branch_id,
                        'No'                            => 2, // placeholder, we'll renumber
                        'Installment_Date'              => $intendedDate->toDateString(),
                        'Panelty_date'                  => $paneltyDate->toDateString(),
                        'Installment_Amount'            => $row3->Installment_Amount,
                        'capital_amount'                => $row3->capital_amount,
                        'interest_amount'               => $row3->interest_amount,
                        'Panalty_Amount'                => $row3->Panalty_Amount,
                        'Saving_amount'                 => $row3->Saving_amount,
                        'Total_Amount'                  => $row3->Total_Amount,
                        'Paid_Amount'                   => $row3->Paid_Amount,
                        'Panalty_Balance'               => $row3->Panalty_Balance,
                        'Interest_Balance'              => $row3->Interest_Balance,
                        'capital_balance'               => $row3->capital_balance,
                        'Saving_balance'                => $row3->Saving_balance,
                        'Total_Balance'                 => $row3->Total_Balance,
                        'Status'                        => $row3->Status,
                        'Panelty_status'                => $row3->Panelty_status,
                        'Panelty_count'                 => $row3->Panelty_count,
                        'Paid_Date'                     => $row3->Paid_Date,
                    ]);
                }
            }

            // 2. Refetch everything after insert
            $all = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->lockForUpdate()
                ->get();

            if ($all->isEmpty()) {
                return;
            }

            // STEP A: sort everyone by Installment_Date asc
            $sortedByDate = $all->sortBy(function ($r) {
                return Carbon::parse($r->Installment_Date)->timestamp;
            })->values();

            // STEP B: find unique balloon row (strict max interest)
            $withInterest = $sortedByDate->map(function ($r) {
                $raw = $r->interest_amount ?? 0;
                $num = (float) str_replace(',', '', (string)$raw);
                $r->_interest_numeric = $num;
                return $r;
            });

            $maxInterest = $withInterest->max('_interest_numeric');
            $maxRows = $withInterest->filter(function ($r) use ($maxInterest) {
                return $r->_interest_numeric == $maxInterest;
            });

            $finalOrder = collect();

            if ($maxRows->count() === 1) {
                // unique high-interest row
                $balloonRow   = $maxRows->first();
                $balloonRowId = $balloonRow->idInstallments;

                // take all others IN DATE ORDER first
                foreach ($sortedByDate as $r) {
                    if ($r->idInstallments !== $balloonRowId) {
                        $finalOrder->push($r);
                    }
                }
                // then push balloon row LAST
                $finalOrder->push($balloonRow);
            } else {
                // no unique balloon row -> keep pure date order
                $finalOrder = $sortedByDate;
            }

            // STEP C: renumber No = 1..n based on this final order
            $n = 1;
            foreach ($finalOrder as $r) {
                tableWithBranch('installments')
                    ->where('idInstallments', $r->idInstallments)
                    ->update([
                        'No' => $n
                    ]);
                $n++;
            }
        });
    }



    public function fixLoanInstallmentsOnce_2(int $loanId): void
    {
        DB::transaction(function () use ($loanId) {

            // just reorder again (no insertion here)
            $all = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->lockForUpdate()
                ->get();

            if ($all->isEmpty()) {
                return;
            }

            // STEP A: sort all rows by Installment_Date asc
            $sortedByDate = $all->sortBy(function ($r) {
                return Carbon::parse($r->Installment_Date)->timestamp;
            })->values();

            // STEP B: find unique balloon row (highest interest)
            $withInterest = $sortedByDate->map(function ($r) {
                $raw = $r->interest_amount ?? 0;
                $num = (float) str_replace(',', '', (string)$raw);
                $r->_interest_numeric = $num;
                return $r;
            });

            $maxInterest = $withInterest->max('_interest_numeric');
            $maxRows = $withInterest->filter(function ($r) use ($maxInterest) {
                return $r->_interest_numeric == $maxInterest;
            });

            $finalOrder = collect();

            if ($maxRows->count() === 1) {
                $balloonRow   = $maxRows->first();
                $balloonRowId = $balloonRow->idInstallments;

                foreach ($sortedByDate as $r) {
                    if ($r->idInstallments !== $balloonRowId) {
                        $finalOrder->push($r);
                    }
                }
                $finalOrder->push($balloonRow);
            } else {
                $finalOrder = $sortedByDate;
            }

            // STEP C: renumber No = 1..n based on finalOrder
            $n = 1;
            foreach ($finalOrder as $r) {
                tableWithBranch('installments')
                    ->where('idInstallments', $r->idInstallments)
                    ->update([
                        'No' => $n
                    ]);
                $n++;
            }
        });
    }



    public function logout()
    {
        $user = auth()->user();
        if ($user) {
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('logout');
        }

        Cache::forget('user_data:' . session('user_id'));
        Session::flush();
        Auth::logout();
        Session::forget('token');
        return redirect()->intended(route('login'));
    }


    public function totalOutstandingData()
    {
        $totalOutstandingData = tableWithBranch('installments', 'installments')
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
            ->where(function ($query) {
                $query->where('installments.capital_balance', '>', 0)
                    ->orWhere('installments.Interest_Balance', '>', 0);
            })
            ->groupBy('customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('total_outstanding > 0')
            ->orderBy('total_outstanding', 'DESC')
            ->get();

        return response()->json(['data' => $totalOutstandingData]);
    }

    public function penaltyBalanceData()
    {
        $penaltyBalanceData = tableWithBranch('installments', 'installments')
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

    public function weeklyNotPaidData()
    {
        // Get current week date range
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekToday = date('Y-m-d');

        $weeklyNotPaidData = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            // ✅ Center joins
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_loan.idCustomer_Loan as loan_id',

                // ✅ IMPORTANT: DO NOT SELECT center.No / center.Name directly
                DB::raw('MAX(center.idCenter) as center_id'),
                DB::raw('IFNULL(MAX(CONCAT(center.No, " - ", center.Name)), "-") as center_name'),


                'customer.idCustomer as customer_id',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Amount as capital_amount',
                'customer_loan.Total_Loan_Amount as full_loan_amount',
                // This week not paid amount (installments between week start and today)
                DB::raw('SUM(CASE WHEN installments.Installment_Date BETWEEN "' . $weekStart . '" AND "' . $weekToday . '" THEN installments.Total_Balance ELSE 0 END) as this_week_not_paid'),
                // Total arrears (all overdue installments)
                DB::raw('SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END) as total_arrears'),
                // Not paid installment count
                DB::raw('COUNT(CASE WHEN installments.Total_Balance > 0 AND installments.Status = 0 THEN 1 END) as not_paid_installment_count')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('installments.Total_Balance', '>', 0)
            ->whereBetween('installments.Installment_Date', [$weekStart, $weekToday])
            ->groupBy('center.idCenter', 'customer_loan.idCustomer_Loan', 'customer.idCustomer', 'customer.First_Name', 'customer.Last_Name', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount')
            ->havingRaw('this_week_not_paid > 0')
            ->orderBy('this_week_not_paid', 'DESC')
            ->get();

        return response()->json(['data' => $weeklyNotPaidData]);
    }

    public function create_panelty()
    {
        $date = date('Y-m-d');


        $installment = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->whereDate('Panelty_date', '<=', $date)
            ->select('installments.*', 'customer_loan.Panalty_Rate', 'customer_loan.Panelty_period as Loan_Panelty_period', 'customer_loan.panelty_method', 'customer_loan.Panelty_period', 'customer_loan.idCustomer_Loan', 'customer_loan.Customer_idCustomer')
            ->get();

        $today  = Carbon::today('Asia/Colombo');
        foreach ($installment as $item) {

            $Panelty_period = $item->Loan_Panelty_period;
            $penaltyDate = Carbon::parse($item->Panelty_date);
            $days = max(0, $penaltyDate->diffInDays($today, false));

            if ($Panelty_period == "Weekly") {
                // Convert to full weeks (round down)
                $days = intdiv($days, 7);
                $days++;
            }

            // dd($item);

            $paneltyCount = (int) ($item->Panelty_count ?? 0);
            $missing      = max(0, $days - $paneltyCount);

            if ($missing > 0) {

                $ins_amount = $item->capital_balance + $item->Interest_Balance;
                $panelty_amount = ($ins_amount * $item->Panalty_Rate) / 100;

                $count = $paneltyCount;

                for ($i = 1; $i <= $missing; $i++) {
                    $count++;
                    $amt = number_format((float) $panelty_amount, 2, '.', ''); // sanitize to 2dp

                    DB::table('installments')
                        ->where('idInstallments', $item->idInstallments)
                        ->where('branch_id', session('branch_id'))
                        ->update([
                            'Panalty_Amount'  => DB::raw("ROUND(Panalty_Amount + {$amt}, 2)"),
                            'Panalty_Balance' => DB::raw("ROUND(Panalty_Balance + {$amt}, 2)"),
                            'Total_Amount'    => DB::raw("ROUND(Total_Amount + {$amt}, 2)"),
                            'Total_Balance'   => DB::raw("ROUND(capital_balance + Interest_Balance + Panalty_Balance, 2)"),
                            'Panelty_status'  => 1,
                            'Panelty_count'   => DB::raw('COALESCE(Panelty_count,0) + 1'),
                        ]);


                    $user_id = user_data('idUser');
                    $date = date('Y-m-d');
                    $time = date('H:i:s');

                    $customer_table = tableWithBranch('customer')
                        ->where('idCustomer', '=', $item->Customer_idCustomer)
                        ->first();

                    // dd($item->Customer_idCustomer,$item);
                    $panelty_amount = number_format($panelty_amount, 2, '.', '');

                    DB::table('customer_log')->insert([
                        'customer_id' => $item->Customer_idCustomer,
                        'customer_name' => $customer_table->First_Name . ' ' . $customer_table->Last_Name,
                        'date' => $date,
                        'time' => $time,
                        'description' => "{$panelty_amount} LKR Penalty added for ({$item->idCustomer_Loan})\nInstallment No : {$item->idInstallments}",
                        'description_id' => $item->idInstallments,
                        'comment' => ' ',
                        'type' => 'Penalty',
                        'user' => $user_id,
                        'branch_id' => session('branch_id')
                    ]);


                    $loanLogController = new LoanLogController();

                    $last_log = DB::table('Loan_Log')->where('Loan_ID', '=', $item->idCustomer_Loan)->orderBy('Loan_Log_ID', 'desc')->first();
                    $Panelty_Balance = number_format((float)$last_log->Panelty_Balance + (float)$panelty_amount, 2, '.', '');
                    $Total_Pending_Balance = number_format((float)$last_log->Total_Pending_Balance + (float)$panelty_amount, 2, '.', '');
                    $loanLogController->index(
                        $item->idCustomer_Loan,
                        'Penalty',
                        $item->idInstallments,
                        'Penalty-Installment No : ' . $item->idInstallments . ' Penalty Count : ' . $count,
                        $panelty_amount,
                        '0.00',
                        '0.00',
                        '0.00',
                        '0.00',
                        $Panelty_Balance,
                        $last_log->Interest_Balance,
                        $last_log->Capital_Balance,
                        $Total_Pending_Balance,
                        $last_log->Saving_Account_Balance
                    );

                    Log::info($item->idCustomer_Loan . '-' . $count);

                    $bankLogController = new BankLogController();

                    $System_default_5 = tableWithBranch('company_bank_accounts')
                        ->where('Bank_Type', '=', 'System_default_5')
                        ->first();
                    $System_default_6 = tableWithBranch('company_bank_accounts')
                        ->where('Bank_Type', '=', 'System_default_6')
                        ->first();
                    $bankLogController->index($System_default_5->Idbank, "Penalty", "Penalty", "-", "debit", $panelty_amount, $System_default_6->Idbank);
                    $bankLogController->index($System_default_6->Idbank, "Penalty", "Penalty", "-", "credit", $panelty_amount, $System_default_5->Idbank);
                }
            }
        }











        //
        //
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
        //            $user_id=session('user_data')["idUser"];
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


    public function privileges(Request $request, Store $session)
    {
        $userId = $request->input('userId');
        $privileges = $request->input('privileges', []);

        // Get user details
        $user = DB::table('user')->where('id', $userId)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }

        foreach ($privileges as $key => $value) {
            // Check if permission exists
            $exists = DB::table('user_privileges_has_user')
                ->where('user_id', $userId)
                ->where('permission_key', $key)
                ->exists();

            if ($exists) {
                DB::table('user_privileges_has_user')
                    ->where('user_id', $userId)
                    ->where('permission_key', $key)
                    ->update(['value' => $value]);
            } else {
                DB::table('user_privileges_has_user')->insert([
                    'user_id' => $userId,
                    'permission_key' => $key,
                    'value' => $value
                ]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Privileges updated successfully!']);
    }

    public function showprivileges($id)
    {
        $permissions = DB::table('user_privileges_has_user')
            ->where('user_id', $id)
            ->select('permission_key', 'value')
            ->get();

        return response()->json(['privileges' => $permissions]);
    }


    public function check_mail(Request $request)
    {

        $email = $request->email;
        $user = DB::table('user')->where('email', $email)->first();

        if ($user) {

            $branch_id = $user->branch_id;
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

            $token = $responseData['token'];
            if (!$token) {
                return response()->json(['error' => 'Token not found in session'], 401);
            }


            $newTransactionId = random_int(1, 999999999999999999);


            $otp = random_int(100000, 999999);

            // Update OTP in the database
            DB::table('user')
                ->where('email', $email)
                ->update(['otp' => $otp]);

            $message = "Your OTP is " . $otp;

            $company = DB::table('company')->where('branch_id', '=', $branch_id)->first();

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

            if ($responseData_result['status'] === "success") {

                $user_details = DB::table('user')->where('email', $email)->first();

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
                return view('recover_password', compact('email'));
            } else {
                return redirect()->route('forget_password')->with("error", "Please contact your provider !");
            }
        } else {
            return redirect()->route('forget_password')->with("error", "Please check your email address !");
        }
    }


    public function recover_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,email',
            'otp' => 'required|numeric',
            'password' => 'required|confirmed', // 'confirmed' requires a matching 'password_confirmation' field
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $password = $request->password;

        if ($validator->fails()) {
            return view('recover_password', compact('email'));
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


    public function designation(Request $request)
    {
        $designation = DB::table('designation')->insert([
            'name' => $request->designation,
            'desi_level' => $request->desi_level,
            'loan_creat' => $request->loan_create,
            'loan_issue' => $request->loan_approve,
            'max_create_amount' => str_replace(',', '', $request->max_amount_create),
            'max_issue_amount' => str_replace(',', '', $request->max_amount_approve),
            'branch_id' => session('branch_id')
        ]);
        if ($designation) {
            return response()->json(['data' => $designation], 200);
        }
        return response()->json(['data' => $designation], 404);
    }

    public function updatedesignation(Request $request)
    {
        // Get current designation for comparison
        $designation = DB::table('designation')
            ->where('idDesignation', '=', $request->id)
            ->where('branch_id', session('branch_id'))
            ->first();

        if (!$designation) {
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
            'userid' => user_data('idUser'),
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

        if (!$designationId) {
            return response()->json(['error' => 'Invalid designation id'], 422);
        }

        // ensure designation belongs to current branch
        $designation = DB::table('designation')
            ->where('idDesignation', $designationId)
            ->where('branch_id', session('branch_id'))
            ->first();

        if (!$designation) {
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
            'userid' => user_data('idUser'),
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

        if (!$designation) {
            return response()->json(['privileges' => (object)[]]);
        }

        $privileges = [];
        if ($designation->privileges) {
            $decoded = json_decode($designation->privileges, true);
            if (is_array($decoded)) {
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



    public function holidays()
    {
        $year = date('Y');

        $loans     = tableWithBranch('customer_loan')->where('Status', '0')->get();
        $branches  = DB::table('branch')->where('Status', '1')->get();
        $centers   = tableWithBranch('center')->get();
        $products  = tableWithBranch('loan_category')->get();

        // Get holidays
        $holidays = tableWithBranch('holidays')->get();

        // Format holiday dates
        $holidayDates = $holidays->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->unique();

        // Get all installment dates (only for active loans)
        $installmentDates = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.Status', '=', '0')
            ->pluck('installments.Installment_Date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->unique();

        // Intersect to find how many holiday dates have installments
        $matchingDates = $holidayDates->intersect($installmentDates);
        $holidayWithInstallmentsCount = $matchingDates->count();

        // Ensure table exists
        DB::statement("
    CREATE TABLE IF NOT EXISTS `due_skip_runs` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `skip_for` VARCHAR(255) NOT NULL,
        `target_id` BIGINT UNSIGNED NULL,
        `skip_type` VARCHAR(255) NOT NULL,

        `branch_id` BIGINT UNSIGNED NULL,

        `total_items` INT UNSIGNED NOT NULL DEFAULT 0,
        `processed_items` INT UNSIGNED NOT NULL DEFAULT 0,

        `status` ENUM('queued','running','completed','failed') NOT NULL DEFAULT 'queued',

        `error_message` TEXT NULL,

        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,

        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");


        return view('pages.Holidays', compact(
            'holidays',
            'year',
            'loans',
            'branches',
            'centers',
            'products',
            'holidayWithInstallmentsCount'
        ));
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


    //    public function generateDueSkip(Request $request, HolidayController $holidayController)
    //    {
    //        $request->validate([
    //            'skip_for'  => 'required|in:all,loan,branch,center,product',
    //            'skip_type' => 'required|in:installment,day',
    //            'target_id' => 'nullable|integer',
    //        ]);
    //
    //        $skipFor  = $request->input('skip_for');
    //        $targetId = $request->input('target_id');
    //        $skipType = $request->input('skip_type');
    //
    //        // Call service method from HolidayController
    //        $result = $holidayController->runDueSkip($skipFor, $targetId, $skipType);
    //
    //        return response()->json([
    //            'success' => true,
    //            'message' => 'Due skip processed successfully.',
    //            'data'    => $result,
    //        ]);
    //    }



    public function generateDueSkip(Request $request)
    {
        $skipFor   = $request->skip_for;
        $targetId  = $request->target_id;
        $skipType  = $request->skip_type;

        // ✅ Selected holiday dates from frontend
        $selectedDates = $request->input('selected_dates', []);

        if (empty($selectedDates)) {
            return response()->json([
                'success' => false,
                'message' => 'No holiday dates selected'
            ], 422);
        }

        // Call existing logic
        $holiday = new \App\Http\Controllers\HolidayController();
        $holiday->index($skipFor, $targetId, $skipType, $selectedDates);

        return response()->json([
            'success' => true,
            'message' => 'Due skip processed successfully'
        ]);
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
            'userid' => user_data('idUser'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['success' => true, 'message' => 'User update request sent for approval!']);
    }

    public function resetPassword($id, Request $request)
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
            ->where(function ($query) use ($designationIdentifier) {
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
