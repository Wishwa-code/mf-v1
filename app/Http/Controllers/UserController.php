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

            $collector=$request->has('collecting_officer') ? 1 : 0;


            $data['Full_Name']=$request->full_name;
            $data['email']=$request->email;
            $data['password']=Hash::make($request->password);
            $data['TP']=$tp;
            $data['Designation']=$request->desi;
            $data['Epf_no']=$request->epf_no;
            $data['Nic']=$request->nic;
            $data['lending_officer']=$request->has('lending_officer') ? 1 : 0;
            $data['otp']=$otp;
            $data['branch_id']=$request->branch;
            $data['branch_access']=$request->has('branch_access') ? 1 : 0;
            $user=User::create($data);
            if (!$user){
                return redirect()->intended(route('pages.user'))->with("error","Registration Failed !");
            }

            if ($collector==1){
                $Bank = [
                    'Bank_Type' => "Collector",
                    'code' => $user->id.'/Collector',
                    'Bank_Name' => "Collector",
                    'Account_Name' => $request->full_name,
                    'Account_No' => $user->id,
                    'Bank_Branch' => '-',
                    'Account_Balance' => "0.00",
                    'type' => "Cash and Bank",
                    'cashflow' => "Non Applicable",
                    'User' => $user->id,
                    'branch_id' => $request->has('branch_access'),
                ];

                if (DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $request->account_number)->exists()) {

                }else {
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
                        'branch_id' => $request->has('branch_access'),
                    ];

// Insert the BankLog entry using the helper function
                    insertWithBranch('company_bank_has_log', $bankLogData);
                }
            }
            return redirect()->intended(route('pages.user'))->with("success", "Registration success !");
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

                // Try-catch block to handle cURL errors
                try {
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

                    // Check if token exists in the response data
                    if (isset($responseData['token'])) {
                        // Store token in session
                        $session->put('token', $responseData['token']);

                        // Optionally, store other relevant data in session
                        $session->put('userData', $responseData['userData']);
                    }
                } catch (\Exception $e) {
                    // Log the error and proceed with login
                    \Log::error('SMS API Login Error: ' . $e->getMessage());
                }

                // Store user information in session

                $session->put('userid', $item->id);
                $session->put('Full_Name', $item->Full_Name);
                $session->put('designation', $item->Designation);
                $session->put('branch_id', $item->branch_id);
                $session->put('branch_access', $item->branch_access);
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

            // Call to the penalty creation function
            $this->create_panelty();
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
            DB::table('user')->where('id', $id)->update(['Status' => $newStatus]); // Access Status as an object property
            return response()->json(['message' => 'Data updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Customer not found'], 404);
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


