<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\TodayPaymentController;


class PendingLoanController extends Controller
{
    protected $customerLogController;
    protected $smsLogController;
    protected $bankLogController;
    protected $LoanLogController;

    // Single constructor to inject both controllers
    public function __construct(CustomerLogController $customerLogController, SmsController $smsLogController,BankLogController $bankLogController,LoanLogController $LoanLogController)
    {
        $this->customerLogController = $customerLogController;
        $this->smsLogController = $smsLogController;
        $this->bankLogController = $bankLogController;
        $this->LoanLogController = $LoanLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = (int)session('userid');
        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector = $collector_val->collector;

        $group = tableWithBranch('customer_group')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        $bank = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->where('status','=','1')->get();
        if ($collector == 1) {
            $bank = DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $user_id)->where('status', '=', '1')->get();
        }
        $documents = tableWithBranch('documents')->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $center = tableWithBranch('center')->get();
        return view('pages.PendingLoan', compact('route','center','group', 'loan_category', 'customers','bank','documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $group = $request->group;
        $category = $request->category;
        $status = $request->status;
        $customer = $request->customer;
        $center_details = $request->center_details;
        $route = $request->route;

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
        FROM group_has_customer 
        LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin(DB::raw('(SELECT loan_id, COUNT(*) as approval_count, 
                SUM(CASE WHEN date = "-" THEN 1 ELSE 0 END) as pending_approvals 
            FROM loan_has_approval 
            GROUP BY loan_id) as approval_subquery'),
                'customer_loan.idCustomer_Loan', '=', 'approval_subquery.loan_id')
            ->where('customer_loan.Status', '=', $status)
            ->whereNotNull('approval_subquery.loan_id')  // Ensure at least one approval exists
            ->where('approval_subquery.pending_approvals', '>', 0)  // Ensure there are still pending approvals
            ->select(
                'customer_loan.*',
                'loan_category.Name as loan_name',
                'customer.*',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(route.name, "-") as route_name'),
                'u1.Full_Name as user_name',
                'u2.Full_Name as lending_officer',
                DB::raw('IFNULL(approval_subquery.approval_count, 0) as approval_count'),
                DB::raw('IFNULL(approval_subquery.pending_approvals, 0) as pending_approvals')
            );


        // Apply filters based on input values
        if ($group != '0') {
            $loanQuery->where('subquery.group_id', '=', $group);
        }

        if ($category != '0') {
            $loanQuery->where('loan_category.idLoan_Category', '=', $category);
        }

        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }

        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($route != '0') {
            $loanQuery->where('customer.route_id', '=', $route);
        }

        $loan = $loanQuery->get();

        return response()->json(['item' => $loan, 'message' => 'filtered'], 200);
    }






    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        DB::beginTransaction();
        try {
            $id=$request->loan_id;
            $id_show=$request->loan_id;
            $company_bank=$request->company_bank;
            $document_details=$request->document_details;

            $customer_loan=tableWithBranch('customer_loan')
                ->where('idCustomer_Loan','=',$id)
                ->first();

            $app_settings = DB::table('app_settings')->where('key','=','loan_disbursement_policy')->first();
            $loan_disbursement_policy=$app_settings->value ?? 'flexible';

            if ($loan_disbursement_policy=='strict'){
                $bank = tableWithBranch('company_bank_accounts')->where('Idbank','=',$company_bank)->first();
                if ($bank->Account_Balance<$customer_loan->Amount){
                    return response()->json(['error' => 'Bank Balance is not enough','id' => 0], 200);
                }
            }

            $affected = DB::table('customer_loan')
                ->where('idCustomer_Loan', $id)
                ->where('branch_id', session('branch_id'))
                ->update(
                    [
                        'Status' => '0',
                        'Date_Time' => date('Y-m-d H:i:s'),
                        'cus_bank_account' => $request->bank_acc,
                        'company_bank_account' => $company_bank
                    ]);


            $bank_log_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";


            $bank_id=tableWithBranch('company_bank_accounts')
                ->where('Bank_Type','=','System_default_1')
                ->first();


            $this->bankLogController->index($company_bank,"Issue Loan",$bank_log_comment,"-","credit",$customer_loan->Amount,$bank_id->Idbank);




            $this->bankLogController->index($bank_id->Idbank,"Issue Loan",$bank_log_comment,"-","debit",$customer_loan->Amount,$company_bank);




            $customer=tableWithBranch('customer')
                ->where('idCustomer','=',$customer_loan->Customer_idCustomer)
                ->first();
            $sumAmount = DB::table('loan_other_charges')
                ->where('Customer_Loan_idCustomer_Loan', '=', $id)
                ->where('branch_id', session('branch_id'))
                ->sum('Amount');



            // Check if the sumAmount is greater than zero
            if ($sumAmount > 0) {
                $bank_log_doc_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";

                $bank_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_9')
                    ->first();


                $this->bankLogController->index($company_bank,"Loan Document Chargers",$bank_log_doc_comment,"-","debit",$sumAmount,$bank_id->Idbank);

                $this->bankLogController->index($bank_id->Idbank,"Loan Document Chargers",$bank_log_doc_comment,"-","credit",$sumAmount,$company_bank);

                $cate=tableWithBranch('income_category')
                    ->where('description','=','Other')
                    ->first();
                $user_id = (int)session('userid');
                if ($cate){

                    // Create a new Expenses instance
                    $expenses = new Expenses();

                    // Set the values for the Expenses instance
                    $expenses->type = "Income";
                    $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                    $expenses->date = date('Y-m-d');
                    $expenses->amount = $sumAmount;
                    $expenses->category_id = $cate->id;
                    $expenses->bank_id = 1;
                    $expenses->user_id = $user_id;
                    $expenses->branch_id = session('branch_id');

                    $expenses->save();
                }else{
                    $cate_id=DB::table('income_category')->insertGetId([
                        'description'=>"Other",
                        'branch_id'=>session('branch_id')
                    ]);

                    // Create a new Expenses instance
                    $expenses = new Expenses();

                    // Set the values for the Expenses instance
                    $expenses->type = "Income";
                    $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                    $expenses->date = date('Y-m-d');
                    $expenses->amount = $sumAmount;
                    $expenses->category_id = $cate_id;
                    $expenses->bank_id = 1;
                    $expenses->user_id = $user_id;
                    $expenses->branch_id = session('branch_id');

                    $expenses->save();
                }




            }


            $request = new Request([
                'customer_id' => $customer_loan->Customer_idCustomer,
                'description' => "Approve Loan ({$customer_loan->Loan_No})\nLoan Amount : ({$customer_loan->Amount})",
                'description_id' => $id,
                'comment' => ' ',
                'type' => 'Approve Loan',
            ]);

            // Call the store method of CustomerLogController
            $this->customerLogController->store($request);


            if (!empty($document_details)) {
                // Process the tableData as needed
                foreach ($document_details as $row) {
                    $id = $row['id'];
                    $checked = $row['checked'];

                    // Convert checked value to 1 or 0
                    $isChecked = $checked ? 1 : 0;

                    // Update database based on idDocuments
                    DB::table('documents')
                        ->where('idDocuments', $id)
                        ->where('branch_id', session('branch_id'))
                        ->update(['issue_loan_check' => $isChecked]);
                }
            }

            $panelty_balance=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$id)->sum('Panalty_Balance');

