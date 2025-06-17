<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    protected $SavingAccountController;

    // Single constructor to inject both controllers
    public function __construct(SavingAccountController $SavingAccountController)
    {
        $this->SavingAccountController = $SavingAccountController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $center = tableWithBranch('center')->get();
        $group = tableWithBranch('customer_group')->get();

        // Check if $center is empty
        if ($center->isEmpty()) {
            // Handle the case when the center table has no values
            $center_details = null; // Or any default value you want to assign
            $group_details = null; // Or any default value you want to assign
            $grouped_loans = array(); // Or any default value you want to assign
            return view('pages.DailyRepayment', compact('center', 'grouped_loans','center_details'));
        } else {
            // If $center is not empty, set the default center value
            $group_details = $request->group_details ?? $group[0]->idCustomer_Group;
            $center_details = $request->center_details ?? $center[0]->idCenter;
        }

        // Loan Query
        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                     FROM group_has_customer
                     LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance_until'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date = CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Today_installment'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as arrease')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Contact_No',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.type',
                'customer_loan.Installment_Count',
                'customer_loan.Vehicle_No',
                'customer_loan.idCustomer_Loan',
                'customer_loan.capital_balance',
                'customer_loan.Installment_Amount',
                'subquery.group_name'
            );

        // Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($group_details != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group_details);
        }

        $loan = $loanQuery->get();

        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        // Convert the grouped loans array to an array (if not already)
        $grouped_loans = is_array($grouped_loans) ? $grouped_loans : $grouped_loans->toArray();

// Sort the groups based on the numeric part of the key
        uksort($grouped_loans, function ($a, $b) {
            // Extract the numeric portion of the group names
            preg_match('/\d+/', $a, $matchesA);
            preg_match('/\d+/', $b, $matchesB);

            $numA = isset($matchesA[0]) ? (int)$matchesA[0] : 0;
            $numB = isset($matchesB[0]) ? (int)$matchesB[0] : 0;

            return $numA <=> $numB; // Ascending order
        });



        return view('pages.DailyRepayment', compact('center','group', 'grouped_loans','center_details','group_details'));
    }


    public function create_for_finwin(Request $request)
    {
        $center = tableWithBranch('center')->get();
        $group = tableWithBranch('customer_group')->get();

        // Check if $center is empty
        if ($center->isEmpty()) {
            // Handle the case when the center table has no values
            $center_details = null; // Or any default value you want to assign
            $group_details = null; // Or any default value you want to assign
            $grouped_loans = array(); // Or any default value you want to assign
            return view('pages.DailyRepayment', compact('center', 'grouped_loans','center_details'));
        } else {
            // If $center is not empty, set the default center value
            $group_details = $request->group_details ?? $group[0]->idCustomer_Group;
            $center_details = $request->center_details ?? $center[0]->idCenter;
        }

        // Loan Query
        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                     FROM group_has_customer
                     LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance_until'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date = CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Today_installment'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as arrease')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Contact_No',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.type',
                'customer_loan.Installment_Count',
                'customer_loan.Vehicle_No',
                'customer_loan.idCustomer_Loan',
                'customer_loan.capital_balance',
                'customer_loan.Installment_Amount',
                'subquery.group_name'
            );

        // Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($group_details != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group_details);
        }

        $loan = $loanQuery->get();

        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        // Convert the grouped loans array to an array (if not already)
        $grouped_loans = is_array($grouped_loans) ? $grouped_loans : $grouped_loans->toArray();