//        $customers = DB::table('customer as c')
//            ->join('group_has_customer as ghc', 'c.idCustomer', '=', 'ghc.cus_id')
//            ->join('customer_group as cg', 'ghc.group_id', '=', 'cg.idCustomer_Group')
//            ->join('center as cn', 'cg.center_id', '=', 'cn.idCenter')
//            ->where('c.branch_id', '=', '1')
//            ->select('c.idCustomer', 'cn.No as center_no')
//            ->orderBy('c.idCustomer') // ensure consistent ordering
//            ->get();
//
//
//        $branch_code = 'AM';
//        $customer_max = 0;
//
//        foreach ($customers as $customer) {
//            $customer_max++;
//
//            $auto_id = str_pad($customer_max, 3, '0', STR_PAD_LEFT);
//            $new_cus_number = "{$branch_code}/{$customer->center_no}/{$auto_id}";
//
//            DB::table('customer')
//                ->where('idCustomer', $customer->idCustomer)
//                ->update(['cus_number' => $new_cus_number]);
//        }





        $customerCount = tableWithBranch('customer')->count();
        $customer_loan_pending_Count = tableWithBranch('customer_loan')->where('Status','=','-1')->count();
        $customer_loan_pending_Amount = tableWithBranch('customer_loan')->where('Status','=','-1')->sum('Amount');
        $customer_loan_current_Count = tableWithBranch('customer_loan')->where('Status','=','0')->count();
        $customer_loan_current_Amount = tableWithBranch('customer_loan')->where('Status','=','0')->sum('Amount');
        $setteled_loan_Count = tableWithBranch('customer_loan')->where('Status','=','1')->count();
        $deleted_loan_Count = tableWithBranch('customer_loan')->where('Status','=','-2')->count();
        $setteled_loan_current_Amount = tableWithBranch('customer_loan')->where('Status','=','1')->sum('Amount');
        $todayinstallment = tableWithBranch('installments')->where('Installment_Date',date('Y-m-d'))->where('Status','=','0')->sum('Total_Balance');
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
        $userid=session('userid');
        $getuser = DB::table('user')->where('id', $userid)->first();
        $dashboard=0;
        if ($getuser){
            $dashboard=$getuser->dashboard;
        }





        $capitalBalanceController = new CapitalBalanceController();
        $loan=DB::table('customer_loan')->get();
        foreach ($loan as $loans){
            $capitalBalanceController->index($loans->idCustomer_Loan);
            $loan_log=DB::table('Loan_Log')->where('Loan_ID','=',$loans->idCustomer_Loan)->where('Type','=','Issue Loan')->first();
            if (!$loan_log){
                $id=$loans->idCustomer_Loan;
                $branch_id=$loans->branch_id;
                $panelty_balance=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$id)->sum('Panalty_Balance');

                $user_id = (int)session('userid');
                // Insert the data into the Savings_Account_Log table
                DB::table('Loan_Log')->insert([
                    'Loan_ID' => $id,
                    'Date_Time' => date('Y-m-d H:i:s'),
                    'Type' => 'Issue Loan',
                    'Type_ID' => $id,
                    'Description' => 'Loan Issue',
                    'Amount' => $loans->Amount,
                    'Panelty_Payment' => '0',
                    'Interest_Payment' => '0',
                    'Capital_Payment' => '0',
                    'Savings_Payment' => '0',
                    'Panelty_Balance' => $panelty_balance,
                    'Interest_Balance' => $loans->Interest_Amount,
                    'Capital_Balance' => $loans->capital_balance,
                    'Total_Pending_Balance' => $loans->Balance_Amount+$panelty_balance,
                    'Saving_Account_Balance' => '0',
                    'User_idUser' => $user_id,
                    'branch_id' =>$branch_id
                ]);





            }
        }

