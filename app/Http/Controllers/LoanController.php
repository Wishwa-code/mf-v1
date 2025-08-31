<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Models\Reschedule;

class LoanController extends Controller
{

    protected $customerLogController;
    protected $CapitalBalanceController;

    public function __construct(CustomerLogController $customerLogController,CapitalBalanceController $capitalBalanceController)
    {
        $this->customerLogController = $customerLogController;
        $this->CapitalBalanceController = $capitalBalanceController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = tableWithBranch('customer')->where('Status','=','1')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $collector = tableWithBranch('user')
            ->where('collector','=','1')
            ->get();
        return view('pages.IssueLoan', compact('customers', 'center', 'product', 'lending_officer','company','collector'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = tableWithBranch('customer')->where('Status','=','1')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $collector = tableWithBranch('user')
            ->where('collector','=','1')
            ->get();
        return view('pages.LoanCalculator', compact('customers', 'center', 'product', 'lending_officer','company','collector'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user_id = (int)session('userid');

        $loan = new Loan();

        $date = Carbon::now()->toDateString();

        $customer_id = $request->customer_id;
        $type_loan_number = $request->type_loan_number;


        // Step 1: Fetch necessary data
        $maxId = DB::table('customer_loan')->where('branch_id', session('branch_id'))->count('idCustomer_Loan') ?? 1;
        $maxId++;
        $type = $request->loan_type;
        $company = tableWithBranch('company')->first();
        $branch_no = $company->branch;
        $loan_format = $company->loan_format;

        $loan_num_type = $company->loan_num_type;

// Initialize the loan_number_txt
        $loan_number_txt = $type_loan_number;
// Format the ID with leading zeros (e.g., 001, 010, 100, etc.)
        $formatted_loan_id = str_pad($maxId, 3, '0', STR_PAD_LEFT);
        $product_code=tableWithBranch('loan_category')
            ->where('idLoan_Category','=',$request->loan_cate_id)
            ->first();
        $cus_loan_count=tableWithBranch('customer_loan')
            ->where('Customer_idCustomer','=',$customer_id)
            ->count();
        if ($type_loan_number==""){
            if ($loan_num_type === "Customize") {
                $branch_no_txt=$branch_no . '/';
                if ($branch_no==""){
                    $branch_no_txt="";
                }
                $loan_number_txt = $branch_no_txt . $formatted_loan_id;
            } else {

                if ($type == "0") {
                    $loan_format = $company->inv_loan_format;
                    $cus_root=tableWithBranch('customer','customer')
                        ->leftjoin('route', 'route.id_route', '=', 'customer.route_id')
                        ->where('idCustomer','=',$customer_id)
                        ->first();
                    // Step 1: Get the route_id of the given customer
                    $routeId = DB::table('customer')
                        ->where('idCustomer', $customer_id)
                        ->value('route_id');

                    // Step 2: Count total loans in that route
                    $routewiseloanCount = DB::table('customer_loan as cl')
                        ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                        ->where('c.route_id', $routeId)
                        ->count();

                    $placeholders = [
                        '@Branch_No@' => $branch_no,
                        '@Root@' => $cus_root->root_code ?? '',
                        '@Product_Code@' => $product_code->Product_code,
                        '@Customer_No@' => str_pad($cus_root->idCustomer, 3, '0', STR_PAD_LEFT),
                        '@Auto_Id@' => $formatted_loan_id,
                        '@Loan_Count@' => $cus_loan_count+1,
                        '@RootlyCount@' => str_pad($routewiseloanCount+1, 3, '0', STR_PAD_LEFT),
                    ];

                    // Step 3: Replace placeholders in the loan_format
                    $loan_number_txt = $loan_format;
                    foreach ($placeholders as $placeholder => $value) {
                        $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                    }
                } else {
                    $loan_no = tableWithBranch('customer','customer')
                        ->leftJoin('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                        ->leftJoin('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                        ->leftJoin('center', 'center.idCenter', '=', 'customer_group.center_id')
                        ->leftJoin('route', 'route.id_route', '=', 'center.route_id')
                        ->where('customer.idCustomer', $customer_id)
                        ->select('customer.*','customer_group.*','center.*','route.root_code as root')
                        ->first();


                    if ($loan_no){
                        $center_id = $loan_no->idCenter;
                        $center_customer_count = DB::table('customer')
                            ->join('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                            ->join('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                            ->where('customer_group.center_id', $center_id)
                            ->distinct('customer.idCustomer') // Optional if customers can be in multiple groups
                            ->count('customer.idCustomer');

                        // Step 1: Get the route_id of the given customer
                        $routeId = DB::table('customer')
                            ->where('idCustomer', $customer_id)
                            ->value('route_id');

                        // Step 2: Count total loans in that route
                        $routewiseloanCount = DB::table('customer_loan as cl')
                            ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                            ->where('c.route_id', $routeId)
                            ->count();



                        // Step 2: Define the mapping
                        $placeholders = [
                            '@Branch_No@' => $branch_no,
                            '@Center_No@' => $loan_no->No,
                            '@Group_No@' => $loan_no->Group_No,
                            '@Product_Code@' => $product_code->Product_code,
                            '@Root@' => $loan_no->root,
                            '@Customer_No@' => str_pad($loan_no->idCustomer, 3, '0', STR_PAD_LEFT),
                            '@Auto_Id@' => $formatted_loan_id,
                            '@Loan_Count@' => $cus_loan_count+1,
                            '@Center_Cus_Count@' => $center_customer_count+1,
                            '@RootlyCount@' => str_pad($routewiseloanCount+1, 3, '0', STR_PAD_LEFT),
                        ];

                        // Step 3: Replace placeholders in the loan_format
                        $loan_number_txt = $loan_format;
                        foreach ($placeholders as $placeholder => $value) {
                            $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                        }
                    }
                }
            }
        }else{
            $loan_no = tableWithBranch('customer_loan')
                ->where('Loan_No','=',$type_loan_number)
                ->first();
            if ($loan_no){
                return response()->json(['item' => "1"], 200);
            }
        }


        if (!Schema::hasColumn('customer_loan', 'panelty_method')) {
            DB::statement(
                "ALTER TABLE `customer_loan`
         ADD COLUMN `panelty_method` VARCHAR(45) NOT NULL
         DEFAULT 'every_installment'"
            );
        }

        if (!Schema::hasColumn('customer_loan', 'Panelty_period')) {
            DB::statement(
                "ALTER TABLE `customer_loan`
         ADD COLUMN `Panelty_period` VARCHAR(45) NOT NULL
         DEFAULT 'Daily'"
            );
        }




        Log::info($loan_number_txt);
        $loan->Loan_No = $loan_number_txt;
        $loan->Loan_Category_idLoan_Category = $request->loan_cate_id;
        $loan->Customer_idCustomer = $request->customer_id;
        $loan->Leasing_type = $request->lease_type;
        $loan->Vehicle_No = $request->vehicle_num;
        $loan->Date_Time = $request->issue_date;
        $loan->Amount = $request->loan_amount;
        $loan->Interest_Rate = $request->interest;
        $loan->Panalty_Rate = $request->panelty_amount;
        $loan->Installment_Count = $request->ins_count;
        $loan->Interest_Amount = $request->interest_amount;
        $loan->Total_Other_Amount = $request->total_loan_charge;
        $loan->Other_Amount_Balance = $request->loan_charge_balance;
        $loan->Total_Loan_Amount = $request->total_loan_amount;
        $loan->Installment_Amount = $request->new_interest_amount;
        $loan->Collection_Type = $request->collection_type;
        $loan->Collection_Date = $request->installment_date_txt;
        $loan->Panalty_Date = $request->panelty_date;
        $loan->Balance_Amount = $request->total_loan_amount;
        $loan->Status = "-1";
        $loan->User_idUser = $user_id;
        $loan->capital_balance = $request->total_capital_amount;
        $loan->installment_balance = $request->total_interest_amount;
        $loan->type = $request->interest_method;
        $loan->Interest_period = $request->Interest_period;
        $loan->lending_officer_id = $request->lending_officer;
        $loan->collector_id = $request->collector_officer;
        $loan->cus_bank_account = $request->bank_acc;
        $loan->repayment_duration = $request->repayment_duration_period;
        $loan->loan_broker = $request->loan_broker;
        $loan->loan_broker_commission = $request->loan_broker_commission;
        $loan->saving_amount = $request->saving_amount ?? '0.00';
        $loan->branch_id = session('branch_id');

        $product = tableWithBranch('loan_category')->where('idLoan_Category', '=' ,$request->loan_cate_id)->first();
        if ($product){
            $loan->panelty_method = $product->panelty_method;
            $loan->Panelty_period = $product->Panelty_period;
        }


        $loan->save();

        $id = $loan->id;


        $product = tableWithBranch('loan_category')->where('idLoan_Category', '=' ,$request->loan_cate_id)->first();
        if ($product){
            $enable_saving_process=$product->enable_saving_process;
            if ($enable_saving_process=="Yes"){
                $saving_format=$company->account_saving_type;

                $saving_number_txt="";
                if ($saving_format === "Customize") {

                } else {
                    $loan_no = tableWithBranch('customer','customer')
                        ->leftJoin('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                        ->leftJoin('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                        ->leftJoin('center', 'center.idCenter', '=', 'customer_group.center_id')
                        ->join('route', 'route.id_route', '=', 'center.route_id')
                        ->where('customer.idCustomer', $customer_id)
                        ->first();

                    if ($loan_no){
                        // Step 2: Define the mapping
                        $placeholders = [
                            '@Branch_No@' => $branch_no,
                            '@Root@' => $loan_no->Group_No,
                            '@Center_No@' => $loan_no->No,
                            '@Group_No@' => $loan_no->Group_No,
                            '@Customer_No@' => $loan_no->idCustomer,
                            '@Auto_Id@' => $formatted_loan_id,
                            '@Loan_Count@' => $cus_loan_count+1,
                        ];

                        // Step 3: Replace placeholders in the loan_format
                        $saving_number_txt = $loan_format;
                        foreach ($placeholders as $placeholder => $value) {
                            $saving_number_txt = str_replace($placeholder, $value, $saving_number_txt);
                        }
                    }


                }


                // Prepare data for Customer_Saving_Accounts
                $savingData = [
                    'Customer_Id' => $customer_id,
                    'Loan_Id' => $id,
                    'Loan_No' => $loan_number_txt,
                    'Created_Date' => date('Y-m-d H:i:s'),
                    'Account_No' => $saving_number_txt,
                    'Account_Type' => "Saving",
                    'Balance' => "0.00",
                    'Status' => "1",
                ];

// Insert and get the ID of the saving account
                $saving = insertWithBranch('Customer_Saving_Accounts', $savingData);

// Prepare data for Savings_Account_Log
                $logData = [
                    'Saving_Acount_Id' => $saving,
                    'Date_Time' => date('Y-m-d H:i:s'),
                    'Type' => "Saving Account",
                    'Description' => "Account Creation",
                    'Credit' => 0.00,
                    'Debit' => 0.00,
                    'Balance' => 0.00,
                    'User' => $user_id,
                ];

// Insert log entry
                insertWithBranch('Savings_Account_Log', $logData);


            }
        }



        $saving_check=$request->saving;

        if ($saving_check=="Yes"){
            foreach ($request->installment as $item) {

                $customerLoanId = $id;
                $no = $item['No'];
                $installmentDate = $item['installmentDate'];
                $installmentAmount = $item['installmentAmount'];
                $capitalAmount = $item['capitalAmount'];
                $interestAmount = $item['interestAmount'];
                $panaltyDate = $item['panaltyDate'];
                $panaltyAmount = $item['panaltyAmount'];
                $savingAmount = $item['savingAmount'];
                $totalAmount = $item['totalAmount'];

                $paidAmount = "0.00";
                $panaltyBalance = $item['panaltyBalance'];
                $savingBalance = $item['savingBalance'];
                $installmentBalance = $item['installmentBalance'];
                $totalBalance = $item['totalBalance'];


                DB::table('installments')->insert([
                    'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                    'No' => $no,
                    'Installment_Date' => $installmentDate,
                    'Installment_Amount' => $installmentAmount,
                    'capital_amount' => $capitalAmount,
                    'interest_amount' => $interestAmount,
                    'Panalty_Amount' => $panaltyAmount,
                    'Saving_amount' => $savingAmount,
                    'Total_Amount' => $totalAmount,
                    'Paid_Amount' => $paidAmount,
                    'Panalty_Balance' => $panaltyBalance,
                    'Interest_Balance' => $interestAmount,
                    'capital_balance' => $capitalAmount,
                    'Total_Balance' => $totalBalance,
                    'Saving_balance' => $savingBalance,
                    'Status' => '0',
                    'Panelty_date' => $panaltyDate,
                    'Panelty_status' => '0',
                    'branch_id' => session('branch_id')
                ]);

            }
        }else{
            foreach ($request->installment as $item) {

                $customerLoanId = $id;
                $no = $item['No'];
                $installmentDate = $item['installmentDate'];
                $installmentAmount = $item['installmentAmount'];
                $capitalAmount = $item['capitalAmount'];
                $interestAmount = $item['interestAmount'];
                $panaltyDate = $item['panaltyDate'];
                $panaltyAmount = $item['panaltyAmount'];
                $totalAmount = $item['totalAmount'];
                $paidAmount = "0.00";
                $panaltyBalance = $item['panaltyBalance'];
                $installmentBalance = $item['installmentBalance'];
                $totalBalance = $item['totalBalance'];


                DB::table('installments')->insert([
                    'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                    'No' => $no,
                    'Installment_Date' => $installmentDate,
                    'Installment_Amount' => $installmentAmount,
                    'capital_amount' => $capitalAmount,
                    'interest_amount' => $interestAmount,
                    'Panalty_Amount' => $panaltyAmount,
                    'Total_Amount' => $totalAmount,
                    'Paid_Amount' => $paidAmount,
                    'Panalty_Balance' => $panaltyBalance,
                    'Interest_Balance' => $interestAmount,
                    'capital_balance' => $capitalAmount,
                    'Total_Balance' => $totalBalance,
                    'Status' => '0',
                    'Panelty_date' => $panaltyDate,
                    'Panelty_status' => '0',
                    'branch_id' => session('branch_id')
                ]);
            }
        }

//        $HolidayController=new HolidayController();
//        $HolidayController->store($id);

        if (isset($request->witnessesArray) && count($request->witnessesArray) > 0) {
            foreach ($request->witnessesArray as $item) {
                // Ensure $item is treated as an array
                $newtype = $item['guatantor_type'];
                $newtypeshow = "Guarantor";
                if ($newtype == "0") {
                    $newtypeshow = "Cross Customer";
                }

                // Prepare data for the witness
                $witnessData = [
                    'Customer_Loan_idCustomer_Loan' => $id,
                    'cus_id' => $item['cus_id'],
                    'type' => $newtypeshow,
                ];

// Insert the witness data with branch scoping
                insertWithBranch('witness', $witnessData);

            }
        }

        if (isset($request->loan_charge_table) && count($request->loan_charge_table) > 0) {
            foreach ($request->loan_charge_table as $item) {

                $customerLoanId = $id;
                $Description = $item['Description'];
                $Type = $item['Type'];
                $Amount = $item['Amount'];

                // Prepare data for the loan other charges
                $loanOtherChargesData = [
                    'Description' => $Description,
                    'Type' => $Type,
                    'Amount' => $Amount,
                    'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                ];

// Insert the loan other charges data with branch scoping
                insertWithBranch('loan_other_charges', $loanOtherChargesData);

            }
        }

        $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $request->loan_cate_id)
            ->first();




        $level=tableWithBranch('level')->where('product_id','=',$request->loan_cate_id)->get();
        foreach ($level as $item){
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


        $request = new Request([
            'customer_id' => $request->customer_id,
            'description' => "Created new loan ({$request->loan_number_txt})\nLoan Amount : {$request->loan_amount}\nProduct name : {$product->Name}",
            'description_id' => $id,
            'comment' => ' ',
            'type' => 'Create Loan',
        ]);

// Call the store method of CustomerLogController
        $this->customerLogController->store($request);
        $this->CapitalBalanceController->create($id);



        return response()->json(['item' => $id, 'installment' => $request->installment, 'type' => $type], 200);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customers = tableWithBranch('customer')
            ->where('idCustomer', '=', $id)->first();
        $category = tableWithBranch('loan_category')->get();
        return view('pages.IssueLoan_2page', compact('id', 'customers', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        // Fetch the list of centers
        $centers = DB::table('center')->where('branch_id', session('branch_id'))->get();

        // Fetch the list of groups
        $groups = DB::table('customer_group')->where('branch_id', session('branch_id'))->select('Group_No as group_name')->distinct()->get();

        // Initialize the query for fetching loans
        $query = tableWithBranch('customer_loan', 'customer_loan')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer_loan.*',
                'loan_category.Name as loan_name',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                'customer.First_Name as First_Name',
                'customer.cus_number as cus_number',
                'customer.Last_Name as Last_Name',
                'loan_category.Name as Name')
            ->where('customer_loan.Status', '!=', '-1');

        // Apply center filter if center_id is provided
        if ($request->has('center_id') && $request->center_id != '') {
            $query->where('center.idCenter', $request->center_id);
        }

        // Apply group filter if group_name is provided
        if ($request->has('group_name') && $request->group_name != '') {
            $query->where('subquery.group_name', $request->group_name);
        }

        // Execute the query and get the loan data
        $loan = $query->get();

        // Pass loan, centers, and groups data to the view
        return view('pages.AllLoanReport', compact('loan', 'centers', 'groups'));
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


//    public function saveFiles(Request $request)
//    {
//        // Define the directory where the file will be stored
//        $directory = 'documents';
//
//// Check if the directory exists, create it if not
//        if (!Storage::disk('public')->exists($directory)) {
//            Storage::disk('public')->makeDirectory($directory);
//        }
//
//        $documentsArray = $request->file('documents');
//
//        foreach ($documentsArray as $index => $file) {
//            if ($file) {
//                // Generate a unique filename to prevent overwriting files with the same name
//                $fileName = uniqid() . '_' . $file->getClientOriginalName();
//
//                // Move the file to the storage directory
//                $storedFile = Storage::disk('public')->putFileAs($directory, $file, $fileName);
//
//                // Retrieve document name and checked status from the request
//                $documentName = $request->documentNames[$index];
//                $issue_checked = $request->issue_checked[$index];
//
//                // Store document information in the database
//                $documentData = [
//                    'Name' => $documentName,
//                    'Path' => $storedFile,
//                    'Customer_Loan_idCustomer_Loan' => $request->id,
//                    'create_loan_check' => $issue_checked,
//                ];
//
//                DB::table('documents')->insert($documentData);
//            }
//        }
//
//        return response()->json(['message' => 'Documents saved successfully'], 200);
//
//
//
//        // Return a success response if the documents were processed successfully
//        return response()->json(['message' => 'Documents processed successfully.']);
//
//
//
//
//
//    }

    public function saveFiles(Request $request) {
        $documentNamesArray = $request->input('documentNames');
        $issueCheckedArray = $request->input('issue_checked');
        $documentsArray = $request->file('documents');

        if ($documentNamesArray && is_array($documentNamesArray) && $issueCheckedArray && is_array($issueCheckedArray)) {
            foreach ($documentNamesArray as $index => $documentName) {
                $file = $documentsArray[$index] ?? null;
                $issue_checked = $issueCheckedArray[$index];

                if ($file) {
                    $fileName = uniqid() . '_' . $file->getClientOriginalName();
                    $storedFile = Storage::disk('public')->putFileAs('documents', $file, $fileName);

                    // Prepare data for the document insertion
                    $documentData = [
                        'Name' => $documentName,
                        'Path' => $storedFile,
                        'Customer_Loan_idCustomer_Loan' => $request->id,
                        'create_loan_check' => $issue_checked,
                    ];

// Insert the document data with branch scoping
                    insertWithBranch('documents', $documentData);

                } else {
                    // Prepare data for the document insertion
                    $documentData = [
                        'Name' => $documentName,
                        'Path' => "-", // Assuming the path is intentionally set to "-"
                        'Customer_Loan_idCustomer_Loan' => $request->id,
                        'create_loan_check' => $issue_checked,
                    ];

// Insert the document data with branch scoping
                    insertWithBranch('documents', $documentData);

                }
            }
            return response()->json(['message' => 'Documents saved successfully']);
        }

        return response()->json(['message' => 'Invalid input'], 400);
    }

    public function saveFiles_Pending_loan(Request $request)
    {


        // Handle file upload
        if ($request->hasFile('documents')) {
            $file = $request->file('documents');
            // Define a path and filename for the uploaded file
            $filePath = 'documents/';
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store the file on the server (in storage/app/public/loan_documents)
            $file->storeAs('public/' . $filePath, $fileName);

            $documentData = [
                'Name' => $request->documentNames,
                'Path' => $filePath.$fileName,
                'Customer_Loan_idCustomer_Loan' => $request->id,
            ];

            // Insert the document data with branch scoping
            insertWithBranch('documents', $documentData);

            // Return a success response
            return response()->json(['success' => true], 200);
        }

        // If no file is provided or validation fails
        return response()->json(['error' => 'File upload failed'], 400);
    }





    public function loan_view(string $id)
    {

        $company = DB::table('company')->first(); // Assuming company does not need branch filtering
        $customers = tableWithBranch('customer')->get(); // Get customers with branch filtering
        $center = tableWithBranch('center')->get(); // Get centers with branch filtering
        $product = tableWithBranch('loan_category')->get(); // Get loan categories with branch filtering
        $loan = tableWithBranch('customer_loan', 'idCustomer_Loan') // Get loan with branch filtering
        ->where('idCustomer_Loan', $id)
            ->first();

        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();


        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = []; // Initialize an array to store details

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                // Add guarantor details to the array
                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                // Add customer details to the array
                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer
                ];
            }
        }
        $witnessCount = count($witnessDetails); // Get the length of the array


        return view('pages.LoanView', compact('customers','company', 'center', 'product', 'id', 'loan', 'installments', 'witnessDetails', 'witnessCount'));
    }


    public function loan_view_Np(string $id,int $type=0)
    {
        $company = DB::table('company')->first();
        // Fetch the loan
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        // Fetch the installments
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->orderBy('idInstallments')->get();
        $Saving_amountSum = $installments->sum('Saving_amount');
        $Panalty_BalanceSum = $installments->sum('Panalty_Balance');
        $Panalty_Amount = $installments->sum('Panalty_Amount');
//        $Saving_balance = $installments->sum('Saving_balance');
//        $savingBalanceSum=$Saving_amountSum-$Saving_balance;
        $last_log = DB::table('Loan_Log')->where('Loan_ID','=',$id)->orderBy('Loan_Log_ID', 'desc')->first();
        $savingBalanceSum=0.00;
        if ($last_log){
            $savingBalanceSum = $last_log->Saving_Account_Balance;
        }

        // Extracting installment IDs from installments
        $installmentIds = $installments->pluck('idInstallments');

        // Fetch the installment logs based on the extracted IDs
        $installment_logs = tableWithBranch('installment_log')
            ->whereIn('Installments_idInstallments', $installmentIds)
            ->get();


        $customer_payments = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->orderBy('Date', 'asc') // 'asc' for ascending, 'desc' for descending
            ->get();

        $total_paid_amount = $customer_payments->sum('Amount');

        // Fetch the customer
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();

        // Fetch the user
        $User = DB::table('user')->where('id', $loan->User_idUser)->first();
        $Lending_Officer = DB::table('user')->where('id', $loan->lending_officer_id)->first();


        // Fetch the loan category
        $Loan_Category = DB::table('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();

        // Fetch the customer bank
        $Customer_Bank = tableWithBranch('customer_has_bank')->where([
            ['id', $loan->cus_bank_account],
            ['cus_id', $loan->Customer_idCustomer]
        ])->first();

        $Other_Charges = tableWithBranch('loan_other_charges')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();


        $documents = tableWithBranch('documents')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();


        $customer_documents = tableWithBranch('customer_documents')->where([
            ['Customer_idCustomer', $loan->Customer_idCustomer]
        ])->get();




        // Fetch the witnesses
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            }
        }
        $user_id = (int)session('userid');

        $payment_delete=DB::table('user')->where('id','=',$user_id)->first();
        $payment_delete_status=0;
        if ($payment_delete){
            $payment_delete_status=(int)$payment_delete->payment_delete;
        }

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->leftJoin(DB::raw('(
        SELECT loan_id, COUNT(*) as approval_count, 
               SUM(CASE WHEN date = "-" THEN 1 ELSE 0 END) as pending_approvals 
        FROM loan_has_approval 
        GROUP BY loan_id
    ) as approval_subquery'), 'customer_loan.idCustomer_Loan', '=', 'approval_subquery.loan_id')
            ->leftJoin(DB::raw('(
        SELECT Customer_Loan_idCustomer_Loan, SUM(Amount) as total_other_charges 
        FROM loan_other_charges 
        GROUP BY Customer_Loan_idCustomer_Loan
    ) as charges_subquery'), 'customer_loan.idCustomer_Loan', '=', 'charges_subquery.Customer_Loan_idCustomer_Loan')
            ->where('idCustomer_Loan','=',$id)
            ->select(
                'customer_loan.idCustomer_Loan',
                DB::raw('IFNULL(approval_subquery.approval_count, 0) as approval_count'),
                DB::raw('IFNULL(approval_subquery.pending_approvals, 0) as pending_approvals'),
                DB::raw('IFNULL(charges_subquery.total_other_charges, 0) as total_other_charges')
            );

        $loans = $loanQuery->first();

        if ($loans->pending_approvals == '0' && str_contains(url()->previous(), 'pendingloan')) {
            return redirect('/pendingloan');
        }


        // Pass the data to the view with compact and handle potential nulls
        return view('pages.LoanView', compact(
            'type',
            'id',
            'customers',
            'loan',
            'installments',
            'company',
            'witnessDetails',
            'User',
            'Lending_Officer',
            'Loan_Category',
            'Other_Charges',
            'documents',
            'customer_documents',
            'installment_logs',
            'customer_payments',
            'Panalty_BalanceSum',
            'Panalty_Amount',
            'total_paid_amount',
            'Customer_Bank',
            'savingBalanceSum',
            'Saving_amountSum',
            'payment_delete_status'
        ));
    }






    public function loan_view_ajax(string $id)
    {
        $loan = DB::table('customer_loan')->where('branch_id', session('branch_id'))->where('idCustomer_Loan', $id)->first();
        if ($loan) {
            $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();


            $nextInstallment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $id)
                ->where('Installment_Date', '>', Carbon::now()->toDateString())
                ->where('Status', '=', "1")
                ->orderBy('Installment_Date', 'desc') // Order by date descending
                ->first(); // Get the first record in this order

            if (!$nextInstallment){
                $nextInstallment = tableWithBranch('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $id)
                    ->where('Installment_Date', '<', Carbon::now()->toDateString())
                    ->orderBy('Installment_Date', 'desc') // Order by date descending
                    ->first(); // Get the first record in this order
            }

            $nextInstallmentDate = Carbon::parse($nextInstallment->Installment_Date);

            $startDate = Carbon::parse($nextInstallment->Installment_Date);
            $endDate = Carbon::parse(date('Y-m-d'));
            $Paid_Date = $nextInstallment->Paid_Date;

            if ($startDate < $Paid_Date) {
                $startDate = Carbon::parse($nextInstallment->Paid_Date);
            }

            $daysCount = $startDate->diffInDays($endDate);

            $capital = $nextInstallment->capital_amount;
            $interest = $nextInstallment->interest_amount;
            $paid_amount = $nextInstallment->Paid_Amount;


            return response()->json(['paid_amount'=>$paid_amount,'loan' => $loan, 'Paid_Date' => $Paid_Date, 'installments' => $installments, 'success' => true, 'next_installment_date' => $nextInstallmentDate->format('Y-m-d'), 'days_from_last_payment_date' => $daysCount, 'capital' => $capital, 'interest' => $interest]);
        }
        return response()->json(['success' => false]);
    }

    public function repaymentreport()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');
        $collection = tableWithBranch('customer_payments','customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->get();


        $paymentsGroupedByDate = [];

        foreach ($collection as $item) {
            if (!isset($paymentsGroupedByDate[$item->Date])) {
                $paymentsGroupedByDate[$item->Date] = [];
            }

            if (!isset($paymentsGroupedByDate[$item->Date][$item->User_idUser])) {
                $paymentsGroupedByDate[$item->Date][$item->User_idUser] = [
                    'confirm_user' => $item->confirm_user,
                    'user_id' => $item->User_idUser,
                    'username' => $item->Full_Name,
                    'date' => $item->Date,
                    'total_amount' => 0
                ];
            }

            $paymentsGroupedByDate[$item->Date][$item->User_idUser]['total_amount'] += $item->Amount;
        }

        $userPayments = [];
        foreach ($paymentsGroupedByDate as $date => $users) {
            foreach ($users as $user) {
                $userPayments[] = $user;
            }
        }

        return view('pages.CollectionReport', compact('userPayments', 'dates', 'date_2'));
    }


    public function loadbank(string $id)
    {

        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $bank = tableWithBranch('customer_has_bank')
            ->where('cus_id', $loan->Customer_idCustomer)->get();
        return response()->json(['bank' => $bank,'bank_id' => $loan->cus_bank_account,'success' => true]);
    }


    public function reduce_capital(Request $request)
    {
        $id = $request->reduce_balance_loan_id;

        // Validate and process the data here
        // ...

        // Store data in session
        Session::put('reduce_capital_data', [
            'reduce_balance_loan_id' => $request->reduce_balance_loan_id,
            'pending_amount' => $request->pending_amount,
            'next_payment_date' => $request->next_payment_date,
            'days_from_last_payment_date' => $request->days_from_last_payment_date,
            'ins_capital' => $request->ins_capital,
            'installment_interest' => $request->installment_interest,
            'installment_interest_today' => $request->installment_interest_today,
            'required_payment_before_capital' => $request->required_payment_before_capital,
        ]);

        // Respond with JSON including the redirect URL
        return response()->json([
            'status' => 'success',
            'redirect_url' => route('loan.reduce_capital_view', ['id' => $id])
        ]);
    }

    public function reduce_capital_view($id)
    {
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $company= DB::table('company')->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Loan not found.');
        }

        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();

        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer
                ];
            }
        }

        $witnessCount = count($witnessDetails);
        $installments_log = tableWithBranch('installment_log','installment_log')
            ->join('installments', 'installment_log.Installments_idInstallments', '=', 'installments.idInstallments')
            ->where('installments.Customer_Loan_idCustomer_Loan', $id)
            ->get();

        // Retrieve data from session
        $reduce_capital_data = Session::get('reduce_capital_data');

        return view('pages.ReduceCapital', compact(
            'customers',
            'installments_log',
            'center',
            'product',
            'id',
            'loan',
            'installments',
            'witnessDetails',
            'witnessCount',
            'reduce_capital_data',
            'company'
        ));
    }

    public function invoice($id)
    {
        $company = DB::table('company')->first();
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();

        $loan_category = tableWithBranch('loan_category')->where('idLoan_Category', '=', $loan->Loan_Category_idLoan_Category)->first();

        $other_charges = tableWithBranch('other_charges')->where('Loan_Category_idLoan_Category', $loan_category->idLoan_Category)->get();
        $required_documents = tableWithBranch('required_documents')->where('Loan_Category_idLoan_Category', $loan_category->idLoan_Category)->get();
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessData = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address', 'Address_02', 'Address_03', 'Gender', 'Dob')
                    ->where('idGuardian', $item->cus_id)->first();

                $guarantorAddress = array_filter([
                    $guarantor->Address ?? null,
                    $guarantor->Address_2 ?? null,
                    $guarantor->Address_3 ?? null
                ]);

                $witnessData[] = [
                    'type' => 'Guarantor',
                    'First_Name' => $guarantor->First_Name,
                    'Last_Name' => $guarantor->Last_Name,
                    'Contact_No' => $guarantor->Contact_No,
                    'Nic' => $guarantor->Nic,
                    'Gender' => $guarantor->Gender,
                    'Dob' => $guarantor->Dob,
                    'Address' => !empty($guarantorAddress) ? implode(', ', $guarantorAddress) : '-'
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address', 'Address_02', 'Address_03', 'Gender', 'Dob')
                    ->where('idCustomer', $item->cus_id)->first();

                $customerAddress = array_filter([
                    $customer->Address ?? null,
                    $customer->Address_02 ?? null,
                    $customer->Address_03 ?? null
                ]);

                $witnessData[] = [
                    'type' => 'Customer',
                    'First_Name' => $customer->First_Name,
                    'Last_Name' => $customer->Last_Name,
                    'Contact_No' => $customer->Contact_No,
                    'Nic' => $customer->Nic,
                    'Dob' => $customer->Dob,
                    'Gender' => $customer->Gender,
                    'Address' => !empty($customerAddress) ? implode(', ', $customerAddress) : '-'
                ];
            }
        }


        return view('pages.Issueinvoice', compact('id', 'company', 'required_documents', 'loan', 'customers', 'other_charges', 'installments', 'witnessData'));
    }

    public function getInstallments($loan_id)
    {
        // Fetch the installments where the loan ID matches
        $installments = DB::table('installments')->where('branch_id', session('branch_id'))->where('Customer_Loan_idCustomer_Loan', $loan_id)->get();
        $loan = DB::table('customer_loan')->where('branch_id', session('branch_id'))->where('idCustomer_Loan', $loan_id)->first();
        $company = DB::table('company')->first();

        $Interest_period=$loan->Collection_Type;
        $saturday_sunday=$company->saturday_sunday;
        $panelty_date_count=$loan->Panalty_Date;

        // Return the installments as JSON response
        return response()->json([
            'installment' => $installments,
            'interest' => $Interest_period,
            'panelty_date_count' => $panelty_date_count,
            'saturday_sunday' => $saturday_sunday
        ]);

    }

    public function updateInstallments(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'loan_id' => 'required',
            'installments' => 'required',
        ]);

        // Loop through each installment and update in the database
        foreach ($request->installments as $installment) {
            DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $request->loan_id)
                ->where('No', $installment['no'])
                ->where('branch_id', session('branch_id'))
                ->update([
                    'Installment_Date' => $installment['installment_date'],
                    'Panelty_date' => $installment['penalty_date'],
                ]);
        }

        return response()->json(['message' => 'Installments updated successfully!']);
    }


