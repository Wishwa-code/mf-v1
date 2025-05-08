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
        $customers = tableWithBranch('customer','customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name')
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
        $bank = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->get();
        $expences_category = tableWithBranch('company_bank_accounts')->where('acc_type_group','=','Expenses')->where('Bank_Type','=','ChartOfAccount')->get();
        return view('pages.CreateExpenses',compact('bank','expences_category'));
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
            'Bank_Type' => "Expenses",
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
        $centers = DB::table('center')->where('branch_id', session('branch_id'))->get();

        // Fetch the list of groups
        $groups = DB::table('customer_group')->where('branch_id', session('branch_id'))->select('Group_No as group_name')->distinct()->get();

        // Initialize the query for fetching loans
        $query = tableWithBranch('customer_loan', 'customer_loan')
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

        // Execute the query and get the loan data
        $loan = $query->get();

        // Pass loan, centers, and groups data to the view
        return view('pages.LoanSummaryDetails', compact('loan', 'centers', 'groups'));
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
        $expenses->branch_id = session('branch_id');

        if ($expenses->save()) {

            if ($type=="Expense") {
                $bank_id=tableWithBranch('company_bank_accounts')
                    ->where('acc_type_group','=','Expenses')
                    ->where('Idbank','=',$request->category)
                    ->first();
                $this->bankLogController->index($request->bank,"Expenses",$reason,"-","credit",$amount,$bank_id->Idbank);
                $this->bankLogController->index($bank_id->Idbank,"Expenses",$reason,"-","debit",$amount,$request->bank);
            }else{
                $bank_id=tableWithBranch('company_bank_accounts')
                    ->where('acc_type_group','=','Income')
                    ->where('Idbank','=',$request->category)
                    ->first();
                $this->bankLogController->index($request->bank,"Income",$reason,"-","debit",$amount,$bank_id->Idbank);
                $this->bankLogController->index($bank_id->Idbank,"Income",$reason,"-","credit",$amount,$request->bank);
            }


            // If the data is saved successfully, return a success response
            return response()->json(['message' => 'Data saved successfully'], 200);
        } else {
            // If the data failed to save, return an error response
            return response()->json(['message' => 'Failed to save data'], 500);
        }


    }

    public function viewexpenses(){
        $expenses = tableWithBranch('expences','expences')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'expences.category_id')
            ->where('company_bank_accounts.Bank_Type', 'Expenses')
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
        DB::table('expences')
            ->where('id', $id)
            ->where('branch_id', session('branch_id'))
            ->delete();
        $expenses = DB::table('expences')
            ->join('expences_category', 'expences_category.id', '=', 'expences.category_id')
            ->where('type', 'Expense')
            ->where('expences.branch_id', session('branch_id'))
            ->get();
        return view('pages.ViewExpenses', compact('expenses'));
    }

    public function deleteincome(string $id){
        DB::table('expences')
            ->where('id', $id)
            ->where('branch_id', session('branch_id'))
            ->delete();
        $expenses = DB::table('expences')
            ->join('income_category', 'income_category.id', '=', 'expences.category_id')
            ->where('type', 'Income')
            ->where('expences.branch_id', session('branch_id'))
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
        return view('pages.SavingReport', compact('group', 'lending_officer', 'recovery_officer', 'center', 'customers', 'route'));
    }


    public function savings_report_filter(Request $request){
        $center_details = $request->center_details;
        $route = $request->route;
        $group = $request->group;
        $customer = $request->customer;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $lending_officer = $request->has('lending') && !empty($request->lending) ? $request->lending : '0';

        $loanQuery = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                            FROM group_has_customer
                            LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->select(
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(center.Name, "-") as center_name'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                'customer.Nic as member_nic',
                DB::raw('CONCAT(customer.First_Name, " ", customer.Last_Name) as member_name'),
                DB::raw('SUM(installments.Saving_amount-installments.Saving_balance) as saving_amount') // Summing saving balances
            )
            ->where('customer_loan.Status', '=', '0')
            ->where('customer_loan.branch_id', session('branch_id'))
            ->orderBy('center_no');

        // Apply filters based on center, group, customer, etc.
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }
        if ($route != '0') {
            $loanQuery->where('route.id_route', '=', $route);
        }
        if ($group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
        }
        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }
        if ($lending_officer != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }

        // Filter by date range if both dates are provided
        if (!empty($date_from) && !empty($date_to)) {
            $loanQuery->whereBetween('installments.Installment_Date', [$date_from, $date_to]);
        }

        // Group by required columns
        $loanQuery->groupBy('center.No','center.Name', 'subquery.group_name', 'customer.Nic', 'customer.First_Name', 'customer.Last_Name');

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
        $branches = tableWithBranch('branch')->where('status', '=', '1')->get();
        $routes = tableWithBranch('route')->get();
        $centers = tableWithBranch('center')->get();
        $collectors = tableWithBranch('user')->where('collector', '=', '1')->get();
        $loanProducts = tableWithBranch('loan_category')->get();

        return view('pages.PaymentFullDetailsReport', compact('loanProducts', 'payments', 'collectors', 'branches', 'routes', 'centers'));

    }



}
