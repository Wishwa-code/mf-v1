<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
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
        $loanCounts = DB::table('customer_loan')
            ->select(
                'Customer_idCustomer',
                DB::raw('SUM(CASE WHEN Status = 0 THEN 1 ELSE 0 END) AS current_loan_count'),
                DB::raw('SUM(CASE WHEN Status = 1 THEN 1 ELSE 0 END) AS settled_loan_count')
            )
            ->groupBy('Customer_idCustomer');

        $customers = tableWithBranch('customer', 'customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoinSub($loanCounts, 'loan_data', function ($join) {
                $join->on('customer.idCustomer', '=', 'loan_data.Customer_idCustomer');
            })
            ->select(
                'customer.*',
                'customer_group.Name as group_name',
                'center.Name as center_name',
                DB::raw('IFNULL(loan_data.current_loan_count, 0) AS current_loan_count'),
                DB::raw('IFNULL(loan_data.settled_loan_count, 0) AS settled_loan_count')
            )
            ->get();
        $group = tableWithBranch('customer_group')->get();
        $center = tableWithBranch('center')->get();
        return view('pages.CustomerReport', compact('customers','group','center'));
    }

    public function recover_officer_wise_index(Request $request)
    {
        // Check if the 'recovery' parameter exists in the request and is not empty
        $officer = $request->has('recovery') && !empty($request->recovery) ? $request->recovery : '0';

        // Retrieve the recovery officers
        $recovery_officer = tableWithBranch('user')->where('collector', '=', '1')->get();

        // Build the query to fetch customers
        $query = tableWithBranch('customer', 'customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('customer_loan', 'customer.idCustomer', '=', 'customer_loan.idCustomer_Loan')
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name');

        // If officer is not '0', filter the query by the officer's center ID
        if ($officer != '0') {
            $query->where('center.idCenter', '=', $officer);
        }

        // Execute the query and get customers
        $customers = $query->get();
        $selectedRecoveryOfficer=$officer;

        // Return the view with the customers and recovery officers
        return view('pages.RecoverOfficerCustomerReport', compact('customers','selectedRecoveryOfficer' ,'recovery_officer'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_id = (int)session('userid');

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $bank = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->get();
        if ($collector_val){
            $collector = $collector_val->collector;
            $cashier = $collector_val->cashier;

            if($collector==1 || $cashier==1){
                $bank = tableWithBranch('company_bank_accounts')->where('Account_No','=',$user_id)->get();
            }

        }

        $expences_category = tableWithBranch('company_bank_accounts')->where('acc_type_group','=','Expenses')->where('Bank_Type','=','ChartOfAccount')->get();
        
        $branches = [];
        if (session('branch_id') == -1) {
            $branches = DB::table('branch')->where('status', '=', '1')->get();
        }
        
        return view('pages.CreateExpenses',compact('bank','expences_category','branches'));
    }

    public function getBranchExpenseData(Request $request)
    {
        $branchId = $request->branch_id;
        
        $banks = DB::table('company_bank_accounts')
            ->where('branch_id', '=', $branchId)
            ->where('Bank_Type', '=', 'Bank')
            ->get();
            
        $categories = DB::table('company_bank_accounts')
            ->where('branch_id', '=', $branchId)
            ->where('acc_type_group', '=', 'Expenses')
            ->where('Bank_Type', '=', 'ChartOfAccount')
            ->get();
            
        return response()->json([
            'banks' => $banks,
            'categories' => $categories
        ]);
    }

    public function income()
    {
        $bank = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->get();
        $expences_category = tableWithBranch('income_category')->get();
        return view('pages.CreateIncome',compact('bank','expences_category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id=DB::table('expences_category')->insertGetId([
            'description'=>$request->description,
            'branch_id' => session('branch_id')
        ]);
        $user_id = (int)session('userid');
        $Bank = [
            'Bank_Type' => "ChartOfAccount",
            'acc_type_group' => "Expenses",
            'code' => $id,
            'Bank_Name' => $request->description,
            'Account_Name' => $request->description,
            'Account_No' => $request->description,
            'Bank_Branch' => $request->description,
            'Account_Balance' => '0.00',
            'type' => "Financial Expenses",
            'cashflow' => "Non Applicable",
            'User' => $user_id,
        ];
        insertWithBranch('company_bank_accounts', $Bank);
        return response()->json(['message' => 'Data saved successfully'], 200);
    }


    public function income_store(Request $request)
    {
        $id=DB::table('income_category')->insertGetId([
            'description'=>$request->description,
            'branch_id' => session('branch_id')
        ]);
        $user_id = (int)session('userid');
        $Bank = [
            'Bank_Type' => "Income",
            'code' => $id,
            'Bank_Name' => $request->description,
            'Account_Name' => $request->description,
            'Account_No' => $request->description,
            'Bank_Branch' => $request->description,
            'Account_Balance' => '0.00',
            'type' => "Financial Income",
            'cashflow' => "Non Applicable",
            'User' => $user_id,
        ];
        insertWithBranch('company_bank_accounts', $Bank);
        return response()->json(['message' => 'Data saved successfully'], 200);
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
    public function edit()
    {
        $loan = tableWithBranch('customer_loan','customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->get();
        return view('pages.LoanReport', compact('loan'));
    }

    public function loansummary(Request $request)
    {
        // Fetch the list of centers
        $branch_access = session('branch_access');

        if ($branch_access == 1) {
            $branch = DB::table('branch')->where('status', '=', '1')->get();
        } else {
            $branch = DB::table('branch')
                ->where('status', '=', '1')
                ->where('branch_id', session('branch_id'))
                ->get();
        }


        $centers = DB::table('center')->where('branch_id', session('branch_id'))->get();

        // Fetch the list of groups
        $groups = DB::table('customer_group')->where('branch_id', session('branch_id'))->select('Group_No as group_name')->distinct()->get();

        // Initialize the query for fetching loans
        $query = DB::table('customer_loan')
            ->join('branch', 'customer_loan.branch_id', '=', 'branch.branch_id')
            ->join('user', 'customer_loan.lending_officer_id', '=', 'user.id')
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
                DB::raw('IFNULL(center.Name, "-") as center_name'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                'customer.First_Name as First_Name',
                'customer.cus_number as cus_number',
                'customer.Last_Name as Last_Name',
                'customer.Contact_No as Contact_No',
                'customer.Nic as Nic',
                'branch.Name as branch_name',
                'user.Full_Name as LendingOfficer',
                'loan_category.Name as Name')
            ->where('customer_loan.Status', '!=', '-1')
            ->where('customer_loan.Status', '!=', '-2');

        // Apply center filter if center_id is provided
        if ($request->has('center_id') && $request->center_id != '') {
            $query->where('center.idCenter', $request->center_id);
        }

        // Apply group filter if group_name is provided
        if ($request->has('group_name') && $request->group_name != '') {
            $query->where('subquery.group_name', $request->group_name);
        }

        // Apply group filter if group_name is provided
        if ($request->has('loan_status') && $request->loan_status != '') {
            $query->where('customer_loan.Status','!=', $request->loan_status);
        }


        if ($branch_access == 1) {
            // Apply group filter if group_name is provided
            if ($request->has('branch') && $request->branch != '') {
                $query->where('customer_loan.branch_id', $request->branch);
            }
        }else{
            $query->where('customer_loan.branch_id', session('branch_id'));
        }




        // Execute the query and get the loan data
        $loan = $query->get();

        // Pass loan, centers, and groups data to the view
        return view('pages.LoanSummaryDetails', compact('loan', 'centers', 'groups','branch'));
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
        DB::table('expences_category')
            ->where('id','=', $id)
            ->where('branch_id', session('branch_id'))
            ->delete();
        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    public function income_destroy(string $id)
    {
        DB::table('income_category')
            ->where('id','=', $id)
            ->where('branch_id', session('branch_id'))
            ->delete();
        return response()->json(['message' => 'Data saved successfully'], 200);
    }


    public function saveexpenses(Request $request){

        $user_id = (int)session('userid');

        $type=$request->type;
        $reason=$request->reason;
        $date=$request->date;
        $amount=$request->amount;

        Log::info($request->bank);

        $expenses=new Expenses();

        $expenses->type=$type;
        $expenses->reason=$reason;
        $expenses->date=$date;
        $expenses->amount=$amount;
        $expenses->category_id=$request->category;
        $expenses->bank_id=$request->bank;
        $expenses->user_id = $user_id;
        $expenses->branch_id = $request->has('branch_id') && $request->branch_id ? $request->branch_id : session('branch_id');

        if ($expenses->save()) {

            if ($type=="Expense") {
                $bank_id=DB::table('company_bank_accounts')
                    ->where('acc_type_group','=','Expenses')
                    ->where('Idbank','=',$request->category)
                    ->first();
                if ($bank_id) {
                    $this->bankLogController->index($request->bank,"Expenses",$reason,"-","credit",$amount,$bank_id->Idbank);
                    $this->bankLogController->index($bank_id->Idbank,"Expenses",$reason,"-","debit",$amount,$request->bank);
                }
            }else{
                $bank_id=DB::table('company_bank_accounts')
                    ->where('acc_type_group','=','Income')
                    ->where('Idbank','=',$request->category)
                    ->first();
                if ($bank_id) {
                    $this->bankLogController->index($request->bank,"Income",$reason,"-","debit",$amount,$bank_id->Idbank);
                    $this->bankLogController->index($bank_id->Idbank,"Income",$reason,"-","credit",$amount,$request->bank);
                }
            }


            // If the data is saved successfully, return a success response
            return response()->json(['message' => 'Data saved successfully'], 200);
        } else {
            // If the data failed to save, return an error response
            return response()->json(['message' => 'Failed to save data'], 500);
        }


    }

    public function viewexpenses(){
        // Show only Expense type + show null categorries too
        $expenses = tableWithBranch('expences','expences')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'expences.category_id')
            ->where('expences.type', 'Expense')
            ->select('expences.*', 'company_bank_accounts.Bank_Name')
            ->get();
        return view('pages.ViewExpenses', compact('expenses'));
    }

    public function viewincome(){
        $expenses = tableWithBranch('expences','expences')
            ->join('income_category', 'income_category.id', '=', 'expences.category_id')
            ->where('type', 'Income')
            ->select('expences.*','income_category.description as description')
            ->get();
        return view('pages.ViewIncome', compact('expenses'));
    }

    public function deleteexpenses(string $id){


        $last_expenses=tableWithBranch('expences')->where('id','=',$id)->first();

        if ($last_expenses){
            $bank_id=tableWithBranch('company_bank_accounts')
                ->where('acc_type_group','=','Expenses')
                ->where('Idbank','=',$last_expenses->category_id)
                ->first();
            
            // Get bank name for description
            $bankName = $bank_id ? $bank_id->Bank_Name : 'Unknown';
            
            // Store expense delete data for approval
            $requestData = [
                'expense_id' => $id,
                'expense_data' => (array)$last_expenses,
                'bank_id_data' => $bank_id ? (array)$bank_id : null,
            ];

            // Create approval request
            DB::table('approval_request')->insert([
                'type' => 'Expense Delete',
                'typeid' => 603,
                'description' => 'Expense Delete: ' . $last_expenses->reason . ' (Amount: ' . $last_expenses->amount . ', Category: ' . $bankName . ')',
                'data' => json_encode($requestData),
                'userid' => session('userid'),
                'branch_id' => session('branch_id'),
                'data_time' => now(),
                'status' => 0
            ]);
            
            // Redirect with success message
            return redirect()->back()->with('success', 'Expense delete request sent for approval!');
            
            // OLD CODE - keeping for approval handler reference
            /*
            $reason='Delete Expense : ('.$last_expenses->reason.')';
            $this->bankLogController->index($last_expenses->bank_id,"Expenses",$reason,"-","debit",$last_expenses->amount,$bank_id->Idbank);
            $this->bankLogController->index($bank_id->Idbank,"Expenses",$reason,"-","credit",$last_expenses->amount,$last_expenses->bank_id);

            DB::table('expences')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->delete();
            */
        }


        // Reload the list with the same logic as viewexpenses()
        $expenses = DB::table('expences')
            ->join('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'expences.category_id')
            ->where('company_bank_accounts.acc_type_group', 'Expenses')
            ->where('expences.type', 'Expense')
            ->where('expences.branch_id', session('branch_id'))
            ->select('expences.*', 'company_bank_accounts.Bank_Name')
            ->get();
        return view('pages.ViewExpenses', compact('expenses'));
    }

    public function deleteincome(string $id){
        DB::table('expences')
            ->where('id', $id)
            ->where('branch_id', session('branch_id'))
            ->delete();
        $expenses = tableWithBranch('expences','expences')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'expences.category_id')
            ->where('expences.type', 'Expense')
            ->select('expences.*', 'company_bank_accounts.Bank_Name')
            ->get();
        return view('pages.ViewIncome', compact('expenses'));
    }
    public function showFullLoanDetailReport(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $collector_id = $request->input('collector_id');
        $loan_status = $request->input('loan_status');
        $route_id = $request->input('route_id');

        $collector = tableWithBranch('user')->where('collector','=','1')->get();
        $route = tableWithBranch('route')->get();

        // Initialize the query
        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('loan_category', 'loan_category.idLoan_Category', '=', 'customer_loan.Loan_Category_idLoan_Category')
            ->join('user as u1', 'u1.id', '=', 'customer_loan.lending_officer_id')
            ->join('user as u2', 'u2.id', '=', 'customer_loan.collector_id')
            ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
            ->join('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin(DB::raw('(SELECT Customer_Loan_idCustomer_Loan, SUM(Amount) as total_paid_amount FROM customer_payments GROUP BY Customer_Loan_idCustomer_Loan) as payments'), 'payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->leftJoin(DB::raw('(SELECT Customer_Loan_idCustomer_Loan, MAX(Installment_Date) as Loan_Maturity_Date FROM installments GROUP BY Customer_Loan_idCustomer_Loan) as maturity'), 'maturity.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                'customer_loan.*',
                'loan_category.Name as Product_Name',
                'u1.Full_Name as lending_officer_name',
                'u2.Full_Name as collector_officer_name',
                'customer.idCustomer',
                'customer.Customer_Group_idCustomer_Group',
                'customer.cus_number as Customer_No',
                'customer.Title',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Email',
                'customer.Contact_No',
                'customer.contact_number_2',
                'customer.Nic',
                'customer.Gender',
                'customer.Dob',
                'customer.Customer_Risk_Level',
                'customer.Address',
                'customer.Address_02',
                'customer.Address_03',
                'customer.Per_Address_01',
                'customer.Per_Address_02',
                'customer.Per_Address_03',
                'customer.City',
                'customer.State',
                'customer.Landline',
                'customer.Note',
                'customer.Longitude',
                'customer.Latitude',
                'customer.Gua_title',
                'customer.Gua_name',
                'customer.Guardian_gender',
                'customer.Gua_relation',
                'customer.Gua_occu',
                'customer.Gua_contact',
                'customer.Gua_address',
                'customer.Gua_nic',
                'customer.Cus_phto',
                'customer.Status as Customer_Status',
                'customer.civil_status',
                'customer.occu_job_position',
                'customer.occu_monthly_salary',
                'customer.occu_address_01',
                'customer.occu_address_02',
                'customer.occu_address_03',
                'customer.occu_contact_no',
                'customer.occu_longitude',
                'customer.occu_latitude',
                DB::raw('IFNULL(payments.total_paid_amount, 0) as total_paid_amount'), // Use IFNULL to default to 0
                'maturity.Loan_Maturity_Date'
            );

        // Apply date range filtering if dates are provided
        if ($dateFrom) {
            $loanQuery->where('customer_loan.Date_Time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $loanQuery->where('customer_loan.Date_Time', '<=', $dateTo);
        }

        if ($collector_id) {
            if ($collector_id=="0") {
            }else{
                $loanQuery->where('customer_loan.collector_id', '=', $collector_id);
            }
        }

        // Loan status filtering
        if (!empty($loan_status)) {
            $loanQuery->whereIn('customer_loan.Status', $loan_status);
        }
        if ($route_id!="0") {
            $loanQuery->where('route.id_route', '=', $route_id);
        }

        $loan = $loanQuery->get();
        $totalAmount = $loanQuery->sum('customer_loan.Amount');
        $totalLoanAmount = $loanQuery->sum('customer_loan.Total_Loan_Amount');
        // Fetch guarantor details
        $guarantee = tableWithBranch('witness', 'witness')
            ->join('customer', 'customer.idCustomer', '=', 'witness.cus_id')
            ->select(
                'witness.Customer_Loan_idCustomer_Loan',
                'customer.*'
            )
            ->where('witness.type', 'Cross Customer')
            ->get()
            ->groupBy('Customer_Loan_idCustomer_Loan');

        // Merge guarantor details into each loan entry
        foreach ($loan as $l) {
            $l->guarantors = isset($guarantee[$l->idCustomer_Loan]) ? $guarantee[$l->idCustomer_Loan] : [];
        }

        return view('pages.AllLoanDetailReport', compact('totalLoanAmount','loan','totalAmount','route_id','route','collector','collector_id','loan_status'));
    }

    public function LoanChargers(Request $request){
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $loanQuery = tableWithBranch('loan_other_charges','loan_other_charges')
        ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'loan_other_charges.Customer_Loan_idCustomer_Loan')
            ->select('loan_other_charges.*','customer_loan.Loan_No','customer_loan.Date_Time as Date_Time')
            ->where('customer_loan.Status', '=', '0');

        // Apply date range filtering if dates are provided
        if ($dateFrom) {
            $loanQuery->where('customer_loan.Date_Time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $loanQuery->where('customer_loan.Date_Time', '<=', $dateTo);
        }
        $loan = $loanQuery->get();
        return view('pages.OtherChargersReport', compact('loan'));
    }



    public function dandlreport(Request $request)
    {
        $center_id = $request->input('center_id');
        $dateFrom = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->toDateString());

        // Subquery for aggregating Doc_Amount separately
        $chargesSubQuery = DB::table('loan_other_charges')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'loan_other_charges.Customer_Loan_idCustomer_Loan')
            ->select(
                'customer_loan.idCustomer_Loan',
                DB::raw("SUM(CASE WHEN customer_loan.Date_Time BETWEEN '$dateFrom' AND '$dateTo' THEN loan_other_charges.Amount ELSE 0 END) as Doc_Amount")
            )
            ->where('customer_loan.Status', '=', '0') // Add Status filter here
            ->where('loan_other_charges.branch_id', session('branch_id'))
            ->groupBy('customer_loan.idCustomer_Loan');

        // Main subquery for installments with join to chargesSubQuery
        $installmentsSubQuery = DB::table('installments')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoinSub($chargesSubQuery, 'charges', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'charges.idCustomer_Loan');
            })
            ->select(
                'installments.Installment_Date',
                'center.idCenter',
                'center.Name as Center_name',
                DB::raw('SUM(installments.Total_Amount) as Total_Amount'),
                DB::raw('SUM(installments.Paid_Amount) as Paid_Amount'),
                DB::raw('SUM(COALESCE(charges.Doc_Amount, 0)) as Doc_Amount'), // Summing Doc_Amount
                DB::raw('SUM(installments.Total_Balance) as Total_Balance')
            )
            ->where('customer_loan.Status', '=', '0') // Add Status filter here
            ->where('installments.branch_id', session('branch_id'))
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->where('installments.Installment_Date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->where('installments.Installment_Date', '<=', $dateTo);
            })
            ->groupBy('installments.Installment_Date', 'center.idCenter','center.Name');

        // Main query filtering by center_id if provided
        $loanQuery = DB::table(DB::raw("({$installmentsSubQuery->toSql()}) as sub"))
            ->mergeBindings($installmentsSubQuery)
            ->select('sub.*');

        if ($center_id != 0) {
            $loanQuery->where('sub.idCenter', '=', $center_id);
        }

        $loan = $loanQuery->get();
        $center = tableWithBranch('center')->get();

        return view('pages.DandLReport', compact('loan', 'center', 'center_id'));
    }

    public function monthlyprofit(Request $request)
    {
        $center_id = $request->input('center_id');
        $dateFrom = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->toDateString());

        // Main subquery for installments with join to chargesSubQuery
        $installmentsSubQuery = DB::table('installments')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                             FROM group_has_customer
                             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_loan.Loan_No as Loan_No',
                'customer.cus_number as cus_number',
                'center.idCenter',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('SUM(installments.capital_amount) as capital_amount'),
                DB::raw('SUM(installments.interest_amount) as interest_amount'),
                DB::raw('SUM(installments.Paid_Amount) as Paid_Amount'),
                DB::raw('SUM(installments.Total_Balance) as Total_Balance')
            )
            ->whereNotIn('customer_loan.Status', ['-1', '-2'])
            ->where('installments.branch_id', session('branch_id'))
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->where('installments.Installment_Date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->where('installments.Installment_Date', '<=', $dateTo);
            })
            ->groupBy(
                'customer_loan.idCustomer_Loan',
                'center.idCenter',
                'customer_loan.Loan_No',
                'customer.cus_number',
                DB::raw('IFNULL(center.No, "-")'),
                DB::raw('IFNULL(subquery.group_name, "-")')
            );

        // Main query filtering by center_id if provided
        $loanQuery = DB::table(DB::raw("({$installmentsSubQuery->toSql()}) as sub"))
            ->mergeBindings($installmentsSubQuery)
            ->select('sub.*');

        if ($center_id != 0) {
            $loanQuery->where('sub.idCenter', '=', $center_id);
        }

        $loan = $loanQuery->get();
        $center = DB::table('center')->get();

        return view('pages.MonthlyProfit', compact('loan', 'center', 'center_id'));
    }




    public function storeComment(Request $request){
        $user_id = (int)session('userid');
        // Insert the comment into the loan_comment table
        $comment_id=DB::table('loan_comment')->insertGetId([
            'comment' => $request->comment,
            'loan_id' => $request->loan_id,
            'user_id' => $user_id,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]);
        $loan = DB::table('customer_loan')->where('installments.branch_id', session('branch_id'))->where('idCustomer_Loan', $request->loan_id)->first();
        $request = new Request([
            'customer_id' => $loan->idCustomer_Loan,
            'description' => 'Loan Comment for '.$request->f_name.' '.$request->last_name,
            'description_id' => $comment_id,
            'comment' =>$request->comment,
            'type' => 'Loan Comment',
        ]);

        // Call the store method of CustomerLogController
        $this->customerLogController->store($request);

        return response()->json(['message' => 'Documents saved successfully']);
    }

    public function fetchComments(Request $request)
    {
        // Fetch comments from the loan_comment table
        $comments = DB::table('loan_comment')
            ->where('loan_id', $request->loan_id)
            ->select('comment', 'date', 'time')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // Return the comments as JSON response
        return response()->json([
            'comments' => $comments
        ]);
    }

    public function gl_report(){
        return view('pages.GlReport');
    }


    public function savings_report()
    {
        $recovery_officer = tableWithBranch('user')->where('collector', '=', '1')->get();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $group = DB::table('customer_group')->where('branch_id', session('branch_id'))->get();
        $customers = DB::table('customer')->where('branch_id', session('branch_id'))->get();
        $center = DB::table('center')->where('branch_id', session('branch_id'))->get();
        $route = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $branch = DB::table('branch')->where('status','=','1')->get(); // Add this line
        return view('pages.SavingReport', compact('group', 'lending_officer', 'recovery_officer', 'center', 'customers', 'route','branch'));
    }

    public function getBranchRelatedData(Request $request)
    {
        $branch_id = $request->branch_id;

        $center = DB::table('center')->where('branch_id', $branch_id)->get();
        $group = DB::table('customer_group')->where('branch_id', $branch_id)->get();
        $customers = DB::table('customer')->where('branch_id', $branch_id)->get();
        $lending_officer = DB::table('user')->where('branch_id', $branch_id)->where('lending_officer', '=', '1')->get();
        $route = DB::table('route')
            ->where('route.branch_id', $branch_id)
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();

        return response()->json([
            'center' => $center,
            'group' => $group,
            'customers' => $customers,
            'lending_officer' => $lending_officer,
            'route' => $route
        ]);
    }



    public function savings_report_filter(Request $request)
    {
        $center_details = $request->center_details;
        $route = $request->route;
        $customer = $request->customer;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $branch = $request->branch ?? '0';
        $lending_officer = $request->has('lending') && !empty($request->lending) ? $request->lending : '0';

        $loanQuery = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                        FROM group_has_customer
                        LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->leftJoin('Customer_Saving_Accounts', function ($join) {
                $join->on('Customer_Saving_Accounts.Customer_Id', '=', 'customer.idCustomer')
                    ->on('Customer_Saving_Accounts.Loan_Id', '=', 'customer_loan.idCustomer_Loan')
                    ->where('Customer_Saving_Accounts.Status', '=', 1);
            })
            ->leftJoin('Savings_Account_Log', function ($join) use ($date_from, $date_to) {
                $join->on('Savings_Account_Log.Saving_Acount_Id', '=', 'Customer_Saving_Accounts.id');

                if (!empty($date_from) && !empty($date_to)) {
                    $startDateTime = $date_from . ' 00:00:00';
                    $endDateTime = $date_to . ' 23:59:59';

                    $join->whereBetween('Savings_Account_Log.Date_Time', [$startDateTime, $endDateTime]);
                }
            })
            ->select(
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(center.Name, "-") as center_name'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                'customer.Nic as member_nic',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as member_name'),
                DB::raw('SUM(Savings_Account_Log.Credit) as saving_amount')
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('customer_loan.branch_id', session('branch_id'))
            ->orderBy('center_no');

        // Filters
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }
        if ($route != '0') {
            $loanQuery->where('route.id_route', '=', $route);
        }
        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }
        if ($lending_officer != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }
        if ($branch != '0') {
            $loanQuery->where('customer_loan.branch_id', '=', $branch);
        }

        $loanQuery->groupBy(
            'center.No',
            'center.Name',
            'subquery.group_name',
            'customer.Nic',
            'customer.First_Name',
            'customer.Last_Name',
            'customer.idCustomer',
        );

        // Only show rows with savings
        $loanQuery->havingRaw('SUM(Savings_Account_Log.Credit) > 0');

        $result = $loanQuery->get();

        return response()->json(['item' => $result, 'message' => 'all'], 200);
    }




    public function payment_report(Request $request){

        // Get filter values from request
        $branchId = $request->input('branch_id');
        $routeId = $request->input('route_id');
        $centerId = $request->input('center_id');
        $collectorId = $request->input('collector_id');
        $loanProductId = $request->input('loan_product_id');
        $paidType = $request->input('paid_type', 'All');
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $payments = DB::table('customer_loan as l')
            ->leftJoin('installments as i', 'i.Customer_Loan_idCustomer_Loan', '=', 'l.idCustomer_Loan') // Ensure all loans appear
            ->leftJoin('customer_payments as p', 'l.idCustomer_Loan', '=', 'p.Customer_Loan_idCustomer_Loan') // Left join payments
            ->join('customer as cust', 'l.Customer_idCustomer', '=', 'cust.idCustomer')
            ->join('loan_category as lp', 'l.Loan_Category_idLoan_Category', '=', 'lp.idLoan_Category')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
        FROM group_has_customer
        LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'cust.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'cust.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->leftJoin('branch', 'route.branch_id', '=', 'branch.branch_id')
            ->leftJoin('user as u', 'l.collector_id', '=', 'u.id') // Ensure loans appear even without payments
            ->select([
                'branch.name as Branch',
                'l.branch_id',
                'l.Status',
                'route.name as Route',
                'center.name as Center',
                'l.loan_no as LoanNo',
                'l.idCustomer_Loan as idCustomer_Loan',
                'l.Balance_Amount as Balance_Amount',
                DB::raw('IFNULL(subquery.group_name, "-") as GroupName'),
                DB::raw('CONCAT(cust.First_Name, " ", cust.Last_Name) as CustomerName'),

                // Overdue Days Calculation (Ensures loans without payments appear)
                DB::raw('IFNULL((SELECT DATEDIFF("'.$endDate.'", MIN(i2.Installment_Date)) 
          FROM installments i2 
          WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan 
            AND i2.Installment_Date BETWEEN "'.$startDate.'" AND "'.$endDate.'"), 0) AS OverdueDays'),

                // Total Overdue Calculation
                DB::raw('IFNULL((SELECT DATEDIFF("'.$endDate.'", MIN(i2.Installment_Date)) 
          FROM installments i2 
          WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan 
            AND i2.Installment_Date BETWEEN "'.$startDate.'" AND "'.$endDate.'") * l.installment_amount, 0) AS TotalOverdue'),

                'lp.name as LoanProduct',
                'l.Amount as LoanAmount',
                'l.installment_amount as InstallmentAmount',

                // Sum Installment Amount Within Date Range
                DB::raw('IFNULL((SELECT SUM(i2.installment_amount) 
          FROM installments i2 
          WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan 
            AND DATE(i2.Installment_Date) BETWEEN "'.$startDate.'" AND "'.$endDate.'"), 0) AS TotalInstallmentAmount'),

                // Sum Penalty Amount Within Date Range
                DB::raw('IFNULL((SELECT SUM(i2.Panalty_Amount) 
          FROM installments i2 
          WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan 
            AND DATE(i2.Installment_Date) BETWEEN "'.$startDate.'" AND "'.$endDate.'"), 0) AS TotalPenaltyAmount'),