    public function reschedule_index($loan_id, $balance)
    {
        $center = DB::table('center')->get();
        $product = DB::table('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = DB::table('user')->where('lending_officer', '=', '1')->get();
        $collector = DB::table('user')
            ->where('collector','=','1')
            ->get();
        $loan=DB::table('customer_loan')
            ->where('idCustomer_Loan','=',$loan_id)
            ->first();
        $product_id=$loan->Loan_Category_idLoan_Category;
        $loan_id=$loan->idCustomer_Loan;
        $customer = DB::table('customer')->where('idCustomer','=',$loan->Customer_idCustomer)->first();
        return view('pages.RescheduleIssueLoan', compact('customer','loan_id','product_id','loan', 'center', 'product', 'lending_officer','company','collector','loan_id','balance'));
    }


    public function save_reschedule(Request $request)
    {
        $loan_id=$request->loan_id;
        $reschedule_type=$request->reschedule_type;
        $date = Carbon::now()->toDateString();
        $user_id = (int)session('userid');



        if ($reschedule_type=='1'){
            $loan = Loan::find($loan_id);  // Find the loan by its ID
// Step 2: Prepare the data to insert into the `reschedule` table
            $rescheduleData = $loan->toArray(); // Convert the loan model to an array

            Reschedule::create($rescheduleData);  // Assuming the `reschedule` table allows mass assignment


            $loan->Loan_Category_idLoan_Category = $request->loan_cate_id;
            $loan->Customer_idCustomer = $request->customer_id;
            $loan->Leasing_type = $request->lease_type;
            $loan->Vehicle_No = $request->vehicle_num;
            $loan->Date_Time = $date;
            $loan->Amount = $request->loan_amount;
            $loan->Interest_Rate = $request->interest;
            $loan->Panalty_Rate = $request->panelty_amount;
            $loan->Interest_Amount = $request->interest_amount;
            $loan->Total_Other_Amount = $request->total_loan_charge;
            $loan->Other_Amount_Balance = $request->loan_charge_balance;
            $loan->Total_Loan_Amount = $request->total_loan_amount;
            $loan->Installment_Amount = $request->new_interest_amount;
            $loan->Collection_Type = $request->collection_type;
            $loan->Collection_Date = $request->installment_date_txt;
            $loan->Panalty_Date = $request->panelty_date;
            $loan->Balance_Amount = $request->total_loan_amount;
            $loan->User_idUser = $user_id;
            $loan->capital_balance = $request->total_capital_amount;
            $loan->installment_balance = $request->total_interest_amount;
            $loan->type = $request->interest_method;
            $loan->Interest_period = $request->Interest_period;
            $loan->repayment_duration = $request->repayment_duration_period;

            $loan->saving_amount = $request->saving_amount ?? '0.00';

// Save the updated loan
            $loan->save();

            if (!empty($request->installment)) {
                $firstInstallmentDate = $request->installment[0]['installmentDate'];
                $customerLoanId = $loan->idCustomer_Loan;

                DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $customerLoanId)
                    ->whereDate('Installment_Date', '>=', $firstInstallmentDate)
                    ->delete();

            }

            foreach ($request->installment as $item) {
                $customerLoanId = $loan->idCustomer_Loan;


                // Fetch the last installment and increment the 'No'
                $lastInstallment = DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $customerLoanId)
                    ->orderByRaw('CAST(No AS UNSIGNED) DESC') // Ensure the 'No' field is ordered numerically
                    ->first();

                // Check if the 'lastInstallment' exists and calculate the 'No' correctly
                $no = $lastInstallment ? (int)$lastInstallment->No + 1 : 1;
                $saving_check=$request->saving;

                if ($saving_check=="Yes"){
                    $customerLoanId = $loan_id;

                    $installmentDate = $item['installmentDate'];
                    $installmentAmount = $item['installmentAmount'];
                    $capitalAmount = $item['capitalAmount'];
                    $interestAmount = $item['interestAmount'];
                    $panaltyDate = $item['panaltyDate'];
                    $panaltyAmount = $item['panaltyAmount'];
                    $savingAmount = $item['savingAmount'];
                    $totalAmount = $item['totalAmount'];

                    $paidAmount = "0.00";
                    $panaltyBalance = $item['panaltyBalance'];
                    $savingBalance = $item['savingBalance'];
                    $totalBalance = $item['totalBalance'];


                    DB::table('installments')->insert([
                        'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                        'No' => $no,
                        'Installment_Date' => $installmentDate,
                        'Installment_Amount' => $installmentAmount,
                        'capital_amount' => $capitalAmount,
                        'interest_amount' => $interestAmount,
                        'Panalty_Amount' => $panaltyAmount,
                        'Saving_amount' => $savingAmount,
                        'Total_Amount' => $totalAmount,
                        'Paid_Amount' => $paidAmount,
                        'Panalty_Balance' => $panaltyBalance,
                        'Interest_Balance' => $interestAmount,
                        'capital_balance' => $capitalAmount,
                        'Total_Balance' => $totalBalance,
                        'Saving_balance' => $savingBalance,
                        'Status' => '0',
                        'Panelty_date' => $panaltyDate,
                        'Panelty_status' => '0',
                        'branch_id' => session('branch_id')
                    ]);
                }else{
                    $customerLoanId = $loan_id;

                    $installmentDate = $item['installmentDate'];
                    $installmentAmount = $item['installmentAmount'];
                    $capitalAmount = $item['capitalAmount'];
                    $interestAmount = $item['interestAmount'];
                    $panaltyDate = $item['panaltyDate'];
                    $panaltyAmount = $item['panaltyAmount'];
                    $totalAmount = $item['totalAmount'];
                    $paidAmount = "0.00";
                    $panaltyBalance = $item['panaltyBalance'];
                    $totalBalance = $item['totalBalance'];


                    DB::table('installments')->insert([
                        'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                        'No' => $no,
                        'Installment_Date' => $installmentDate,
                        'Installment_Amount' => $installmentAmount,
                        'capital_amount' => $capitalAmount,
                        'interest_amount' => $interestAmount,
                        'Panalty_Amount' => $panaltyAmount,
                        'Total_Amount' => $totalAmount,
                        'Paid_Amount' => $paidAmount,
                        'Panalty_Balance' => $panaltyBalance,
                        'Interest_Balance' => $interestAmount,
                        'capital_balance' => $capitalAmount,
                        'Total_Balance' => $totalBalance,
                        'Status' => '0',
                        'Panelty_date' => $panaltyDate,
                        'Panelty_status' => '0',
                        'branch_id' => session('branch_id')
                    ]);
                }
            }

        }else{

        }

        return response()->json([ 'installment' => $request->installment], 200);

    }

    public function getCustomerBankDetails(Request $request)
    {
        $customerIds = $request->input('customer_ids', []);
        $results = DB::table('customer_has_bank')
            ->join('customer','customer_has_bank.cus_id','=','customer.idCustomer')
            ->whereIn('customer.idCustomer', $customerIds)
            ->select('customer.idCustomer','customer_has_bank.bank_name','customer_has_bank.account_number')
            ->get();

        $data = [];
        foreach ($results as $row) {
            $data[$row->idCustomer] = $row->bank_name . ' - ' . $row->account_number;
        }

        return response()->json($data);
    }






}