//        $loan=DB::table('customer_loan')->where('Status','=','1')->get();
//        foreach ($loan as $loans){
//            $loanId=$loans->idCustomer_Loan;
//            // Update the status of the loan in the customer_loan table to 0 (settled)
//            DB::table('customer_loan')->where('idCustomer_Loan', $loanId)->update([
//                'Balance_Amount' => '0.00',
//                'capital_balance' => '0.00',
//                'installment_balance' => '0.00',
//            ]);
//
//            // Update the status in the installments table to 1 (completed)
//            DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $loanId)->update([
//                'status' => 1,
//                'Panalty_Balance' => '0.00',
//                'Interest_Balance' => '0.00',
//                'capital_balance' => '0.00',
//                'Saving_balance' => '0.00',
//                'Total_Balance' => '0.00',
//                'Paid_Amount' => DB::raw('Total_Amount'),
//            ]);
//        }
        $currentYear = date('Y');

        $monthlyRevenue = DB::table('customer_payments')
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
            $results = DB::table('customer_payments')
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



        \Log::info('Weekly Current', $weeklyComparison['current']);
        \Log::info('Weekly Last', $weeklyComparison['last']);


        return view('home',compact('weeklyComparison','deleted_loan_Count','all_loan','monthlyData','dashboard','checqueamount','totalBalanceUntil','arrease','todayInstallment','setteled_loan_current_Amount','customer_loan_pending_Amount','customer_loan_current_Amount','setteled_loan_Count','shortcut_count','shortcut','customerCount','customer_loan_pending_Count','customer_loan_current_Count','todayinstallment','todaycollection'));
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        Session::forget('token');
        return redirect()->intended(route('login'));
    }



    public function create_panelty()
    {
        $date=date('Y-m-d');

        $poya=tableWithBranch('holidays')->get();
        $poyaDates = $poya->pluck('date')->toArray(); // Extract only the dates


        $installment=tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('customer_loan.Status', '=', '0')
            ->where('installments.Status', '=', '0')
            ->where('Panelty_status', '=', '0')
            ->whereDate('Panelty_date', '<=', $date)
            ->whereNotIn('Panelty_date', $poyaDates) // Exclude dates in $poya
            ->select('installments.*', 'customer_loan.Panalty_Rate','customer_loan.idCustomer_Loan','customer_loan.Customer_idCustomer')
            ->get();


        foreach ($installment as $item){
            $total_balance=$item->Total_Balance;
            $panelty_amount=($total_balance*$item->Panalty_Rate)/100;
            $newPanaltyBalance = str_replace(',', '', number_format($item->Panalty_Balance + $panelty_amount, 2));
            $tot_balance = str_replace(',', '', number_format($total_balance + $panelty_amount, 2));


            DB::table('installments')
                ->where('idInstallments', $item->idInstallments)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'Panalty_Amount' => number_format($panelty_amount, 2, '.', ''),
                    'Panalty_Balance' => number_format($newPanaltyBalance, 2, '.', ''),
                    'Total_Amount' => DB::raw('ROUND(Total_Amount + ' . $panelty_amount . ', 2)'),
                    'Total_Balance' => number_format($tot_balance, 2, '.', ''),
                    'Panelty_status' => '1'
                ]);

            $user_id= session('userid');
            $date=date('Y-m-d');
            $time=date('H:i:s');

            $customer_table=tableWithBranch('customer')
                ->where('idCustomer','=',$item->Customer_idCustomer)
                ->first();

            $panelty_amount=number_format($panelty_amount, 2,'.','');

            DB::table('customer_log')->insert([
                'customer_id' => $item->Customer_idCustomer,
                'customer_name' => $customer_table->First_Name.' '.$customer_table->Last_Name,
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

            $last_log = DB::table('Loan_Log')->where('Loan_ID','=',$item->idCustomer_Loan)->orderBy('Loan_Log_ID', 'desc')->first();
            $Panelty_Balance = number_format((float)$last_log->Panelty_Balance + (float)$panelty_amount, 2, '.', '');
            $Total_Pending_Balance = number_format((float)$last_log->Total_Pending_Balance + (float)$panelty_amount, 2, '.', '');
            $loanLogController->index(
                $item->Customer_idCustomer, 'Penalty', $item->idInstallments,
                'Penalty-Installment No : '.$item->idInstallments, $panelty_amount,
                $last_log->Panelty_Payment, $last_log->Interest_Payment,
                $last_log->Capital_Payment, $last_log->Savings_Payment, $Panelty_Balance,
                $last_log->Interest_Balance, $last_log->Capital_Balance, $Total_Pending_Balance, $last_log->Saving_Account_Balance
            );

            $bankLogController = new BankLogController();

            $System_default_5=tableWithBranch('company_bank_accounts')
                ->where('Bank_Type','=','System_default_5')
                ->first();
            $System_default_6=tableWithBranch('company_bank_accounts')
                ->where('Bank_Type','=','System_default_6')
                ->first();
            $bankLogController->index($System_default_5->Idbank,"Penalty","Penalty","-","debit",$panelty_amount,$System_default_6->Idbank);
            $bankLogController->index($System_default_6->Idbank,"Penalty","Penalty","-","credit",$panelty_amount,$System_default_5->Idbank);


        }


    }


    public function showprivileges(string $id){
        $userData = tableWithBranch('user')->where('id',$id)->first();

        return response()->json(['items' => $userData], 200);
    }


    public function privileges(Request $request)
    {
        $user=$request->userId;
        $checkboxValues = $request->get('checkboxValues', []);

        DB::table('user')->where('id', $user)->update(array(
            'customer' => $checkboxValues['customer'],
            'add_customer' => $checkboxValues['add_customer'],
            'view_customer' => $checkboxValues['view_customer'],
            'loan_center' => $checkboxValues['loan_center'],
            'create_loan_center' => $checkboxValues['create_loan_center'],
            'view_center' => $checkboxValues['view_center'],
            'create_group' => $checkboxValues['create_group'],
            'view_group' => $checkboxValues['view_group'],
            'assign_customer_to_group' => $checkboxValues['assign_customer_to_group'],
            'guarantee' => $checkboxValues['guarantee'],
            'add_guarantee' => $checkboxValues['add_guarantee'],
            'view_guarantee' => $checkboxValues['view_guarantee'],
            'product' => $checkboxValues['product'],
            'add_product' => $checkboxValues['add_product'],
            'view_product' => $checkboxValues['view_product'],
            'issue_loan' => $checkboxValues['issue_loan'],
            'pending_loan' => $checkboxValues['pending_loan'],
            'current_loan' => $checkboxValues['current_loan'],
            'loan_in_arrease' => $checkboxValues['loan_in_arrease'],
            'payment' => $checkboxValues['payment'],
            'add_re_payment' => $checkboxValues['add_re_payment'],
            'view_repayment' => $checkboxValues['view_repayment'],
            'pending_approval_repayment' => $checkboxValues['pending_approval_repayment'],
            'approval_repayment' => $checkboxValues['approval_repayment'],
            'agent_collection' => $checkboxValues['agent_collection'],
            'loan_calculator' => $checkboxValues['loan_calculator'],
            'calender' => $checkboxValues['calender'],
            'expenses' => $checkboxValues['expenses'],
            'add_expenses' => $checkboxValues['add_expenses'],
            'view_expenses' => $checkboxValues['view_expenses'],
            'income' => $checkboxValues['income'],
            'add_income' => $checkboxValues['add_income'],
            'view_income' => $checkboxValues['view_income'],
            'user' => $checkboxValues['user'],
            'create_user' => $checkboxValues['create_user'],
            'user_privilage' => $checkboxValues['user_privilage'],
            'report' => $checkboxValues['report'],
            'report_1' => $checkboxValues['report_1'],
            'report_2' => $checkboxValues['report_2'],
            'report_3' => $checkboxValues['report_3'],
            'report_4' => $checkboxValues['report_4'],
            'report_5' => $checkboxValues['report_5'],
            'report_6' => $checkboxValues['report_6'],
            'report_7' => $checkboxValues['report_7'],
            'report_8' => $checkboxValues['report_8'],
            'report_9' => $checkboxValues['report_9'],
            'report_10' => $checkboxValues['report_10'],
            'report_11' => $checkboxValues['report_11'],
            'report_12' => $checkboxValues['report_12'],
            'report_13' => $checkboxValues['report_13'],
            'report_14' => $checkboxValues['report_14'],
            'report_15' => $checkboxValues['report_15'],
            'report_16' => $checkboxValues['report_16'],
            'report_17' => $checkboxValues['report_17'],
            'add_bulk_re_payment' => $checkboxValues['add_bulk_re_payment'],
            'account' => $checkboxValues['account'],
            'bank_details' => $checkboxValues['bank_details'],
            'chq_details' => $checkboxValues['chq_details'],
            'dashboard' => $checkboxValues['dashboard'],
        ));


        return response()->json(['data' => $user]);
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
            'max_create_amount' => $request->max_amount_create,
            'max_issue_amount' => $request->max_amount_approve,
            'branch_id' => session('branch_id')
        ]);
        if ($designation){
            return response()->json(['data' => $designation], 200);
        }
        return response()->json(['data' => $designation], 404);
    }

    public function updatedesignation(Request $request){
        $designation = DB::table('designation')
            ->where('idDesignation', '=', $request->id) // specify the column name here
            ->where('branch_id', session('branch_id'))
            ->update([
                'name' => $request->designation,
                'desi_level' => $request->desiLevel,
                'loan_creat' => $request->loanCreate,
                'loan_issue' => $request->loanApprove,
                'max_create_amount' => $request->maxCreateAmount,
                'max_issue_amount' => $request->maxIssueAmount,
            ]);

        if ($designation){
            return response()->json(['data' => $designation], 200);
        }
        return response()->json(['data' => $designation], 404);
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

        // Use DB::table to update the user record in the 'users' table
        $updated = DB::table('user')
            ->where('email', $request->email) // Find the user by id
            ->update([
                'Epf_no' => $request->epf_no,
                'Designation' => $request->desi,
                'Nic' => $request->nic,
                'Full_Name' => $request->full_name,
                'TP' => $request->tp,
                'lending_officer' => $request->lending_officer ? 1 : 0,
                'collector' => $request->collecting_officer ? 1 : 0,
                'branch_id' => $request->branch,
                'branch_access' => $request->branch_access ? 1 : 0,
            ]);

//        $user = DB::table('user')->where('email', $request->email)->first();
//        if ($user){
//            $user_id=$user->id;
//            Log::info($user_id);
//            if ($user->collector=="1"){
//                DB::table('company_bank_accounts')
//                    ->where('Account_No',(string) $user_id) // Find the user by id
//                    ->update([
//                        'branch_id' => $request->branch,
//                    ]);
//                $bank = DB::table('company_bank_accounts')->where('Account_No', $user_id)->first();
//                if ($bank){
//                    $bank_id=$bank->Idbank;
//                    DB::table('company_bank_has_log')
//                        ->where('Bank_Account_Id', $bank_id) // Find the user by id
//                        ->update([
//                            'branch_id' => $request->branch,
//                        ]);
//                }
//            }
//        }

        // Check if the update was successful and return response
        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'No changes made or user not found']);
        }
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


}