//                // Sum Paid Amount Within Date Range
                DB::raw('(SELECT SUM(i2.Amount)
          FROM customer_payments i2
            WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan AND  DATE(i2.Date) >= "'.$startDate.'"
            AND DATE(i2.Date) <= "'.$endDate.'") AS TotalRealPaidAmount'),





                DB::raw('IFNULL((SELECT SUM(i2.Paid_Amount) 
          FROM installments i2 
          WHERE i2.Customer_Loan_idCustomer_Loan = l.idCustomer_Loan 
            AND DATE(i2.Installment_Date) BETWEEN "'.$startDate.'" AND "'.$endDate.'"), 0) AS TotalPaidAmount'),

                DB::raw('IFNULL(u.Full_Name, "-") as Collector') // Ensures empty collectors don't cause issues
            ])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereDate('i.Installment_Date', '>=', $startDate)
                    ->whereDate('i.Installment_Date', '<=', $endDate)
                    ->orWhereNull('i.Installment_Date');
            });


// Apply filters only when values are not '0'
        if ($branchId != '0') {
            $payments->where('l.branch_id', '=', $branchId);
        }
        if ($routeId != '0') {
            $payments->where('route.id_route', '=', $routeId);
        }
        if ($centerId != '0') {
            $payments->where('center.idCenter', '=', $centerId);
        }
        if ($collectorId != '0') {
            $payments->where('u.id', '=', $collectorId);
        }
        if ($loanProductId != '0') {
            $payments->where('lp.idLoan_Category', '=', $loanProductId);
        }
        $payments->whereIn('l.Status', [0, 1]);