// Sort the groups based on the numeric part of the key
        uksort($grouped_loans, function ($a, $b) {
            // Extract the numeric portion of the group names
            preg_match('/\d+/', $a, $matchesA);
            preg_match('/\d+/', $b, $matchesB);

            $numA = isset($matchesA[0]) ? (int)$matchesA[0] : 0;
            $numB = isset($matchesB[0]) ? (int)$matchesB[0] : 0;

            return $numA <=> $numB; // Ascending order
        });



        return view('pages.DailyRepaymentFinwin', compact('center','group', 'grouped_loans','center_details','group_details'));
    }

    public function getGroupsByCenter($centerId)
    {
        $groups = tableWithBranch('customer_group')->where('center_id', $centerId)->get();

        return response()->json($groups);
    }



    public function create_lasantha(Request $request){
        $center = tableWithBranch('route')->get();
        $route="";
        // Check if $center is empty
        if ($center->isEmpty()) {
            // Handle the case when the center table has no values
            $center_details = null; // Or any default value you want to assign
            $grouped_loans = array(); // Or any default value you want to assign
            return view('pages.DailyRepayment', compact('center', 'grouped_loans','center_details'));
        } else {
            // If $center is not empty, set the default center value
            $center_details = $request->route ?? $center[0]->id_route;
            $routequery = DB::table('route')->where('branch_id', session('branch_id'))->where('id_route','=',$center[0]->id_route)->first();
            $route=$routequery->name;
        }

        // Loan Query
        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                     FROM group_has_customer
                     LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'route.id_route', '=', 'center.route_id')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(route.name, "-") as route_name'),
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance_until'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date = CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Today_installment'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as arrease')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'route.name',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Contact_No',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.type',
                'customer_loan.Installment_Count',
                'customer_loan.Vehicle_No',
                'customer_loan.idCustomer_Loan',
                'customer_loan.capital_balance',
                'customer_loan.Installment_Amount',
                'subquery.group_name'
            );

        // Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('route.id_route', '=', $center_details);
        }

        $loan = $loanQuery->get();

        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        return view('pages.DailyRepaymentLasantha', compact('center','route', 'grouped_loans','center_details'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $saving_account=tableWithBranch('Customer_Saving_Accounts')
            ->where('id','=',$request->accountId)
            ->first();

        $amount=$request->amount;

        if ($request->type=="Deposit"){
            $this->SavingAccountController->index($saving_account->id,'Deposit','Deposit',$amount,'0.00',$amount,'Credit');
        }else{
            $this->SavingAccountController->index($saving_account->id,'Withdraw','Withdraw','0.00',$amount,$amount,'Debit');
        }


    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $group = tableWithBranch('customer_group')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        return view('pages.SettledLoan', compact('route','group', 'loan_category', 'customers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {


        $group=$request->group;
        $category=$request->category;
        $customer=$request->customer;
        $route = $request->route;

        if ($group == '0' && $category == '0' && $customer == '0' && $route == '0') {
            $loan = tableWithBranch('customer_loan','customer_loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
                ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id') // Join for User_idUser
                ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id') // Join for lending_officer_id
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Name, "-") as group_name
                         FROM group_has_customer
                         LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
                ->where('customer_loan.Status', '=', '1')
                ->select(
                    'customer_loan.*',
                    'loan_category.Name as loan_name',
                    'customer.*',
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                    DB::raw('IFNULL(route.name, "-") as route_name'),
                    'u1.Full_Name as user_name',
                    'u2.Full_Name as lending_officer'
                )
                ->get();


            return response()->json(['item' => $loan,'message' => 'all'], 200);
        } else {
            $loanQuery = tableWithBranch('customer_loan','customer_loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Name, "-") as group_name
                         FROM group_has_customer
                         LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
                ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id') // Join for User_idUser
                ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id') // Join for lending_officer_id
                ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
                ->where('customer_loan.Status', '=', '1')
                ->select(
                    'customer_loan.*',
                    'loan_category.Name as loan_name',
                    'customer.*',
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                    DB::raw('IFNULL(route.name, "-") as route_name'),
                    'u1.Full_Name as user_name',
                    'u2.Full_Name as lending_officer'
                );

            if ($group != '0') {
                $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
            }

            if ($category != '0') {
                $loanQuery->where('loan_category.idLoan_Category', '=', $category);
            }

            if ($customer != '0') {
                $loanQuery->where('customer.idCustomer', '=', $customer);
            }

            if ($route != '0') {
                $loanQuery->where('customer.route_id', '=', $route);
            }

            $loan = $loanQuery->get();
            return response()->json(['item' => $loan, 'message' => 'notall'], 200);

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

    public function create_hm(Request $request)
    {
        $center = tableWithBranch('center')->get();

        // Set default values
        $center_details = $request->center_details ?? ($center->isNotEmpty() ? $center[0]->idCenter : null);
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // Loan Query
        $loanQuery = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(customer_loan.Balance_Amount, 2) as Total_Balance'),
                DB::raw('ROUND(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END, 2) as arrease') // Removed SUM()
            );

        // Filter by center if selected
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        // Filter by date range if provided
        if (!empty($from_date)) {
            $loanQuery->whereDate('installments.Installment_Date', $from_date);
        }

        $loan = $loanQuery->get();

        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        return view('pages.DailyRepaymentNoble', compact('center', 'grouped_loans', 'center_details', 'from_date'));
    }

    public function rightway(Request $request){
        $center = tableWithBranch('center')->get();

        // Check if $center is empty
        if ($center->isEmpty()) {
            // Handle the case when the center table has no values
            $center_details = null; // Or any default value you want to assign
            $grouped_loans = array(); // Or any default value you want to assign
            return view('pages.RightWayDailyRepayment', compact('center', 'grouped_loans','center_details'));
        } else {
            // If $center is not empty, set the default center value
            $center_details = $request->center_details ?? $center[0]->idCenter;
        }

        // Loan Query
        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('loan_category', 'loan_category.idLoan_Category', '=', 'customer_loan.Loan_Category_idLoan_Category')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                     FROM group_has_customer
                     LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.cus_number as cus_number',
                'loan_category.Product_code as Product_code',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance_until'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date = CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Today_installment'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as arrease'),
                DB::raw('(SELECT Saving_Account_Balance FROM Loan_Log 
          WHERE Loan_Log.Loan_ID = customer_loan.idCustomer_Loan 
          ORDER BY Loan_Log.Loan_Log_ID DESC LIMIT 1) as last_saving_balance')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'customer.First_Name',
                'loan_category.Product_code',
                'customer.cus_number',
                'customer.Last_Name',
                'customer.Contact_No',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.type',
                'customer_loan.Installment_Count',
                'customer_loan.Vehicle_No',
                'customer_loan.idCustomer_Loan',
                'customer_loan.capital_balance',
                'customer_loan.Installment_Amount',
                'subquery.group_name'
            );

        // Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        $loan = $loanQuery->get();

        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        return view('pages.RightWayDailyRepayment', compact('center', 'grouped_loans','center_details'));
    }

    public function GreenLankaTrustRepayment(Request $request){
        $center = tableWithBranch('center')->get();

        // Check if $center is empty
        if ($center->isEmpty()) {
            // Handle the case when the center table has no values
            $center_details = null; // Or any default value you want to assign
            $grouped_loans = array(); // Or any default value you want to assign
            return view('pages.RightWayDailyRepayment', compact('center', 'grouped_loans','center_details'));
        } else {
            // If $center is not empty, set the default center value
            $center_details = $request->center_details ?? $center[0]->idCenter;
        }

        // Loan Query
        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('loan_category', 'loan_category.idLoan_Category', '=', 'customer_loan.Loan_Category_idLoan_Category')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                     FROM group_has_customer
                     LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.cus_number as cus_number',
                'loan_category.Product_code as Product_code',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.Contact_No as Contact_No',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Balance_Amount as Balance_Amount',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance_until'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date = CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Today_installment'),
                DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date < CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as arrease'),
                DB::raw('(SELECT Saving_Account_Balance FROM Loan_Log 
          WHERE Loan_Log.Loan_ID = customer_loan.idCustomer_Loan 
          ORDER BY Loan_Log.Loan_Log_ID DESC LIMIT 1) as last_saving_balance')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'customer.First_Name',
                'loan_category.Product_code',
                'customer.cus_number',
                'customer.Last_Name',
                'customer.Contact_No',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.Balance_Amount',
                'customer_loan.Amount',
                'customer_loan.type',
                'customer_loan.Installment_Count',
                'customer_loan.Vehicle_No',
                'customer_loan.idCustomer_Loan',
                'customer_loan.capital_balance',
                'customer_loan.Installment_Amount',
                'subquery.group_name'
            );

        // Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        $loan = $loanQuery->get();
        $loan->transform(function ($item) {
            $initial = strtoupper(substr($item->customer_name, 0, 1)) . '.';
            $item->name_with_initials = $initial . ' ' . $item->customer_lastname;
            return $item;
        });

        $group_filter = $request->group_filter;
        if ($group_filter) {
            $loan = $loan->filter(function ($item) use ($group_filter) {
                return $item->group_name === $group_filter;
            });
        }


        // Group data by 'group_name'
        $grouped_loans = $loan->groupBy('group_name');

        return view('pages.GreenLankaTrustRepayment', compact('center', 'grouped_loans','center_details'));
    }

}