            // Call the store method of LoanLogController
            $this->LoanLogController->index(
                $id_show,
                'Issue Loan',
                $id_show,
                'Loan Issue',
                $customer_loan->Amount,
                '0',
                '0',
                '0',
                '0',
                $panelty_balance,
                $customer_loan->Interest_Amount,
                $customer_loan->capital_balance,
                $customer_loan->Balance_Amount+$panelty_balance,
                '0');

// Instantiate UserController
            $userController = new UserController();

            // Call the create_panelty function
            // $userController->create_panelty();

            // Check if any rows were affected
            if ($affected) {

                $sms_template = tableWithBranch('sms_template')->where('type', '=', 'loan_issue')->where('status', '=', '1')->first();
                if ($sms_template) {
                    $customer = tableWithBranch('customer')->where('idCustomer', '=', $customer_loan->Customer_idCustomer)->first();
                    $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $customer_loan->Loan_Category_idLoan_Category)->first();

                    // Step 2: Define the mapping
                    $placeholders = [
                        '@Member_No@' => $customer->cus_number,
                        '@Member_Name@' => $customer->First_Name . ' ' . $customer->Last_Name,
                        '@Loan_No@' => $customer_loan->Loan_No,
                        '@Loan_Amount@' => $customer_loan->Amount,
                        '@Interest_Amount@' => $customer_loan->Interest_Amount,
                        '@Repayment_Type@' => $product->Repayment_type,
                        '@Installment_Amount@' => $customer_loan->Installment_Amount,
                        '@Issue_Date@' => $customer_loan->Date_Time,
                    ];

                    // Step 3: Replace placeholders in the loan_format
                    $loan_number_txt = $sms_template->template;
                    foreach ($placeholders as $placeholder => $value) {
                        $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                    }

                    // Log the SMS message
                    $this->smsLogController->index($customer_loan->Customer_idCustomer, $loan_number_txt, "Issue Loan");
                }
                DB::commit();
                return response()->json(['message' => 'User updated successfully','id'=>1,$document_details], 200);


            } else {
                return response()->json(['error' => 'User not found'], 404);
            }
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id,string $loan)
    {
        $customers = tableWithBranch('customer')
            ->where('idCustomer', '=', $id)->first();

        $getloan=tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $loan)->first();

        $category=tableWithBranch('loan_category')->where('idLoan_Category', '=', $getloan->Loan_Category_idLoan_Category)->get();
        $installments=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', '=', $loan)->get();
        $witnesses=tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', '=', $loan)->get();
        return view('pages.Show_Loan', compact('category','customers','id','getloan','installments','witnesses'));
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
    public function destroy(string $id,Request $request)
    {
        $affected = DB::table('customer_loan')
            ->where('idCustomer_Loan', $id)
            ->where('branch_id', session('branch_id'))
            ->update(['Status' => '-2','reason' => $request->reason_for_dlt]);



        $customer_loan=tableWithBranch('customer_loan')
            ->where('idCustomer_Loan','=',$id)
            ->first();


        $request = new Request([
            'customer_id' => $customer_loan->Customer_idCustomer,
            'description' => "Delete Loan ({$id})\nReason : {$request->reason_for_dlt}",
            'description_id' => $id,
            'comment' => ' ',
            'type' => 'Delete Loan',
        ]);

        // Call the store method of CustomerLogController
        $this->customerLogController->store($request);

        // Check if any rows were affected
        if ($affected) {
            return response()->json(['message' => 'User updated successfully'],200);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }


    public function check_the_document(string $id){

        $customer_loan_doc=tableWithBranch('documents')
            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
            ->get();
        return response()->json(['item' => $customer_loan_doc],200);
    }

    public function check_the_approval(string $id)
    {

        $login_designation = session('designation');

        $customer_loan = DB::table('customer_loan')
            ->where('idCustomer_Loan', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->first();

        // Fetch levels with their respective designations
        $level = tableWithBranch('level', 'level')
            ->join('level_has_designation', 'level.id', '=', 'level_has_designation.level_id')
            ->where('product_id', '=', $customer_loan->Loan_Category_idLoan_Category)
            ->select(
                'level.id as level_id', // Keep level ID
                'level_has_designation.designation_id' // Keep designation as it was
            )
            ->get();

        $loanCategory = DB::table('loan_category')
            ->where('idLoan_Category', '=', $customer_loan->Loan_Category_idLoan_Category)
            ->first();

        $categoryUpdatedAt = Carbon::parse($loanCategory->updated_at);
        $now = Carbon::now();

        // Set your threshold (e.g., 1 minute ago)
        $thresholdInSeconds = 60;

        $shouldUpdate = $categoryUpdatedAt->diffInSeconds($now) <= $thresholdInSeconds;


        if ($customer_loan->Status=="-1" && $shouldUpdate) {
            DB::table('loan_has_approval')->where('loan_id', $id)->delete();
            DB::table('loan_has_approval_checklist')->where('loan_id', $id)->delete();
            $get_level=tableWithBranch('level')->where('product_id','=',$customer_loan->Loan_Category_idLoan_Category)->get();
            foreach ($get_level as $item){
                // Prepare data for the loan approval
                $loanApprovalData = [
                    'loan_id' => $id,
                    'level' => $item->type,
                    'level_id' => $item->id,
                    'description' => $item->description,
                    'comment' => '',
                    'user_id' => 0,
                    'date' => '-',
                ];

// Insert the loan approval data with branch scoping
                insertWithBranch('loan_has_approval', $loanApprovalData);

                $checklist=tableWithBranch('approval_checklist')->where('level_id','=',$item->id)->get();
                foreach ($checklist as $check_item){
                    $loanChecklistData = [
                        'loan_id' => $id,
                        'level' => $item->id,
                        'description' => $check_item->description,
                        'status' => '0',
                    ];
                    insertWithBranch('loan_has_approval_checklist', $loanChecklistData);
                }
            }
        }


        $customer_loan_doc = tableWithBranch('loan_has_approval', 'loan_has_approval')
            ->leftJoin('user', 'loan_has_approval.user_id', '=', 'user.id')
            ->where('loan_id', '=', $id)
            ->select(
                'loan_has_approval.*',
                DB::raw('IF(user.id IS NULL, 0, user.id) as user_id'),
                DB::raw('IF(user.id IS NULL, "-", user.Full_Name) as Full_Name')
            )
            ->get();

        return response()->json([
            'item' => $customer_loan_doc, // No changes here
            'designation' => $level, // Now properly linked to levels
            'login_designation' => $login_designation // Unchanged
        ], 200);
    }




    public function approve_loan(Request $request){
        $user_id = (int)session('userid');

        $affected = DB::table('loan_has_approval')
            ->where('id', $request->id)
            ->where('branch_id', session('branch_id'))
            ->update([
                'comment' => $request->comment,
                'user_id' => $user_id,
                'date'    => date('Y-m-d H:i:s'),
            ]);

        $customer_loan_doc = tableWithBranch('loan_has_approval', 'loan_has_approval')
            ->leftJoin('user', 'loan_has_approval.user_id', '=', 'user.id')
            ->where('loan_id', '=', $request->loan_id)
            ->select(
                'loan_has_approval.*',
                DB::raw('IF(user.id IS NULL, 0, user.id) as user_id'),
                DB::raw('IF(user.id IS NULL, "-", user.Full_Name) as Full_Name')
            )
            ->get();

        // Check if all are approved
        $allApproved = $customer_loan_doc->every(function ($item) {
            return $item->date !== '-' && $item->user_id != 0;
        });

        if ($allApproved) {
            $loan_id = $request->loan_id;
            
            $customer_loan = tableWithBranch('customer_loan')
                ->where('idCustomer_Loan', '=', $loan_id)
                ->first();
            
            if ($customer_loan) {
                $customer = tableWithBranch('customer')
                    ->where('idCustomer', '=', $customer_loan->Customer_idCustomer)
                    ->first();
                
                $loan_category = tableWithBranch('loan_category')
                    ->where('idLoan_Category', '=', $customer_loan->Loan_Category_idLoan_Category)
                    ->first();
                
                $requestData = [
                    'loan_id' => $loan_id,
                    'customer_id' => $customer_loan->Customer_idCustomer,
                    'loan_no' => $customer_loan->Loan_No,
                    'amount' => $customer_loan->Amount,
                ];
                
                $description = 'Loan Approval: ' . $customer->First_Name . ' ' . $customer->Last_Name . 
                               ' | Loan No: ' . $customer_loan->Loan_No . 
                               ' | Amount: ' . $customer_loan->Amount .
                               ' | Category: ' . $loan_category->Name;
                
                DB::table('approval_request')->insert([
                    'type' => 'Loan Approval',
                    'typeid' => 401,
                    'description' => $description,
                    'data' => json_encode($requestData),
                    'userid' => session('userid'),
                    'branch_id' => session('branch_id'),
                    'data_time' => now(),
                    'status' => 0
                ]);
                
                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', session('branch_id'))
                    ->update(['Status' => '-3']);
            }
            
            return response()->json([
                'item'     => $affected,
                'redirect' => false,
                'message'  => 'All approvals completed. Loan sent to head office for final approval!'
            ], 200);
        }

        return response()->json(['item' => $affected, 'redirect' => false],200);
    }




    public function index_disbursement()
    {
        $user_id = (int)session('userid');
        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector = $collector_val->collector;
        $cashier = $collector_val->cashier;

        $group = tableWithBranch('customer_group')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        $bank = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->where('status','=','1')->get();
        if ($collector == 1 || $cashier==1) {
            $bank = DB::table('company_bank_accounts')->where('branch_id', session('branch_id'))->where('Account_No', '=', $user_id)->where('status', '=', '1')->get();
        }
        $documents = tableWithBranch('documents')->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $center = tableWithBranch('center')->get();

        return view('pages.DisbursementLoan', compact('route','center','group', 'loan_category', 'customers','bank','documents'));
    }

    public function create_disbursement(Request $request)
    {
        $group          = $request->group;
        $category       = $request->category;
        $status         = $request->status;
        $customer       = $request->customer;
        $center_details = $request->center_details;
        $route          = $request->route;

        // --- ONE bank row per customer (latest by id). If you prefer created_at, swap MAX(id) -> MAX(created_at) join back on created_at/id. ---
        $bankSub = DB::raw("
        (
            SELECT chb.*
            FROM customer_has_bank chb
            JOIN (
                SELECT cus_id, MAX(id) AS pick_id
                FROM customer_has_bank
                GROUP BY cus_id
            ) p ON p.cus_id = chb.cus_id AND p.pick_id = chb.id
        ) AS bank_sub
    ");

        // --- ONE group/center row per customer (most recent membership) ---
        $groupSub = DB::raw("
        (
            SELECT ghc.cus_id,
                   ghc.group_id,
                   cg.Name   AS group_name,
                   cg.center_id
            FROM group_has_customer ghc
            LEFT JOIN customer_group cg
                   ON ghc.group_id = cg.idCustomer_Group
            JOIN (
                SELECT cus_id, MAX(id) AS pick_row
                FROM group_has_customer
                GROUP BY cus_id
            ) pick ON pick.cus_id = ghc.cus_id AND pick.pick_row = ghc.id
        ) AS subquery
    ");

        // --- ONE collector per route (deterministic pick: lowest id) ---
        $collectorPick = DB::raw("
        (
            SELECT r.id_route,
                   MIN(chr.collector_id) AS collector_id
            FROM route r
            LEFT JOIN collector_has_route chr ON chr.route_id = r.id_route
            GROUP BY r.id_route
        ) AS cr
    ");

        // --- Approvals pre-aggregated ---
        $approvalSub = DB::raw("
        (
            SELECT loan_id,
                   COUNT(*) AS approval_count,
                   SUM(CASE WHEN date = '-' THEN 1 ELSE 0 END) AS pending_approvals
            FROM loan_has_approval
            GROUP BY loan_id
        ) AS approval_subquery
    ");

        // --- Other charges pre-aggregated ---
        $chargesSub = DB::raw("
        (
            SELECT Customer_Loan_idCustomer_Loan,
                   SUM(Amount) AS total_other_charges
            FROM loan_other_charges
            GROUP BY Customer_Loan_idCustomer_Loan
        ) AS charges_subquery
    ");

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin($bankSub, 'customer_loan.Customer_idCustomer', '=', 'bank_sub.cus_id')
            ->leftJoin($groupSub, 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin($collectorPick, 'route.id_route', '=', 'cr.id_route')
            ->leftJoin('user as collector', 'customer_loan.collector_id', '=', 'collector.id')
            ->leftJoin($approvalSub, 'customer_loan.idCustomer_Loan', '=', 'approval_subquery.loan_id')
            ->leftJoin($chargesSub, 'customer_loan.idCustomer_Loan', '=', 'charges_subquery.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.Status', '=', $status)
            ->whereRaw('COALESCE(approval_subquery.pending_approvals, 0) = 0') // fully approved
            ->select(
                'customer_loan.*',

                // ✅ use your actual bank_sub columns
                DB::raw('bank_sub.bank_name        AS bank_name'),
                DB::raw('bank_sub.account_name     AS bank_account_name'),
                DB::raw('bank_sub.account_number   AS bank_account_number'),
                DB::raw('bank_sub.branch           AS bank_branch'),
                DB::raw('bank_sub.branch_id        AS bank_branch_id'),

                'loan_category.Name as loan_name',
                'customer.*',
                'customer_loan.Interest_Rate',
                'customer_loan.Installment_Count',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('IFNULL(subquery.group_id, 0) as group_id'),
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(center.Name, "-") as center_name'),
                DB::raw('IFNULL(route.name, "-") as route_name'),
                DB::raw('IFNULL(route.root_code, "-") as route_code'),
                DB::raw('IFNULL(collector.id, 0) as collector_id'),
                DB::raw('IFNULL(collector.Full_Name, "-") as collector_name'),
                'u1.Full_Name as user_name',
                'u2.Full_Name as lending_officer',
                DB::raw('IFNULL(approval_subquery.approval_count, 0) as approval_count'),
                DB::raw('IFNULL(approval_subquery.pending_approvals, 0) as pending_approvals'),
                DB::raw('IFNULL(charges_subquery.total_other_charges, 0) as total_other_charges')
            );


        // Filters
        if ($group != '0') {
            $loanQuery->where('subquery.group_id', '=', $group);
        }
        if ($category != '0') {
            $loanQuery->where('loan_category.idLoan_Category', '=', $category);
        }
        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }
        if ($route != '0') {
            $loanQuery->where('customer.route_id', '=', $route);
        }

        $loanQuery->orderBy('customer_loan.idCustomer_Loan', 'desc');

        $loan = $loanQuery->get();

        return response()->json(['item' => $loan, 'message' => 'filtered'], 200);
    }




    public function portfolio_performance()
    {
        $branch = DB::table('branch')->where('status','=','1')->get();
        $branch_access=session('branch_access');
        return view('pages.PortfolioPerformance', compact('branch','branch_access'));
    }

    public function getRoutesCenters(Request $request)
    {
        $branch_id = $request->branch_id;

        // Get Routes for selected Branch
        $routes = DB::table('route')
            ->where('branch_id', $branch_id)
            ->get();

        // Get Centers for selected Branch
        $centers = DB::table('center')
            ->where('branch_id', $branch_id)
            ->get();

        return response()->json([
            'routes' => $routes,
            'centers' => $centers
        ]);
    }

    public function getPortfolioPerformanceExcel(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $branch = $request->input('branch');
        $route = $request->input('route');
        $center_details = $request->input('center_details');
        $paid_type = $request->input('paid_type');

        // Subquery: Installments
        $installment_subquery = DB::table('installments')
            ->select('Customer_Loan_idCustomer_Loan', DB::raw('SUM(Installment_Amount) as schedule_repayments'))
            ->when(!empty($date_from) && !empty($date_to), function ($query) use ($date_from, $date_to) {
                return $query->whereBetween('Installment_Date', [$date_from, $date_to]);
            })
            ->groupBy('Customer_Loan_idCustomer_Loan');

        // Subquery: Loan Other Charges
        $loan_other_charges_subquery = DB::table('loan_other_charges')
            ->select('Customer_Loan_idCustomer_Loan', DB::raw('SUM(Amount) as processing_fee_received'))
            ->groupBy('Customer_Loan_idCustomer_Loan');

        // 🔹 Run Loan_Log separately
        $loanLogData = DB::table('Loan_Log')
            ->select(
                'Loan_ID',
                DB::raw('SUM(CASE WHEN Type IN ("Customer Payment", "Loan Settlement") THEN Capital_Payment WHEN Type = "Payment Undo" THEN -Capital_Payment ELSE 0 END) as capital_received'),
                DB::raw('SUM(CASE WHEN Type IN ("Customer Payment", "Loan Settlement") THEN Interest_Payment WHEN Type = "Payment Undo" THEN -Interest_Payment ELSE 0 END) as interest_received'),
                DB::raw('SUM(CASE WHEN Type IN ("Customer Payment", "Loan Settlement") THEN Panelty_Payment WHEN Type = "Payment Undo" THEN -Panelty_Payment ELSE 0 END) as penalty_received'),
                DB::raw('SUM(CASE WHEN Type IN ("Customer Payment", "Loan Settlement") THEN Amount WHEN Type = "Payment Undo" THEN -Amount ELSE 0 END) as collected_repayments')
            )
            ->when(!empty($date_from) && !empty($date_to), function ($query) use ($date_from, $date_to) {
                return $query->whereBetween('Date_Time', [$date_from, $date_to]);
            })
            ->groupBy('Loan_ID')
            ->get()
            ->keyBy('Loan_ID'); // 🔑 So we can match it later easily

        // 📌 1️⃣ Center-wise Summary Query (without Loan_Log)
        $centerSummaryQuery = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
            FROM group_has_customer 
            LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->join('branch', 'customer_loan.branch_id', '=', 'branch.branch_id')
            ->leftJoinSub($installment_subquery, 'installments', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan');
            })
            ->leftJoinSub($loan_other_charges_subquery, 'loan_other_charges', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'loan_other_charges.Customer_Loan_idCustomer_Loan');
            })
            ->leftJoin(DB::raw('(SELECT Customer_idCustomer, COUNT(*) as loan_count FROM customer_loan GROUP BY Customer_idCustomer) as loan_count_table'),
                'customer_loan.Customer_idCustomer', '=', 'loan_count_table.Customer_idCustomer')
            ->select(
                'center.Name as center_name',
                'branch.Name as branch_name',
                'route.name as route_name',
                DB::raw('SUM(customer_loan.Amount) as total_disbursement'),
                DB::raw('SUM(customer_loan.capital_balance) as capital_balance'),
                DB::raw('SUM(customer_loan.installment_balance) as installment_balance'),
                DB::raw('SUM(customer_loan.Total_Loan_Amount) as total_loan_amount'),
                DB::raw('COUNT(customer_loan.idCustomer_Loan) as issued_loan_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN loan_count_table.loan_count = 1 THEN customer_loan.Customer_idCustomer END) as new_clients'),
                DB::raw('COUNT(DISTINCT CASE WHEN loan_count_table.loan_count > 1 THEN customer_loan.Customer_idCustomer END) as repeat_clients'),
                DB::raw('COALESCE(SUM(installments.schedule_repayments), 0) as schedule_repayments'),
                DB::raw('COALESCE(SUM(loan_other_charges.processing_fee_received), 0) as processing_fee_received')
            )
            ->whereIn('customer_loan.Status', [0, 1]);


        if ($branch != "0") {
            $centerSummaryQuery->where('customer_loan.branch_id', $branch);
        }
        if ($route != "0") {
            $centerSummaryQuery->where('customer.route_id', $route);
        }
        if ($center_details != "0") {
            $centerSummaryQuery->where('subquery.center_id', $center_details);
        }

        $centerSummaryData = $centerSummaryQuery
            ->groupBy('branch.Name', 'route.name', 'center.Name')
            ->get();

        // 📌 2️⃣ Loan-wise Details Query (also without Loan_Log)
        $loanDetailsQuery = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
            FROM group_has_customer 
            LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->join('branch', 'customer_loan.branch_id', '=', 'branch.branch_id')
            ->leftJoinSub($installment_subquery, 'installments', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan');
            })
            ->leftJoinSub($loan_other_charges_subquery, 'loan_other_charges', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'loan_other_charges.Customer_Loan_idCustomer_Loan');
            })
            ->select(
                'center.Name as center_name',
                'customer_loan.idCustomer_Loan as loan_id',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.installment_balance as installment_balance',
                'customer.First_Name as customer_name',
                'customer_loan.Amount as loan_disbursement',
                'customer_loan.Total_Loan_Amount as loan_amount',
                'installments.schedule_repayments',
                'loan_other_charges.processing_fee_received'
            )
            ->whereIn('customer_loan.Status', [0, 1]);


        if ($branch != "0") {
            $loanDetailsQuery->where('customer_loan.branch_id', $branch);
        }
        if ($route != "0") {
            $loanDetailsQuery->where('customer.route_id', $route);
        }
        if ($center_details != "0") {
            $loanDetailsQuery->where('subquery.center_id', $center_details);
        }

        $loanDetailsData = $loanDetailsQuery->get();

        foreach ($loanDetailsData as $loan) {
            $log = $loanLogData->get($loan->loan_id, (object)[
                'capital_received' => 0,
                'interest_received' => 0,
                'penalty_received' => 0,
                'collected_repayments' => 0,
            ]);

            $loan->capital_received = $log->capital_received;
            $loan->interest_received = $log->interest_received;
            $loan->penalty_received = $log->penalty_received;
            $loan->collected_repayments = $log->collected_repayments;

            // ✅ Apply paid_type filter
            if ($paid_type === "1" && floatval($loan->collected_repayments) != 0) {
                continue; // Not Paid: skip paid
            }
            if ($paid_type === "2" && floatval($loan->collected_repayments) <= 0) {
                continue; // Paid: skip unpaid
            }

        }

        $finalData = [];

        foreach ($centerSummaryData as $center) {
            $centerData = (array)$center;
            $centerData['capital_received'] = 0;
            $centerData['interest_received'] = 0;
            $centerData['penalty_received'] = 0;
            $centerData['collected_repayments'] = 0;
            $centerData['loan_details'] = [];

            foreach ($loanDetailsData as $loan) {
                if ($loan->center_name === $center->center_name) {

                    // ✅ Filter loan rows based on paid_type
                    if ($paid_type === "1" && floatval($loan->collected_repayments) != 0) {
                        continue; // Skip paid
                    }
                    if ($paid_type === "2" && floatval($loan->collected_repayments) <= 0) {
                        continue; // Skip unpaid
                    }

                    // Add loan to center totals
                    $centerData['capital_received'] += $loan->capital_received;
                    $centerData['interest_received'] += $loan->interest_received;
                    $centerData['penalty_received'] += $loan->penalty_received;
                    $centerData['collected_repayments'] += $loan->collected_repayments;

                    $centerData['loan_details'][] = (array)$loan;
                }
            }

            // ✅ Add only if we have valid loan_details OR paid_type is summary only
            if (
                $paid_type === "0" || // All
                ($paid_type === "1" && $centerData['collected_repayments'] == 0) || // Not paid
                ($paid_type === "2" && $centerData['collected_repayments'] > 0)     // Paid
            ) {
                $finalData[] = $centerData;
            }
        }



        return response()->json(['data' => $finalData]);
    }



    public function report_disbursement()
    {
        $branch_access=session('branch_access');
        return view('pages.DisbursmentPerformanceReport',compact('branch_access')); // assuming this is your blade file
    }

    public function getFilters_disbursement()
    {
        $branches = DB::table('branch')->where('status', 1)->get(['branch_id', 'Name']);
        $centers = DB::table('center')->get(['idCenter', 'Name', 'branch_id']);
        $products = DB::table('loan_category')->get(['idLoan_Category', 'Name', 'branch_id', 'Product_code']);
        $route = DB::table('route')->get(['id_route', 'name', 'root_code']);

        return response()->json([
            'branches' => $branches,
            'centers' => $centers,
            'route' => $route,
            'products' => $products
        ]);
    }

    public function get_report_disbursement(Request $request)
    {
        $query = DB::table('customer_loan as cl')
            ->join('customer', 'cl.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
            FROM group_has_customer 
            LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin('loan_category as lc', 'lc.idLoan_Category', '=', 'cl.Loan_Category_idLoan_Category')
            ->whereIn('cl.Status', [0, 1]);


        if ($request->date_from) {
            $query->whereDate('cl.Date_Time', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('cl.Date_Time', '<=', $request->date_to);
        }

        if ($request->branch_id) {
            $query->where('cl.branch_id', $request->branch_id);
        }

        if ($request->center_id) {
            $query->where('center.idCenter', $request->center_id); // assuming this column exists
        }

        if ($request->product_id) {
            $query->where('cl.Loan_Category_idLoan_Category', $request->product_id);
        }

        if ($request->route_id) {
            $query->where('customer.route_id', $request->route_id);
        }

        $loans = $query->select(
            'cl.idCustomer_Loan',
            'cl.Loan_No',
            DB::raw('IFNULL(center.Name, "-") as center_no'),
            DB::raw('IFNULL(customer_group.Name, "-") as Group_name'),
            'customer.cus_number as cus_number',
            'customer.First_Name as customer_fname',
            'customer.Last_Name as customer_lastname',
            'cl.Date_Time as create_date',
            'cl.Date_Time as disburse_date',
            'lc.Name as product_name',
            'lc.Product_code as Product_code',
            'route.name as route_name',
            'cl.Amount',
            'cl.Interest_Amount',
            'cl.Total_Loan_Amount',
            'cl.Total_Other_Amount',
            'cl.capital_balance',
            'cl.installment_balance',
            'cl.Balance_Amount',
            'cl.Status'
        )->orderBy('disburse_date')->get();

        $data = $loans->map(function ($loan) {

            $customer_log = tableWithBranch('customer_log')
                ->where('type', '=', 'Create Loan')
                ->where('description_id', '=', $loan->idCustomer_Loan)
                ->first();

            if ($customer_log) {
                $create = \Carbon\Carbon::parse($customer_log->date . ' ' . $customer_log->time);
                $create_display = $customer_log->date . ' ' . $customer_log->time;
            } else {
                // Fallback to loan Date_Time (set to 00:00 if no time part)
                $create = \Carbon\Carbon::parse($loan->create_date)->startOfDay();
                $create_display = \Carbon\Carbon::parse($loan->create_date)->format('Y-m-d 00:00:00');
            }


            $days = '0 days';

            if ($loan->disburse_date) {
                $disburse = \Carbon\Carbon::parse($loan->disburse_date);
                $diff = $create->diff($disburse);

                $days = $diff->days . ' days';

                $timeParts = [];

                if ($diff->h > 0) {
                    $timeParts[] = $diff->h . ' hrs';
                }
                if ($diff->i > 0) {
                    $timeParts[] = $diff->i . ' min';
                }
                if ($diff->s > 0) {
                    $timeParts[] = $diff->s . ' sec';
                }

                if (!empty($timeParts)) {
                    $days .= ' ' . implode(' ', $timeParts);
                }
            }





            return [
                'idCustomer_Loan' => $loan->idCustomer_Loan,
                'Loan_No' => $loan->Loan_No,
                'create_date' => $create_display,
                'disburse_date' => $loan->disburse_date ?? '-',
                'time' => $days,
                'product_name' => $loan->product_name,
                'Product_code' => $loan->Product_code,
                'Amount' => number_format($loan->Amount, 2),
                'Interest_Amount' => number_format($loan->Interest_Amount, 2),
                'Total_Loan_Amount' => number_format($loan->Total_Loan_Amount, 2),
                'Total_Other_Amount' => number_format($loan->Total_Other_Amount, 2),
                'capital_balance' => number_format($loan->capital_balance, 2),
                'Other_Amount_Balance' => number_format($loan->Interest_Amount-$loan->installment_balance, 2),
                'Balance_Amount' => number_format($loan->Balance_Amount, 2),
                'Status' => $loan->Status,
                'cus_number' => $loan->cus_number,
                'customer_fname' => $loan->customer_fname,
                'customer_lastname' => $loan->customer_lastname,
                'center_no' => $loan->center_no,
                'Group_name' => $loan->Group_name,
                'route_name' => $loan->route_name ?? '-',
            ];
        });

        return response()->json($data);
    }


    public function destroy_loan(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer',
            'mode'    => 'required|in:password,approval',
            'admin_password' => 'nullable|string'
        ]);

        $loanId   = (int) $request->loan_id;
        $branchId = (int) session('branch_id');
        $userId   = (int) session('userid');
        $mode     = $request->mode;

        if ($mode === 'password') {
            // Verify admin password of the current user (or any admin policy you have)
            $user = DB::table('user')->where('id', $userId)->where('Designation','=','Admin')->first();
            if (!$user || !Hash::check($request->admin_password ?? '', $user->password)) {
                return response()->json(['error' => 'Invalid admin password.'], 403);
            }

            // Perform deletion immediately
            [$ok, $msg, $payload] = $this->performLoanDelete($loanId, $branchId, $userId);
            if (!$ok) {
                return response()->json(['message' => 'Loan delete failed', 'error' => $msg], 500);
            }
            return response()->json(['message' => 'Loan deleted successfully. Disbursement reversed, data archived.', 'audit_id' => $payload['audit_id'] ?? null], 200);

        } else {
            // Create approval request
            if (!Schema::hasTable('loan_delete_requests')) {
                DB::statement("
                CREATE TABLE IF NOT EXISTS `loan_delete_requests` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `loan_id` BIGINT UNSIGNED NOT NULL,
                  `branch_id` BIGINT UNSIGNED NOT NULL,
                  `requested_by` BIGINT UNSIGNED NOT NULL,
                  `requested_at` DATETIME NOT NULL,
                  `status` ENUM('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
                  `approved_by` BIGINT UNSIGNED NULL,
                  `approved_at` DATETIME NULL,
                  `audit_id` BIGINT UNSIGNED NULL,
                  `context` JSON NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            }

            // inside else {  // Create approval request
            $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loanId)->first();
            if (!$loan) return response()->json(['error' => 'Loan not found'], 404);

            $customer = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
            $product  = tableWithBranch('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();

            $reqId = DB::table('loan_delete_requests')->insertGetId([
                'loan_id'      => $loanId,
                'branch_id'    => $branchId,
                'requested_by' => $userId,
                'requested_at' => now(),
                'status'       => 'PENDING',
                'context'      => json_encode([
                    'Loan_No'       => $loan->Loan_No,
                    'Amount'        => (string)$loan->Amount,
                    'Customer_Id'   => $customer->idCustomer ?? null,
                    'Customer_Name' => trim(($customer->First_Name ?? '').' '.($customer->Last_Name ?? '')),
                    'Product'       => $product->Name ?? null,
                    // 'Reason'      => $request->input('delete_reason') ?? null,  // if you capture a reason
                ], JSON_UNESCAPED_UNICODE),
            ]);


            return response()->json(['need_approval' => true, 'request_id' => $reqId], 200);
        }
    }

    public function approve_destroy_loan(Request $request)
    {
        // Only admins should reach here — adapt to your role system
        $request->validate([
            'request_id' => 'required|integer'
        ]);

        $approverId = (int) session('userid');
        $branchId   = (int) session('branch_id');

        $req = DB::table('loan_delete_requests')->where('id', $request->request_id)->first();
        if (!$req) return response()->json(['error' => 'Approval request not found'], 404);
        if ($req->status !== 'PENDING') return response()->json(['error' => 'Request is not pending'], 409);

        // Optional: verify $approverId is admin
        // e.g., $isAdmin = DB::table('user')->where('idUser', $approverId)->value('role') === 'admin';
        // if (!$isAdmin) return response()->json(['error'=>'Forbidden'],403);

        [$ok, $msg, $payload] = $this->performLoanDelete((int)$req->loan_id, (int)$req->branch_id, $approverId);
        if (!$ok) {
            return response()->json(['message' => 'Loan delete failed', 'error' => $msg], 500);
        }

        DB::table('loan_delete_requests')->where('id', $req->id)->update([
            'status'      => 'APPROVED',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'audit_id'    => $payload['audit_id'] ?? null
        ]);

        return response()->json(['message' => 'Loan deletion approved and completed.', 'audit_id' => $payload['audit_id'] ?? null], 200);
    }


    private function performLoanDelete(int $loanId, int $branchId, int $actorUserId): array
    {
        // ---------- UNDO PAYMENTS FIRST (uses TodayPaymentController; each call has its own tx) ----------
        $eligiblePayments = DB::table('customer_payments')
            ->where('Customer_Loan_idCustomer_Loan', $loanId)
            ->where('branch_id', $branchId)
            ->orderByDesc('idCustomer_Payments')
            ->get();

// Keep a snapshot of all payment rows BEFORE undo
        $paymentsBeforeUndo = $eligiblePayments->map(function ($p) {
            return (array)$p;
        })->values();

        $undonePaymentIds = [];
        $undoReason = 'Payment Undo (Loan Delete)';

        if ($eligiblePayments->count()) {
            $tp = app(TodayPaymentController::class); // resolve your controller

            foreach ($eligiblePayments as $p) {
                // Call your existing controller method (keeps your logic, SMS, logs, bank reversals, etc.)
                $resp = $tp->undoPayment(new \Illuminate\Http\Request([
                    'reason' => $undoReason,
                ]), (int)$p->idCustomer_Payments);

                // Validate result
                $ok = !method_exists($resp, 'getStatusCode') || $resp->getStatusCode() === 200;
                if (!$ok) {
                    $payload = method_exists($resp, 'getContent') ? @json_decode($resp->getContent(), true) : null;
                    $msg = $payload['message'] ?? 'Undo payment failed';
                    throw new \Exception("Payment {$p->idCustomer_Payments} undo failed: {$msg}");
                }

                $undonePaymentIds[] = (int)$p->idCustomer_Payments;
            }
        }

// After all undos, fetch what those rows look like NOW (status/amount changed)
        $paymentsAfterUndo = [];
        if ($undonePaymentIds) {
            $paymentsAfterUndo = DB::table('customer_payments')
                ->whereIn('idCustomer_Payments', $undonePaymentIds)
                ->get()
                ->map(function ($p) { return (array)$p; })
                ->values();
        }

        DB::beginTransaction();
        try {
            $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loanId)->lockForUpdate()->first();
            if (!$loan) {
                DB::rollBack();
                return [false, 'Loan not found', []];
            }

            // Gather related data for archive
            $installments      = DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->get();
            $loanOtherCharges  = DB::table('loan_other_charges')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->get();
            $witnesses         = DB::table('witness')->where('Customer_Loan_idCustomer_Loan', $loanId)->orWhere('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->get();
            $approvals         = DB::table('loan_has_approval')->where('loan_id', $loanId)->where('branch_id', $branchId)->get();
            $approvalChecklist = DB::table('loan_has_approval_checklist')->where('loan_id', $loanId)->where('branch_id', $branchId)->get();
            $savingAccounts    = DB::table('Customer_Saving_Accounts')->where('Loan_Id', $loanId)->where('branch_id', $branchId)->get();
            $savingAccountIds  = $savingAccounts->pluck('id')->filter()->values();
            $savingLogsByAcc   = [];
            foreach ($savingAccountIds as $sid) {
                $savingLogsByAcc[$sid] = DB::table('Savings_Account_Log')->where('Saving_Acount_Id', $sid)->where('branch_id', $branchId)->get();
            }

            $customer = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
            $product  = tableWithBranch('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();



            // Ensure audit table
            if (!Schema::hasTable('loan_delete_audit')) {
                DB::statement("
                CREATE TABLE IF NOT EXISTS `loan_delete_audit` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `loan_id` BIGINT UNSIGNED NOT NULL,
                  `branch_id` BIGINT UNSIGNED NOT NULL,
                  `deleted_by` BIGINT UNSIGNED NOT NULL,
                  `deleted_at` DATETIME NOT NULL,
                  `loan_snapshot` JSON NOT NULL,
                  `related_snapshots` JSON NOT NULL,
                  `reversal_metadata` JSON NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            }

            // Reverse disbursement transfers if applicable
            $companyBankId = (int) ($loan->company_bank_account ?? 0);
            $default1 = tableWithBranch('company_bank_accounts')->where('Bank_Type', 'System_default_1')->first();
            $default9 = tableWithBranch('company_bank_accounts')->where('Bank_Type', 'System_default_9')->first();

            // Reverse Issue Loan
            if ($companyBankId && $default1) {
                $comment = "REVERSAL of Issue Loan\nLoan Number : {$loan->Loan_No}\nLoan Amount : {$loan->Amount}\n";
                // company bank DEBIT (reverse)
                $this->bankLogController->index($companyBankId, "Reversal - Issue Loan", $comment, "-", "debit", $loan->Amount, $default1->Idbank);
                // default_1 CREDIT (reverse)
                $this->bankLogController->index($default1->Idbank, "Reversal - Issue Loan", $comment, "-", "credit", $loan->Amount, $companyBankId);
            }

            // Reverse Doc Charges
            $sumOther = DB::table('loan_other_charges')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->sum('Amount');
            if ($sumOther > 0 && $companyBankId && $default9) {
                $docComment = "REVERSAL of Loan Document Charges\nLoan Number : {$loan->Loan_No}\nAmount : {$sumOther}\n";
                // company CREDIT
                $this->bankLogController->index($companyBankId, "Reversal - Loan Document Charges", $docComment, "-", "credit", $sumOther, $default9->Idbank);
                // default_9 DEBIT
                $this->bankLogController->index($default9->Idbank, "Reversal - Loan Document Charges", $docComment, "-", "debit", $sumOther, $companyBankId);

                // Neutralize income with compensating Expense
                $reason = "Reversal of Other loan charges for loan number: ({$loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                $exp = new \App\Models\Expenses();
                $exp->type       = "Expense";
                $exp->reason     = $reason;
                $exp->date       = date('Y-m-d');
                $exp->amount     = $sumOther;
                $exp->category_id= optional(tableWithBranch('income_category')->where('description','Other')->first())->id;
                $exp->bank_id    = 1;
                $exp->user_id    = $actorUserId;
                $exp->branch_id  = $branchId;
                $exp->save();
            }

            // Logs (customer + loan)
            $custLogReq = new Request([
                'customer_id'    => $loan->Customer_idCustomer,
                'description'    => "Loan Deleted ({$loan->Loan_No})\nLoan Amount : ({$loan->Amount})",
                'description_id' => $loanId,
                'comment'        => ' ',
                'type'           => 'Delete Loan',
            ]);
            $this->customerLogController->store($custLogReq);

            $this->LoanLogController->index(
                $loanId,
                'Loan Delete',
                $loanId,
                'Loan Delete Reversal',
                0, // Amount
                0, // Panelty_Payment
                0, // Interest_Payment
                0, // Capital_Payment
                0, // Savings_Payment
                0, // Panelty_Balance
                0, // Interest_Balance
                0, // Capital_Balance
                0, // Total_Pending_Balance
                '0' // Status
            );


            $auditId = DB::table('loan_delete_audit')->insertGetId([
                'loan_id'          => $loanId,
                'branch_id'        => $branchId,
                'deleted_by'       => $actorUserId,
                'deleted_at'       => now(),
                'loan_snapshot'    => json_encode($loan, JSON_UNESCAPED_UNICODE),
                'related_snapshots'=> json_encode([
                    'installments'                 => $installments,
                    'loan_other_charges'           => $loanOtherCharges,
                    'witnesses'                    => $witnesses,
                    'loan_has_approval'            => $approvals,
                    'loan_has_approval_checklist'  => $approvalChecklist,
                    'saving_accounts'              => $savingAccounts,
                    'saving_logs'                  => $savingLogsByAcc,
                    'payments_before_undo'         => $paymentsBeforeUndo,   // << added
                    'payments_after_undo'          => $paymentsAfterUndo,    // << added
                    'customer'                     => $customer,
                    'product'                      => $product,
                ], JSON_UNESCAPED_UNICODE),
                'reversal_metadata' => json_encode([
                    'reversed_issue_loan'  => (bool) ($companyBankId && $default1),
                    'reversed_doc_charges' => (bool) ($sumOther > 0 && $companyBankId && $default9),
                    'company_bank_id'      => $companyBankId,
                    'default1_bank_id'     => optional($default1)->Idbank,
                    'default9_bank_id'     => optional($default9)->Idbank,
                    'sum_other_charges'    => (float) $sumOther,
                    'undone_payment_ids'   => $undonePaymentIds,             // << added
                    'undone_payments_count'=> count($undonePaymentIds),      // << added
                    'payments_undo_reason' => $undoReason,                   // << added
                ], JSON_UNESCAPED_UNICODE),
            ]);


            // Delete dependents
            foreach ($savingAccountIds as $sid) {
                DB::table('Savings_Account_Log')->where('Saving_Acount_Id', $sid)->where('branch_id', $branchId)->delete();
            }
            DB::table('Customer_Saving_Accounts')->where('Loan_Id', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('loan_has_approval_checklist')->where('loan_id', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('loan_has_approval')->where('loan_id', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('witness')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('witness')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('loan_other_charges')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->delete();
            DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', $branchId)->delete();

            DB::table('customer_loan')->where('idCustomer_Loan', $loanId)->where('branch_id', $branchId)->delete();

            DB::commit();
            return [true, null, ['audit_id' => $auditId]];

        } catch (\Throwable $e) {
            Log::info($e);
            DB::rollBack();
            return [false, $e->getMessage(), []];
        }
    }

    public function delete_loan_requests(Request $request)
    {
        $branchId = (int) session('branch_id');

        if (!Schema::hasTable('loan_delete_requests')) {
            return back()->with('error', 'No delete requests table found.');
        }

        $status = $request->query('status');

        $q = DB::table('loan_delete_requests as r')
            ->leftJoin('customer_loan as l', 'r.loan_id', '=', 'l.idCustomer_Loan')
            ->leftJoin('customer as c', 'l.Customer_idCustomer', '=', 'c.idCustomer')
            ->leftJoin('user as u1', 'u1.id', '=', 'r.requested_by')
            ->leftJoin('user as u2', 'u2.id', '=', 'r.approved_by')
            ->select(
                'r.*',
                'l.Loan_No',
                'l.Amount',
                'c.First_Name', 'c.Last_Name',
                DB::raw("u1.Full_Name as requester_name"),
                DB::raw("u2.Full_Name as approver_name")
            )
            ->where('r.branch_id', $branchId);

        if (in_array($status, ['PENDING','APPROVED','REJECTED'])) {
            $q->where('r.status', $status);
        }

        if ($search = trim($request->query('search', ''))) {
            $q->where(function ($x) use ($search) {
                $x->where('l.Loan_No', 'like', "%{$search}%")
                    ->orWhere('c.First_Name', 'like', "%{$search}%")
                    ->orWhere('c.Last_Name', 'like', "%{$search}%");
            });
        }

        $requests = $q->orderByDesc('r.id')->get();

        foreach ($requests as $r) {
            // ----- Context taken from loan_delete_requests.context -----
            // Example (your data):
            // {"Loan_No":"...","Amount":"40000","Customer_Id":3376,"Customer_Name":"...","Product":"..."}
            $ctx = json_decode($r->context ?? '', true) ?: [];

            // Fallbacks from context
            $r->ctx_loan_no       = $ctx['Loan_No']        ?? null;
            $r->ctx_amount        = $ctx['Amount']         ?? null;
            $r->ctx_customer_name = $ctx['Customer_Name']  ?? null;  // <- use Customer_Name
            $r->ctx_customer_id   = $ctx['Customer_Id']    ?? null;
            $r->ctx_product       = $ctx['Product']        ?? null;

            // Defaults for audit-based fallbacks
            $r->snap_customer_no    = null;
            $r->snap_customer_name  = null;
            $r->snap_customer_phone = null;

            // Payments summary (for modal)
            $r->payments_summary = null;

            if ($r->audit_id) {
                $audit = DB::table('loan_delete_audit')->where('id', $r->audit_id)->first();
                if ($audit) {
                    $related = json_decode($audit->related_snapshots ?? '{}', true);

                    // 1) Customer fallback from related_snapshots.customer
                    // (only when joined customer name is missing AND context also didn't have it)
                    if ((empty($r->First_Name) && empty($r->Last_Name)) && empty($r->ctx_customer_name)) {
                        $cust = $related['customer'] ?? [];
                        $r->snap_customer_no    = $cust['cus_number'] ?? null;
                        $r->snap_customer_name  = trim(($cust['First_Name'] ?? '').' '.($cust['Last_Name'] ?? '')) ?: null;
                        $r->snap_customer_phone = $cust['Contact_No'] ?? ($cust['Gua_contact'] ?? null);
                    }

                    // 2) Payments for modal
                    $before = $related['payments_before_undo'] ?? [];
                    if (!empty($before)) {
                        $items = [];
                        $total = 0.0;
                        foreach ($before as $p) {
                            $amt = (float) ($p['Amount'] ?? 0);
                            $total += $amt;
                            $items[] = [
                                'id'     => $p['idCustomer_Payments'] ?? null,
                                'date'   => $p['Date'] ?? '-',
                                'amount' => $amt,
                                'type'   => $p['Payment_type'] ?? '-',
                                'user'   => $p['User_idUser'] ?? '-',
                                'desc'   => $p['Description'] ?? '',
                            ];
                        }
                        $r->payments_summary = [
                            'count'  => count($items),
                            'total'  => $total,
                            'items'  => $items,
                        ];
                    }

                    // 3) Ensure Loan_No/Amount exist from audit->loan_snapshot if still missing
                    if (empty($r->Loan_No) || empty($r->Amount) || empty($r->ctx_loan_no) || empty($r->ctx_amount)) {
                        $snap = json_decode($audit->loan_snapshot ?? '{}', true);
                        $r->ctx_loan_no = $r->ctx_loan_no ?: ($snap['Loan_No'] ?? null);
                        $r->ctx_amount  = $r->ctx_amount  ?: ($snap['Amount']  ?? null);
                    }
                }
            }
        }

        $counts = DB::table('loan_delete_requests')
            ->selectRaw("
          SUM(status='PENDING') as pending_count,
          SUM(status='APPROVED') as approved_count,
          SUM(status='REJECTED') as rejected_count
        ")
            ->where('branch_id', $branchId)
            ->first();

        return view('pages.loan_delete_requests', compact('requests','counts','status','search'));
    }





    public function reject_destroy_loan(Request $request)
    {
        $request->validate(['request_id' => 'required|integer']);
        $req = DB::table('loan_delete_requests')->where('id', $request->request_id)->first();
        if (!$req) return response()->json(['error' => 'Request not found'], 404);
        if ($req->status !== 'PENDING') return response()->json(['error' => 'Request is not pending'], 409);

        DB::table('loan_delete_requests')->where('id', $req->id)->update([
            'status'      => 'REJECTED',
            'approved_by' => (int) session('userid'),
            'approved_at' => now(),
        ]);

        return response()->json(['message' => 'Request rejected.'], 200);
    }






}