// Group by necessary fields
        $payments->groupBy(
            'l.idCustomer_Loan','l.branch_id',
            'l.Amount', 'l.installment_amount',
            'branch.name', 'route.name', 'center.name',
            'l.loan_no', 'subquery.group_name',
            'cust.First_Name', 'cust.Last_Name',
            'lp.name', 'u.Full_Name', 'l.Balance_Amount','l.Status'
        );

// Execute query first
        $payments = $payments->get();

// Apply the paid type filter AFTER fetching the data
        if ($paidType != 'All') {
            $payments = $payments->filter(function ($payment) use ($paidType) {
                $totalPayable = $payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount;
                $totalPaid = $payment->TotalPaidAmount;

                if ($paidType == 'Under Paid' && $totalPayable > $totalPaid && $totalPaid != 0) {
                    return true;
                } elseif ($paidType == 'Over Paid' && $totalPayable < $totalPaid) {
                    return true;
                } elseif ($paidType == 'Not Paid' && $totalPaid == 0) {
                    return true;
                } elseif ($paidType == 'Normal' && $totalPayable == $totalPaid) {
                    return true;
                }
                return false;
            });
        }

// Fetch dropdown data
        $branch_access = session('branch_access');

        if ($branch_access == 1) {
            // User can access all branches
            $branches = DB::table('branch')->where('status', '=', '1')->get();
        } else {
            // User can only access their own branch
            $branches = DB::table('branch')
                ->where('status', '=', '1')
                ->where('branch_id', session('branch_id'))
                ->get();
        }

        $routes = tableWithBranch('route')->get();
        $centers = tableWithBranch('center')->get();
        $collectors = tableWithBranch('user')->where('collector', '=', '1')->get();
        $loanProducts = tableWithBranch('loan_category')->get();

        return view('pages.PaymentFullDetailsReport', compact(
            'loanProducts', 'payments', 'collectors', 'branches', 'routes', 'centers', 'branch_access'
        ));

    }


    public function getCentersGroups(Request $request)
    {
        $branchId = $request->branch_id;

        $centers = DB::table('center')
            ->where('branch_id', $branchId)
            ->select('idCenter', 'No', 'Name')
            ->get();

        $groups = DB::table('customer_group')
            ->where('branch_id', $branchId)
            ->select('Group_No as group_name')
            ->distinct()
            ->get();

        return response()->json([
            'centers' => $centers,
            'groups' => $groups,
        ]);
    }

    public function depletion(){
        $branch = tableWithBranch('branch')->where('status','=','1')->get();
        $product = tableWithBranch('loan_category')->where('status','=','1')->get();
        $officer = tableWithBranch('user')->where('Status','=','1')->get();
        $branch_access=session('branch_access');
        return view('pages.Depletion',compact('branch','branch_access','product','officer'));
    }


    public function depletionData(\Illuminate\Http\Request $request)
    {
        // Filters ("" => null = All)
        $productId   = $request->filled('product') ? $request->input('product') : null;      // loan_category id
        $collectorId = $request->filled('loan_officer') ? $request->input('loan_officer') : null;

        // Dates (defaults to current month)
        $startInput = $request->input('from');
        $endInput   = $request->input('to');
        $start  = $startInput ? \Carbon\Carbon::parse($startInput)->startOfDay() : now()->startOfMonth()->startOfDay();
        $end    = $endInput   ? \Carbon\Carbon::parse($endInput)->endOfDay()   : now()->endOfMonth()->endOfDay();

        $today = now()->toDateString();                 // maturity cutoff for OC
        $customerIdCol = 'Customer_idCustomer';         // FK column in customer_loan (adjust if different)

        // ---------- as-of START snapshot (keep your existing logic) ----------
        $beginningOne = DB::query()->fromSub(function ($q) use ($start) {

            // 1) Sum of capital actually paid on/before $start, per loan  (DATE FIX)
            $capitalPaidBefore = DB::table('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, COALESCE(SUM(ins.capital_amount), 0) as capital_paid_before')
                ->whereDate('ins.Installment_Date', '<', $start->toDateString())
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');

            // 2) Join to loan table to get original capital (Amount) and compute balance
            $inner = DB::table('customer_loan as cl')
                ->leftJoinSub($capitalPaidBefore, 'p', 'p.Loan_ID', '=', 'cl.idCustomer_Loan')
                ->selectRaw("
                cl.idCustomer_Loan as Loan_ID,
                GREATEST(COALESCE(cl.Amount,0) - COALESCE(p.capital_paid_before,0), 0) as Capital_Balance,
                cl.Date_Time as Date_Time,
                0 as Loan_Log_ID,
                1 as rn
            ")
                ->when(session()->has('branch_id'), function ($qb) {
                    // Optional: keep branch scope if you need it
                    $qb->where('cl.branch_id', session('branch_id'));
                })
                ->orderBy('cl.idCustomer_Loan'); // not required; just to keep things deterministic

            // Push the built SELECT into the outer subquery
            $q->fromSub($inner, 't');

        }, 'x')
            ->where('rn', 1)
            ->where('Date_Time', '<', $start);

        // ---------- as-of END snapshot (NEW) ----------
        $endingOne = DB::query()->fromSub(function ($q) use ($end) {

            // (DATE FIX)
            $capitalPaidUntilEnd = DB::table('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, COALESCE(SUM(ins.capital_amount), 0) as capital_paid_until_end')
                ->whereDate('ins.Installment_Date', '<=', $end->toDateString())
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');

            DB::table('customer_loan as cl')
                ->leftJoinSub($capitalPaidUntilEnd, 'p', 'p.Loan_ID', '=', 'cl.idCustomer_Loan')
                ->selectRaw('
                cl.idCustomer_Loan as Loan_ID,
                GREATEST(COALESCE(cl.Amount,0) - COALESCE(p.capital_paid_until_end,0), 0) as Capital_Balance,
                ? as Date_Time,
                0 as Loan_Log_ID,
                1 as rn
            ', [$end])
                ->when(session()->has('branch_id'), function ($qb) {
                    $qb->where('cl.branch_id', session('branch_id'));
                })
                ->orderBy('cl.idCustomer_Loan')
                ->tap(function ($builder) use ($q) {
                    $q->fromSub($builder, 't');
                });

        }, 'y')->where('rn', 1);

        // 2) Investment sum per loan in [start, end]  (cl.Date_Time likely DATETIME → keep as-is)
        $investment = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_loan as cl')
                ->selectRaw('cl.idCustomer_Loan as Loan_ID, SUM(cl.Amount) as Investment_Sum')
                ->whereBetween('cl.Date_Time', [$start, $end])
                ->groupBy('cl.idCustomer_Loan');
        }, 'inv');

        // 3) Depletion (capital payments) per loan in [start, end]  (DATE FIX)
        $depletion = DB::query()->fromSub(function ($q) use ($start,$end) {
            $q->from('installments as i')
                ->selectRaw('i.Customer_Loan_idCustomer_Loan as Loan_ID, SUM(COALESCE(i.capital_amount, 0)) as Depletion_Sum')
                ->whereDate('i.Installment_Date', '>=', $start->toDateString())
                ->whereDate('i.Installment_Date', '<=', $end->toDateString())
                ->groupBy('i.Customer_Loan_idCustomer_Loan');
        }, 'dep');

        // Collections (customer_payments) per COLLECTOR in [start, end]  (already DATE-safe)
        $collectionsByCollector = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_payments as cp')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'cp.Customer_Loan_idCustomer_Loan')
                ->whereDate('cp.Date', '>=', $start->toDateString())
                ->whereDate('cp.Date', '<=', $end->toDateString())
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cp.branch_id', session('branch_id'));
                })
                ->selectRaw('cl.collector_id as collector_id, SUM(cp.Amount) as Collection_Sum')
                ->groupBy('cl.collector_id');
        }, 'cbc');

        // A) Installments sum by assigned collector within [start, end]  (uses <= end only; leave logic as-is)
        $installmentSum = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('installments as ins')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'ins.Customer_Loan_idCustomer_Loan')
                ->where('ins.Installment_Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(ins.Installment_Amount) as Installment_Sum')
                ->groupBy('cl.collector_id');
        }, 'insx');

        // Arrears per LOAN (sum of Total_Balance for installments before $end)  (already DATE-safe)
        $arrearsPerLoan = DB::query()->fromSub(function ($q) use ($end) {
            $q->from('installments as i')
                ->where('i.Installment_Date', '<', $end->toDateString())
                ->selectRaw('
                i.Customer_Loan_idCustomer_Loan as Loan_ID,
                SUM(COALESCE(i.Total_Balance, 0)) as Arrears_Sum
            ')
                ->groupBy('i.Customer_Loan_idCustomer_Loan');
        }, 'arrl');

        // B) Payments sum by assigned collector (<= end)  (keep logic; just ensure DATE-safe compare)
        $paymentsAssigned = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_payments as cp')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'cp.Customer_Loan_idCustomer_Loan')
                ->whereDate('cp.Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(cp.Amount) as Pay_Sum')
                ->groupBy('cl.collector_id');
        }, 'payx');

        // C) Savings credits per LOAN (<= end)  (sal.Date_Time likely DATETIME → keep as-is)
        $savingsCredits = DB::query()->fromSub(function ($q) use ($end) {
            $q->from('Customer_Saving_Accounts as csa')
                ->join('Savings_Account_Log as sal', 'sal.Saving_Acount_Id', '=', 'csa.id')
                ->where('sal.Date_Time', '<=', $end->toDateString())
                ->when(session()->has('branch_id'), function ($qq) {
                    // keep branch scoping (use csa’s branch; if you prefer the log's, switch to sal.branch_id)
                    $qq->where('csa.branch_id', session('branch_id'));
                })
                ->selectRaw('
                csa.Loan_Id as Loan_ID,
                SUM(COALESCE(sal.Credit, 0)) as Savings_Credit_Sum
            ')
                ->groupBy('csa.Loan_Id');
        }, 'sav');

        // TOTAL LOANS per collector (optional product filter)
        $collectorLoanTotals = DB::query()->fromSub(function ($q) use ($productId) {
            $q->from('customer_loan as cl')
                ->selectRaw('cl.collector_id as collector_id,
               COUNT(*) as Loans_Count_Total,
               SUM(cl.Amount) as Loans_Amount_Total')
                ->groupBy('cl.collector_id');
        }, 'lt');

        // PENALTY ARREARS per collector (<= end)  (already using <= end string)
        $penaltyArrears = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('installments as ins')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'ins.Customer_Loan_idCustomer_Loan')
                ->where('ins.Installment_Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(ins.Panalty_Balance) as Penalty_Sum')
                ->groupBy('cl.collector_id');
        }, 'pnlx');

        // OC loans per collector (maturity < today)
        $ocLoans = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, MAX(ins.Installment_Date) as maturity_date')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'm')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'm.Loan_ID')
            ->where('m.maturity_date', '<=', $end->toDateString())
            ->selectRaw('cl.collector_id as collector_id, COUNT(*) as oc_count')
            ->groupBy('cl.collector_id');

        // TOTAL CLIENTS per collector (optional product filter)
        $totalClients = DB::query()->fromSub(function ($q) use ($productId, $customerIdCol) {
            $q->from('customer_loan as cl')
                ->when($productId, fn($qq) => $qq->where('cl.Loan_Category_idLoan_Category', $productId))
                ->selectRaw("cl.collector_id as collector_id, COUNT(DISTINCT cl.`$customerIdCol`) as Clients_Total")
                ->groupBy('cl.collector_id');
        }, 'ct');

        // OC CLIENTS per collector (maturity < today)
        $ocClients = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, MAX(ins.Installment_Date) as maturity_date')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'm')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'm.Loan_ID')
            ->where('m.maturity_date','<=', $end->toDateString())
            ->selectRaw("cl.collector_id as collector_id, COUNT(DISTINCT cl.`$customerIdCol`) as OC_Clients")
            ->groupBy('cl.collector_id');

        // Outstanding (sum of installments.Total_Balance) per LOAN (NO date filter)
        $outstandingPerLoan = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('
                ins.Customer_Loan_idCustomer_Loan as Loan_ID,
                SUM(COALESCE(ins.Total_Balance, 0)) as Outstanding_Sum
            ')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'out');

        /*
         * NEW: Collector ↔ Product loan counts, folded into one row per collector via GROUP_CONCAT.
         * (No date/product filter so you see the full distribution per collector.)
         */
        $collectorProductCounts = DB::query()->fromSub(function ($q) {
            $q->from('customer_loan as cl')
                ->selectRaw('cl.collector_id,
               cl.Loan_Category_idLoan_Category as product_id,
               COUNT(*) as cnt')
                ->groupBy('cl.collector_id', 'cl.Loan_Category_idLoan_Category');
        }, 'pp')
            ->selectRaw('pp.collector_id,
         GROUP_CONCAT(CONCAT(pp.product_id, ":", pp.cnt)
                      ORDER BY pp.product_id SEPARATOR ",") as product_kv')
            ->groupBy('pp.collector_id');

        // ---------- Main rollup per collector ----------
        $collectorRows = DB::table('customer_loan as cl')
            ->join('loan_category as lc', 'lc.idLoan_Category', '=', 'cl.Loan_Category_idLoan_Category')
            ->leftJoin('user as u', 'u.id', '=', 'cl.collector_id')

            ->leftJoinSub($beginningOne, 'bs',   fn($j) => $j->on('bs.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($endingOne,    'es',   fn($j) => $j->on('es.Loan_ID', '=', 'cl.idCustomer_Loan'))  // NEW
            ->leftJoinSub($investment,   'inv',  fn($j) => $j->on('inv.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($depletion,    'dep',  fn($j) => $j->on('dep.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($collectionsByCollector, 'cbc', fn($j) => $j->on('cbc.collector_id', '=', 'u.id'))
            ->leftJoinSub($installmentSum,'insx',fn($j) => $j->on('insx.collector_id', '=', 'u.id'))
            ->leftJoinSub($paymentsAssigned,'payx',fn($j) => $j->on('payx.collector_id', '=', 'u.id'))
            ->leftJoinSub($collectorLoanTotals,'lt', fn($j) => $j->on('lt.collector_id', '=', 'u.id'))
            ->leftJoinSub($penaltyArrears, 'pnlx', fn($j) => $j->on('pnlx.collector_id', '=', 'u.id'))
            ->leftJoinSub($ocLoans, 'ocx', fn($j) => $j->on('ocx.collector_id', '=', 'u.id'))
            ->leftJoinSub($totalClients, 'ct', fn($j) => $j->on('ct.collector_id', '=', 'u.id'))
            ->leftJoinSub($ocClients, 'occt', fn($j) => $j->on('occt.collector_id', '=', 'u.id'))
            ->leftJoinSub($savingsCredits, 'sav', fn($j) => $j->on('sav.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($outstandingPerLoan, 'out', fn($j) => $j->on('out.Loan_ID', '=', 'cl.idCustomer_Loan'))

            // NEW: join collector ↔ product counts
            ->leftJoinSub($collectorProductCounts, 'pc', fn($j) => $j->on('pc.collector_id', '=', 'u.id'))
            // NEW: join arrears per collector
            ->leftJoinSub($arrearsPerLoan, 'arrl', fn($j) => $j->on('arrl.Loan_ID', '=', 'cl.idCustomer_Loan'))

            // optional filters
            ->when($productId,   fn($q) => $q->where('lc.idLoan_Category', $productId))
            ->when($collectorId, fn($q) => $q->where('cl.collector_id', $collectorId))

            // group by collector
            ->groupBy('u.id', 'u.Full_Name')

            ->selectRaw('
            COALESCE(u.id, 0)                      as collector_id,
            COALESCE(u.Full_Name, "—")             as collector,
            COUNT(DISTINCT cl.idCustomer_Loan)     as loan_count,

            SUM(COALESCE(bs.Capital_Balance, 0))   as beginning_total,
            SUM(COALESCE(es.Capital_Balance, 0))   as ending_total,       -- NEW
            SUM(COALESCE(inv.Investment_Sum, 0))   as investment_total,
            SUM(COALESCE(dep.Depletion_Sum, 0))    as depletion_total,

            COALESCE(MAX(cbc.Collection_Sum), 0)    as collection_total,
            COALESCE(MAX(insx.Installment_Sum), 0) as installment_total,
            COALESCE(MAX(payx.Pay_Sum), 0)         as payment_total_assigned,
            COALESCE(MAX(sav.Savings_Credit_Sum), 0)         as payment_saving,
            /* Use the pre-aggregated arrears by collector */
            COALESCE(SUM(arrl.Arrears_Sum), 0) as arrears_total,

            COALESCE(MAX(lt.Loans_Count_Total), 0)  as total_loans,
            COALESCE(MAX(lt.Loans_Amount_Total), 0) as total_loans_amount,

            COALESCE(MAX(pnlx.Penalty_Sum), 0)      as penalty_arrears_total,
            COALESCE(MAX(ocx.oc_count), 0)          as oc_loan_count,

            COALESCE(MAX(ct.Clients_Total), 0)      as total_clients,
            COALESCE(MAX(occt.OC_Clients), 0)       as oc_clients,

            /* NEW: flattened product counts like "12:34,15:7" */
            COALESCE(MAX(pc.product_kv), "")        as product_kv, SUM(COALESCE(out.Outstanding_Sum, 0)) as outstanding_total
        ')
            ->where('cl.branch_id', session('branch_id'))   // keep branch scoping
            ->whereIn('cl.Status', [0, 1])
            ->orderBy('collector')
            ->get();

        // Shape response
        $data = collect($collectorRows)->map(function($r) {
            $begin   = (float) ($r->beginning_total ?? 0);
            $invest  = (float) ($r->investment_total ?? 0);
            $deplete = (float) ($r->depletion_total ?? 0);

            // Use snapshot as-of $end (DON'T change other fields)
            $endStock    = $begin+$invest-$deplete;

            $arrears     = (float) ($r->arrears_total ?? 0);

            $portfolio   = $endStock+$arrears;
            $debtorRatio = $endStock > 0 ? ($arrears / $endStock) * 100 : 0;

            $totalClients  = (int) ($r->total_clients ?? 0);
            $ocClients     = (int) ($r->oc_clients ?? 0);
            $activeClients = max(0, $totalClients - $ocClients);

            // Parse pc.product_kv → { product_id: count, ... }
            $perProduct = [];
            $kv = (string)($r->product_kv ?? '');
            if ($kv !== '') {
                foreach (explode(',', $kv) as $pair) {
                    [$pid, $cnt] = array_pad(explode(':', $pair, 2), 2, null);
                    if ($pid !== null && $cnt !== null) {
                        $perProduct[(string)(int)$pid] = (int)$cnt; // string keys play nice in JSON/JS
                    }
                }
            }

            return [
                'Loan_Officer'               => $r->collector ?? '—',
                'Beginning_Stock'            => round($begin, 2),
                'Current_End_Stock'          => round($endStock, 2),            // NEW source
                'Investment'                 => round($invest, 2),
                'Depletion'                  => round($deplete, 2),
                'Collection'                 => round((float) ($r->collection_total ?? 0), 2),
                'Arrears'                    => round($arrears, 2),
                'Portfolio'                  => round($portfolio, 2),
                'Debtor_Ratio'               => round($debtorRatio, 2),
                'Penalty_Arrears'            => round((float) ($r->penalty_arrears_total ?? 0), 2),
                'Total_Loans'                => (int)   ($r->total_loans ?? 0),
                'OC_Loans'                   => (int)   ($r->oc_loan_count ?? 0),
                'Total_Clients'              => $totalClients,
                'OC_Clients'                 => $ocClients,
                'Active_Clients'             => $activeClients,
                'Total_Outstanding_Balance'  => round((float) ($r->outstanding_total ?? 0), 2),
                // per-collector product counts map
                'product_counts'             => (object)$perProduct,
            ];
        });

        return response()->json(['data' => $data], 200);
    }










}
