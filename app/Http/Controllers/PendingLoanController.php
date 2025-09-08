<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $userController->create_panelty();

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
            return response()->json([
                'item'     => $affected,
                'redirect' => true,
                'url'      => url('/loan_disbursement')
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
        $group = $request->group;
        $category = $request->category;
        $status = $request->status;
        $customer = $request->customer;
        $center_details = $request->center_details;
        $route = $request->route;

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('customer_has_bank', 'customer_loan.Customer_idCustomer', '=', 'customer_has_bank.cus_id')
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
            ->leftJoin(DB::raw('(SELECT Customer_Loan_idCustomer_Loan, SUM(Amount) as total_other_charges 
            FROM loan_other_charges 
            GROUP BY Customer_Loan_idCustomer_Loan) as charges_subquery'),
                'customer_loan.idCustomer_Loan', '=', 'charges_subquery.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.Status', '=', $status)
            ->where('approval_subquery.pending_approvals', '=', 0)  // Ensure no pending approvals (fully approved)
            ->select(
                'customer_loan.*',
                'customer_has_bank.*',
                'loan_category.Name as loan_name',
                'customer.*',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(center.Name, "-") as center_name'),
                DB::raw('IFNULL(route.name, "-") as route_name'),
                'u1.Full_Name as user_name',
                'u2.Full_Name as lending_officer',
                DB::raw('IFNULL(approval_subquery.approval_count, 0) as approval_count'),
                DB::raw('IFNULL(approval_subquery.pending_approvals, 0) as pending_approvals'),
                DB::raw('IFNULL(charges_subquery.total_other_charges, 0) as total_other_charges') // Sum of Amount from loan_other_charges
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
            DB::raw('IFNULL(center.No, "-") as center_no'),
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



}
