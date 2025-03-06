<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TodayPaymentController extends Controller
{


    protected $smsLogController;
    protected $bankLogController;
    protected $capitalBalanceController;
    protected $loanLogController;

    protected $SavingAccountController;
    protected $customerLogController;

    // Single constructor to inject both controllers
    public function __construct(CustomerLogController $customerLogController,SmsController $smsLogController, BankLogController $bankLogController, CapitalBalanceController $capitalBalanceController, LoanLogController $loanLogController, SavingAccountController $SavingAccountController)
    {
        $this->smsLogController = $smsLogController;
        $this->bankLogController = $bankLogController;
        $this->capitalBalanceController = $capitalBalanceController;
        $this->loanLogController = $loanLogController;
        $this->SavingAccountController = $SavingAccountController;
        $this->customerLogController = $customerLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = DB::table('company')->first();
        $group = DB::table('customer_group')->where('branch_id','=',session('branch_id'))->get();
        $customers = DB::table('customer')->where('branch_id','=',session('branch_id'))->get();
        $center = DB::table('center')->where('branch_id','=',session('branch_id'))->get();

        $user_id = (int)session('userid');

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector=0;
        if ($collector_val){
            $collector = $collector_val->collector;
        }

        $loanQuery = tableWithBranch('customer_loan','customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->where('customer_loan.Status', '=', '0');
        if ($collector == 1) {
            $loanQuery->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }
        $loan = $loanQuery->get();
        $route = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $banks = DB::table('company_bank_accounts')->where('branch_id','=',session('branch_id'))->where('status', '=', '1')->get();
        if ($collector == 1) {
            $banks = DB::table('company_bank_accounts')->where('branch_id','=',session('branch_id'))->where('Account_No', '=', $user_id)->where('status', '=', '1')->get();
        }
        return view('pages.TodayPayment', compact('collector', 'group', 'loan', 'route', 'center', 'customers', 'company', 'banks'));
    }

    public function bulk_repayment()
    {
        $company = DB::table('company')->first();
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $route = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();

        $user_id = (int)session('userid');

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector = $collector_val->collector;
        $loanQuery = tableWithBranch('customer_loan','customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->where('customer_loan.Status', '=', '0');
        if ($collector == 1) {
            $loanQuery->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }
        $loan = $loanQuery->get();
        return view('pages.BulkPayment', compact('group','loan', 'route', 'center', 'customers', 'company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $center_details = $request->center_details;
        $route = $request->route;
        $group = $request->group;
        $customer = $request->customer;
        $status = $request->status;
        $loan_number = $request->loan_number_search;

        $user_id = (int)session('userid');

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector = $collector_val->collector;


        $recovery_officer = $request->has('recovery') && !empty($request->recovery) ? $request->recovery : '0';
        $lending_officer = $request->has('lending') && !empty($request->lending) ? $request->lending : '0';

        $poya=tableWithBranch('holidays')->get();
        $poyaDates = $poya->pluck('date')->toArray(); // Extract only the dates

        // Subquery for aggregated calculations on installments table
        $subquery = DB::table('installments')
            ->select(
                'Customer_Loan_idCustomer_Loan',
                DB::raw('COUNT(idInstallments) as Calculated_Installment_Count'),
                DB::raw('SUM(Total_Balance) as Total_Balance'),
                DB::raw('SUM(Paid_Amount) as Total_Paid_Amount'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until'),
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease')
            )
            ->where('installments.branch_id', '=', session('branch_id'))
            ->whereNotIn('Panelty_date', $poyaDates) // Exclude dates in $poya
            ->groupBy('Customer_Loan_idCustomer_Loan');


        // Main query with joins, using the subquery as 'installment_summary'
        $loanQuery = DB::table('customer_loan')
            ->joinSub($subquery, 'installment_summary', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'installment_summary.Customer_Loan_idCustomer_Loan');
            })
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->select(
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                'installment_summary.Calculated_Installment_Count',
                'installment_summary.Total_Balance',
                'installment_summary.Total_Paid_Amount',
                'installment_summary.Total_Balance_until',
                'installment_summary.Today_installment',
                'installment_summary.arrease'
            )
            ->where('customer_loan.branch_id','=',session('branch_id'))
            ->where('customer_loan.Status', '=', '0');

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
        if ($recovery_officer != '0') {
            $loanQuery->where('customer_loan.collector_id', '=', $recovery_officer);
        }
        if ($lending_officer != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }
        if ($collector == 1) {
            $loanQuery->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }

        // Apply status-specific filters
        if ($status == '0') {
            $loanQuery->having('installment_summary.Total_Balance_until', '>', 0); // Reference the alias from the subquery
        } elseif ($status == '2') {
            $loanQuery->having('installment_summary.arrease', '>', 0); // Correctly reference alias
        } elseif ($status == '1') {
            $loanQuery->having('installment_summary.Today_installment', '>', 0);
        }



        if ($loan_number != '0') {
            $loanQuery->where('customer_loan.idCustomer_Loan', '=', $loan_number);
        }

        // Paginate the loans
        $loan = $loanQuery->paginate(10);
        $loanQuery_2 = DB::table('installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                'Customer_Loan_idCustomer_Loan',
                DB::raw('COUNT(idInstallments) as Calculated_Installment_Count'),
                DB::raw('SUM(Total_Balance) as Total_Balance'),
                DB::raw('SUM(Paid_Amount) as Total_Paid_Amount'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until'),
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease')
            )
            ->where('installments.branch_id','=',session('branch_id'))
            ->groupBy('Customer_Loan_idCustomer_Loan');
        if ($collector == 1) {
            $loanQuery_2->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }

        $gettotal = $loanQuery_2->get();

        return response()->json(['item' => $loan, 'message' => 'all', 'gettotal' => $gettotal], 200);
    }




    public function latePayment(Request $request)
    {
        $center_details = $request->center_details;
        $group = $request->group;
        $customer = $request->customer;
        $route = $request->route;
        $status = $request->status;
        $lending_officer = $request->lending;


        if ($status == '-1') {
            $today = Carbon::now()->toDateString();
            $loanQuery = DB::table('installments')
                ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                         FROM group_has_customer
                         LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
                ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
                ->where('customer_loan.Status', '=', '0')
                ->select(
                    'customer.idCustomer',
                    DB::raw('IFNULL(center.No, "-") as center_no'),
                    'customer.First_Name as customer_name',
                    'route.name as routename',
                    'customer.Last_Name as customer_lastname',
                    'customer.Nic as NIC',
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
                    DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Installment_Balance'),
                    DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Panalty_Balance ELSE 0 END), 2) as Panalty_Balance'),
                    DB::raw('ROUND(SUM(CASE WHEN installments.Installment_Date <= CURDATE() THEN installments.Total_Balance ELSE 0 END), 2) as Total_Balance')
                )
                ->groupBy(
                    'customer.idCustomer',
                    'center.No',
                    'route.name',
                    'customer.First_Name',
                    'customer.Last_Name',
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
        }else{
            $loanQuery = DB::table('installments')
                ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
             FROM group_has_customer
             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
                ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
                ->where('installments.Status', '=', '0')
                ->where('customer_loan.Status', '=', '0')
                ->select(
                    'customer.idCustomer',
                    DB::raw('IFNULL(center.No, "-") as center_no'),
                    'customer.First_Name as customer_name',
                    'route.name as routename',
                    'customer.Last_Name as customer_lastname',
                    'customer.Nic as NIC',
                    'customer_loan.Loan_No as Loan_No',
                    'customer_loan.Amount as Loan_Amount',
                    'customer_loan.idCustomer_Loan as idCustomer_Loan',
                    'customer_loan.type as type',
                    'customer_loan.Installment_Amount as Installment_Amount',
                    'customer_loan.Vehicle_No as Vehicle_No',
                    'customer_loan.idCustomer_Loan as idCustomer_Loan',
                    DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                    DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Installment_Balance'),
                    DB::raw('ROUND(SUM(installments.Panalty_Balance), 2) as Panalty_Balance'),
                    DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance')
                )
                ->groupBy(
                    'customer.idCustomer',
                    'center.No',
                    'route.name',
                    'customer.First_Name',
                    'customer.Last_Name',
                    'customer.Nic',
                    'customer_loan.Loan_No',
                    'customer_loan.Amount',
                    'customer_loan.type',
                    'customer_loan.Vehicle_No',
                    'customer_loan.Installment_Amount',
                    'customer_loan.idCustomer_Loan',
                    'subquery.group_name'
                );
        }


// Filter by center, group, and customer if provided
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
        }

        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }

        if ($route != '0') {
            $loanQuery->where('route.id_route', '=', $route);
        }

        if ($lending_officer != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }


// Apply status-specific filters
        if ($status == '1') {
            $loanQuery->whereDate('installments.Installment_Date', '=', date('Y-m-d'));
        } elseif ($status == '2') {
            $loanQuery->whereDate('installments.Installment_Date', '<', date('Y-m-d'));
        } elseif ($status == '-1') {

        }else{
            $loanQuery->whereDate('installments.Installment_Date', '<=', date('Y-m-d'));
        }

        $loan = $loanQuery->get();



        return response()->json(['item' => $loan, 'message' => 'all'], 200);
    }



    public function Bulk_create(Request $request)
    {
        $center_details = $request->center_details;
        $route = $request->route;
        $group = $request->group;
        $customer = $request->customer;
        $status = $request->status;
        $loan_number = $request->loan_number_search;

        $user_id = (int)session('userid');

        $collector_val = DB::table('user')->where('id', '=', $user_id)->first();
        $collector = $collector_val->collector;


        $recovery_officer = $request->has('recovery') && !empty($request->recovery) ? $request->recovery : '0';
        $lending_officer = $request->has('lending') && !empty($request->lending) ? $request->lending : '0';

        $poya=tableWithBranch('holidays')->get();
        $poyaDates = $poya->pluck('date')->toArray(); // Extract only the dates

        // Subquery for aggregated calculations on installments table
        $subquery = DB::table('installments')
            ->select(
                'Customer_Loan_idCustomer_Loan',
                DB::raw('COUNT(idInstallments) as Calculated_Installment_Count'),
                DB::raw('SUM(Total_Balance) as Total_Balance'),
                DB::raw('SUM(Paid_Amount) as Total_Paid_Amount'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until'),
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease')
            )
            ->where('installments.branch_id', '=', session('branch_id'))
            ->whereNotIn('Panelty_date', $poyaDates) // Exclude dates in $poya
            ->groupBy('Customer_Loan_idCustomer_Loan');


        // Main query with joins, using the subquery as 'installment_summary'
        $loanQuery = DB::table('customer_loan')
            ->joinSub($subquery, 'installment_summary', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'installment_summary.Customer_Loan_idCustomer_Loan');
            })
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->select(
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.idCustomer',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                'installment_summary.Calculated_Installment_Count',
                'installment_summary.Total_Balance',
                'installment_summary.Total_Paid_Amount',
                'installment_summary.Total_Balance_until',
                'installment_summary.Today_installment',
                'installment_summary.arrease',
                 DB::raw('COALESCE(center.idCenter, "No Center") as Center_ID') // Handle NULL values
            )
            ->where('customer_loan.branch_id','=',session('branch_id'))
            ->where('customer_loan.Status', '=', '0');

        // Apply filters based on center, group, customer, etc.
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }else{
            $loanQuery->where(function ($query) use ($center_details) {
                $query->where('center.idCenter', '=', $center_details)
                    ->orWhereNull('center.idCenter'); // Include customers with no center
            });
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
        if ($recovery_officer != '0') {
            $loanQuery->where('customer_loan.collector_id', '=', $recovery_officer);
        }
        if ($lending_officer != '0') {
            $loanQuery->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }
        if ($collector == 1) {
            $loanQuery->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }

        // Apply status-specific filters
        if ($status == '0') {
            $loanQuery->having('installment_summary.Total_Balance_until', '>', 0); // Reference the alias from the subquery
        } elseif ($status == '2') {
            $loanQuery->having('installment_summary.arrease', '>', 0); // Correctly reference alias
        } elseif ($status == '1') {
            $loanQuery->having('installment_summary.Today_installment', '>', 0);
        }



        if ($loan_number != '0') {
            $loanQuery->where('customer_loan.idCustomer_Loan', '=', $loan_number);
        }

        // Paginate the loans
        $loan = $loanQuery->paginate(500);
        $loanQuery_2 = DB::table('installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->select(
                'Customer_Loan_idCustomer_Loan',
                DB::raw('COUNT(idInstallments) as Calculated_Installment_Count'),
                DB::raw('SUM(Total_Balance) as Total_Balance'),
                DB::raw('SUM(Paid_Amount) as Total_Paid_Amount'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until'),
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease'),
                DB::raw('COALESCE(center.idCenter, "No Center") as Center_ID') // Handle NULL values
            )
            ->where('installments.branch_id','=',session('branch_id'))
            ->groupBy('Customer_Loan_idCustomer_Loan','center.idCenter');
        if ($collector == 1) {
            $loanQuery_2->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', '=', $user_id);
        }
        if ($center_details != '0') {
            $loanQuery_2->where('center.idCenter', '=', $center_details);
        }else{
            $loanQuery_2->where(function ($query) use ($center_details) {
                $query->where('center.idCenter', '=', $center_details)
                    ->orWhereNull('center.idCenter'); // Include customers with no center
            });
        }
        if ($route != '0') {
            $loanQuery_2->where('route.id_route', '=', $route);
        }
        if ($group != '0') {
            $loanQuery_2->where('customer_group.idCustomer_Group', '=', $group);
        }
        if ($customer != '0') {
            $loanQuery_2->where('customer.idCustomer', '=', $customer);
        }
        if ($recovery_officer != '0') {
            $loanQuery_2->where('customer_loan.collector_id', '=', $recovery_officer);
        }
        if ($lending_officer != '0') {
            $loanQuery_2->where('customer_loan.lending_officer_id', '=', $lending_officer);
        }

        $gettotal = $loanQuery_2->get();

        return response()->json(['item' => $loan, 'message' => 'all', 'gettotal' => $gettotal], 200);
    }


    public function create_view($id){
        $loan = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan','=',$id)
            ->select(
                'Customer_Loan_idCustomer_Loan',
                DB::raw('COUNT(idInstallments) as Calculated_Installment_Count'),
                DB::raw('SUM(Total_Balance) as Total_Balance'),
                DB::raw('SUM(Paid_Amount) as Total_Paid_Amount'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until'),
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease')
            )
            ->where('installments.branch_id','=',session('branch_id'))
            ->groupBy('Customer_Loan_idCustomer_Loan')->get();
        return response()->json(['item' => $loan], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = (int)session('userid');
        $loan_id = $request->loan_id;
        $cus_id = $request->cus_id;
        $payment_amount = $request->payment_amount;
        $new_payment_amount = $request->payment_amount;
        $payment_date = $request->payment_date;
        $time = date('H:i:s');
        $payment_type = $request->payment_type;

        $cheque_accept = $request->cheque_accept ?? '0';
        $cheque_id = $request->cheque_id ?? '0';
        $cheque_issue_bank= $request->cheque_issue_bank ?? '0';
        $chq_number= $request->chq_number ?? '0';
        $chq_type= $request->chq_type ?? '0';
        $name_on_cheque= $request->name_on_cheque ?? '0';
        $chq_date= $request->chq_date ?? '0';


        $loan = tableWithBranch('customer_loan')
            ->where('idCustomer_Loan', '=', $loan_id)
            ->first();

        if ($cheque_accept == "0") {
            if ($payment_type == "Cheque") {
                $slipPath = null;
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $directory = 'payment_slip';

                    // Check if the directory exists on the public disk, create it if not
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    $slipPath = Storage::disk('public')->putFile($directory, $file);
                }
                DB::table('Cheque_payment')->insert([
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'cus_id' => $loan->Customer_idCustomer,
                    'file' => $slipPath,
                    'payment_amount' => $payment_amount,
                    'loan_id' => $loan_id,
                    'payment_date' => $payment_date,
                    'payment_type' => $request->payment_type,
                    'bank_account_company' => $request->bank_account_company,
                    'cheque_issue_bank' => $request->cheque_issue_bank,
                    'name_on_cheque' => $request->name_on_cheque,
                    'chq_number' => $request->chq_number,
                    'chq_date' => $request->chq_date,
                    'chq_type' => $request->chq_type,
                    'branch_id' => session('branch_id')
                ]);

                $chq_comment='Cheque Received ! Cheque No : '.$request->chq_number.' Cheque Date : '.$request->chq_date.' Cheque Type : '.$request->chq_type.' Amount : '.$request->payment_amount;
                $comment_id=DB::table('loan_comment')->insertGetId([
                    'comment' => $chq_comment,
                    'loan_id' => $loan_id,
                    'user_id' => $user_id,
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                ]);
                $request = new Request([
                    'customer_id' => $loan->Customer_idCustomer,
                    'description' => 'Cheque payment from '.$request->f_name.' '.$request->last_name,
                    'description_id' => $comment_id,
                    'comment' =>$chq_comment,
                    'type' => 'Loan Comment',
                ]);
                $this->customerLogController->store($request);
                return response()->json(['item' => 'success', 'id' => '1', 'test' => "1", 'payment_id' => 0], 200);
            }
        } else {
            if ($payment_type == "Cheque") {
                DB::table('Cheque_payment')
                    ->where('idChq', $cheque_id)
                    ->update([
                        'chq_status' => '1'
                    ]);
                $chq_comment='Cheque Accepted ! Cheque No : '.$request->chq_number.' Cheque Date : '.$request->chq_date.' Cheque Type : '.$request->chq_type.' Amount : '.$request->payment_amount;
                $comment_id=DB::table('loan_comment')->insertGetId([
                    'comment' => $chq_comment,
                    'loan_id' => $loan_id,
                    'user_id' => $user_id,
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                ]);
                $request = new Request([
                    'customer_id' => $loan->Customer_idCustomer,
                    'description' => 'Cheque payment from '.$request->f_name.' '.$request->last_name,
                    'description_id' => $comment_id,
                    'comment' =>$chq_comment,
                    'type' => 'Loan Comment',
                ]);
                $this->customerLogController->store($request);
            }
        }






        $company = DB::table('company')->first();


        $points_to_add = 0;
        if ($company->points === "1") {
            $points_percentage = $company->points_percentage;
            $payment_amount_for_points = $request->payment_amount;

            // Calculate the points to be added
            $points_to_add = ($payment_amount_for_points * $points_percentage) / 100;

            // Retrieve the current points of the customer
            $customer = DB::table('customer')->where('branch_id', session('branch_id'))->where('idCustomer', $loan->Customer_idCustomer)->first();
            $current_points = $customer->points;

            // Update the customer's points
            DB::table('customer')
                ->where('idCustomer', $loan->Customer_idCustomer)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'points' => $current_points + $points_to_add
                ]);
        }


        $truepayment_amount = 0.0;

        $loan = DB::table('customer_loan')
            ->where('idCustomer_Loan', '=', $loan_id)
            ->where('branch_id', session('branch_id'))
            ->where('Status', '=', '0')
            ->get();

        foreach ($loan as $loan_item) {
            $type = $loan_item->type;

            $loan_cate = DB::table('loan_category')->where('branch_id', session('branch_id'))->where('idLoan_Category', '=', $loan_item->Loan_Category_idLoan_Category)->first();
            $enable_saving_process = $loan_cate->enable_saving_process;

            if ($type === "Flat Rate") {

                $installments = DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                    ->where('Status', '=', '0')
                    ->where('branch_id', session('branch_id'))
                    ->orderBy('idInstallments')
                    ->get();

                $slipPath = null;
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $directory = 'payment_slip';

                    // Check if the directory exists on the public disk, create it if not
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Store the file on the public disk
                    $slipPath = Storage::disk('public')->putFile($directory, $file);
                }

                $savedId = 0;
                $comment='-';
                if ($cheque_accept != "0") {
                    $comment='Cheque Accepted ! Cheque No : '.$chq_number.' Cheque Date : '.$chq_date.' Cheque Type : '.$chq_type;
                }
                $savedId = DB::table('customer_payments')->insertGetId([
                    'comment' => $comment,
                    'Date' => $payment_date,
                    'Description' => 'Payment',
                    'Amount' => $payment_amount,
                    'Customer_Loan_idCustomer_Loan' => $loan_id,
                    'User_idUser' => $user_id,
                    'time' => Carbon::now()->format('H:i:s'),
                    'Slip' => $slipPath,
                    'Payment_type' => $payment_type,
                    'branch_id' => session('branch_id')
                ]);




                $customer_loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $loan_id)->first();

                $customer_table = tableWithBranch('customer')
                    ->where('idCustomer', '=', $customer_loan->Customer_idCustomer)
                    ->first();

                $newpayment_amount = number_format($payment_amount, 2);
                DB::table('customer_log')->insert([
                    'customer_id' => $customer_loan->Customer_idCustomer,
                    'customer_name' => $customer_table->First_Name . ' ' . $customer_table->Last_Name,
                    'date' => $payment_date,
                    'time' => date('H:i:s'),
                    'description' => "Payment Amount :({$newpayment_amount})\nLoan Id : {$loan_id}",
                    'description_id' => $loan_id,
                    'comment' => ' ',
                    'type' => 'Payment',
                    'user' => session('userid'),
                    'points' => $points_to_add,
                    'branch_id' => session('branch_id')
                ]);

                $Panalty_Balance_tot_paid = 0;
                $Interest_Balance_tot_paid = 0;
                $capital_balance_tot_paid = 0;
                $Saving_balance_tot_paid = 0;
                $Total_Balance_tot_paid = 0;
                $New_Total_paid = 0;
                $current_payment_amount = $payment_amount;


                foreach ($installments as $item) {
                    if ($payment_amount > 0) {
                        $Panalty_Balance = $item->Panalty_Balance;
                        $Interest_Balance = $item->Interest_Balance;
                        $capital_balance = $item->capital_balance;
                        $Saving_balance = $item->Saving_balance;
                        $Total_Balance = $item->Total_Balance;
                        $Status = $item->Status;


                        $idInstallments = $item->idInstallments;


                        if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance + $Saving_balance)) {

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $Saving_balance;


                            $New_Total_paid = $Total_Balance;
                            $current_payment_amount -= $Total_Balance;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = 0;
                            $Total_Balance = 0;
                            $Status = "1";

                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance)) {

                            $New_Saving_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance + $capital_balance);
                            $New_Saving_balance = $Saving_balance - $New_Saving_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $capital_balance + $New_Saving_payment;

                            $New_Total_Balance = $New_Saving_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $New_Saving_payment;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = $New_Saving_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance)) {

                            $New_Capital_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance);
                            $New_Capital_balance = $capital_balance - $New_Capital_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $New_Capital_payment;

                            $New_Total_Balance = $New_Capital_balance + $Saving_balance;


                            $current_payment_amount -= $New_Total_paid;


                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $New_Capital_payment;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = $New_Capital_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance)) {

                            $New_Interest_payment = $current_payment_amount - ($Panalty_Balance);
                            $New_Interest_balance = $Interest_Balance - $New_Interest_payment;


                            $New_Total_paid = $Panalty_Balance + $New_Interest_payment;

                            $New_Total_Balance = $New_Interest_balance + $Saving_balance + $capital_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $New_Interest_payment;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = $New_Interest_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else {

                            $New_Panelty_payment = $current_payment_amount;
                            $New_Panelty_balance = $Panalty_Balance - $New_Panelty_payment;


                            $New_Total_paid = $New_Panelty_payment;

                            $New_Total_Balance = $Interest_Balance + $Saving_balance + $capital_balance + $New_Panelty_balance;

                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $New_Panelty_payment;
                            $Interest_Balance_tot_paid += 0.00;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;


                            $Panalty_Balance = $New_Panelty_balance;
                            $Total_Balance = $New_Total_Balance;
                        }


                        DB::table('installments')
                            ->where('idInstallments', $idInstallments)
                            ->where('branch_id', session('branch_id'))
                            ->update([
                                'Status' => $Status,
                                'Panelty_status' => '2',
                                'Paid_Amount' => DB::raw('Paid_Amount + ' . $New_Total_paid),
                                'Total_Balance' => $Total_Balance,
                                'Panalty_Balance' => $Panalty_Balance,
                                'Interest_Balance' => $Interest_Balance,
                                'capital_balance' => $capital_balance,
                                'Saving_balance' => $Saving_balance,
                            ]);


                        DB::table('installment_log')->insert([
                            'Installments_idInstallments' => $idInstallments,
                            'Date' => $payment_date . ' ' . $time,
                            'Description' => 'Payment : ' . $payment_amount,
                            'Amount' => $New_Total_paid,
                            'Panalty_Total' => $Panalty_Balance,
                            'Interest_Balance' => $Interest_Balance,
                            'Capital_balance' => $capital_balance,
                            'Saving_balance' => $Saving_balance,
                            'Total_Balance' => $Total_Balance,
                            'User_idUser' => $user_id,
                            'branch_id' => session('branch_id')
                        ]);

                    }
                }


                $loan_log = DB::table('Loan_Log')
                    ->where('Loan_ID', '=', $loan_id)
                    ->orderBy('Loan_Log_ID', 'desc')  // Assuming 'id' is the primary key or auto-increment column
                    ->first();

                $Panelty_Balance_Log = $loan_log->Panelty_Balance;
                $Interest_Balance_Log = $loan_log->Interest_Balance;
                $Capital_Balance_Log = $loan_log->Capital_Balance;
                $Saving_Balance_Log = $loan_log->Saving_Account_Balance;


                $Panelty_Balance_Log -= $Panalty_Balance_tot_paid;
                $Interest_Balance_Log -= $Interest_Balance_tot_paid;
                $Capital_Balance_Log -= $capital_balance_tot_paid;
                $Saving_Balance_Log -= $Saving_balance_tot_paid;
                $Total_Pending_Balance_Log = $Panelty_Balance_Log + $Interest_Balance_Log + $Capital_Balance_Log + $Saving_Balance_Log;


                $this->loanLogController->index(
                    $loan_id, 'Customer Payment', $savedId,
                    'Customer Payment', $payment_amount,
                    $Panalty_Balance_tot_paid, $Interest_Balance_tot_paid,
                    $capital_balance_tot_paid, $Saving_balance_tot_paid, $Panelty_Balance_Log,
                    $Interest_Balance_Log, $Capital_Balance_Log, $Total_Pending_Balance_Log, $Saving_Balance_Log
                );
                $this->capitalBalanceController->index($loan_id);


                $loan_for_bank = tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan_id)
                    ->first();
                $bank_log_comment = "Loan Number : {$loan_for_bank->Loan_No}";
                $bank_account_company = $request->bank_account_company;

                $capital_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_1')
                    ->first();

                $interest_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_2')
                    ->first();

                $panelty_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_5')
                    ->first();


                if ($payment_type === "Bank Deposit") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Collector") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cash") {
                    $bank_account_company = DB::table('company_bank_accounts')
                        ->where('branch_id', session('branch_id'))
                        ->whereRaw('LOWER(Account_No) = ?', ['cash'])  // Case-insensitive comparison
                        ->value('Idbank');
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Cash", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Cash", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Cash", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Cash", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Cash", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Cash", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cheque") {
                    DB::table('cheque_details')->insert([
                        'Date_Time' => date('Y-m-d H:i:s'),
                        'Type' => "Receive",
                        'Company_Account' => $cheque_issue_bank,
                        'Description' => "Customer payment",
                        'Amount' => $payment_amount,
                        'Cheque_No' => $chq_number,
                        'Cheque_Type' => $chq_type,
                        'Name_On_The_Cheque' => $name_on_cheque,
                        'Cheque_Date' => $chq_date,
                        'Status' => '0',
                        'Note' => '-',
                        'Payment_id' => $savedId,
                        'branch_id' => session('branch_id')
                    ]);
                }

                if ($enable_saving_process == "Yes") {
                    $saving_account = tableWithBranch('Customer_Saving_Accounts')
                        ->where('Loan_Id', '=', $loan_id)
                        ->first();
                    $this->SavingAccountController->index($saving_account->id, 'Deposit', 'Payment', $Saving_balance_tot_paid, '0.00', $Saving_balance_tot_paid, 'Credit');
                }
                return response()->json(['item' => 'sucess', 'id' => '1', 'test' => "1", 'payment_id' => $savedId], 200);

            } else if ($type === "Draft") {

                $installments = tableWithBranch('installments')
                    ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                    ->where('Status', '=', '0')
                    ->orderBy('idInstallments')
                    ->get();

                $slipPath = null;
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $directory = 'payment_slip';

                    // Check if the directory exists on the public disk, create it if not
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Store the file on the public disk
                    $slipPath = Storage::disk('public')->putFile($directory, $file);
                }

                $savedId = 0;
                $comment='-';
                if ($cheque_accept != "0") {
                    $comment='Cheque Accepted ! Cheque No : '.$chq_number.' Cheque Date : '.$chq_date.' Cheque Type : '.$chq_type;
                }
                $savedId = DB::table('customer_payments')->insertGetId([
                    'comment' => $comment,
                    'Date' => $payment_date,
                    'Description' => 'Payment',
                    'Amount' => $payment_amount,
                    'Customer_Loan_idCustomer_Loan' => $loan_id,
                    'User_idUser' => $user_id,
                    'time' => Carbon::now()->format('H:i:s'),
                    'Slip' => $slipPath,
                    'Payment_type' => $payment_type,
                    'branch_id' => session('branch_id')
                ]);

                $customer_loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $loan_id)->first();

                $customer_table = tableWithBranch('customer')
                    ->where('idCustomer', '=', $customer_loan->Customer_idCustomer)
                    ->first();

                $newpayment_amount = number_format($payment_amount, 2);
                DB::table('customer_log')->insert([
                    'customer_id' => $customer_loan->Customer_idCustomer,
                    'customer_name' => $customer_table->First_Name . ' ' . $customer_table->Last_Name,
                    'date' => $payment_date,
                    'time' => date('H:i:s'),
                    'description' => "Payment Amount :({$newpayment_amount})\nLoan Id : {$loan_id}",
                    'description_id' => $loan_id,
                    'comment' => ' ',
                    'type' => 'Payment',
                    'user' => session('userid'),
                    'points' => $points_to_add,
                    'branch_id' => session('branch_id')
                ]);

                $Panalty_Balance_tot_paid = 0;
                $Interest_Balance_tot_paid = 0;
                $capital_balance_tot_paid = 0;
                $Saving_balance_tot_paid = 0;
                $Total_Balance_tot_paid = 0;
                $New_Total_paid = 0;
                $current_payment_amount = $payment_amount;


                foreach ($installments as $item) {
                    if ($payment_amount > 0) {
                        $Panalty_Balance = $item->Panalty_Balance;
                        $Interest_Balance = $item->Interest_Balance;
                        $capital_balance = $item->capital_balance;
                        $Saving_balance = $item->Saving_balance;
                        $Total_Balance = $item->Total_Balance;
                        $Status = $item->Status;


                        $idInstallments = $item->idInstallments;


                        if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance + $Saving_balance)) {

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $Saving_balance;


                            $New_Total_paid = $Total_Balance;
                            $current_payment_amount -= $Total_Balance;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = 0;
                            $Total_Balance = 0;
                            $Status = "1";

                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance)) {

                            $New_Saving_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance + $capital_balance);
                            $New_Saving_balance = $Saving_balance - $New_Saving_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $capital_balance + $New_Saving_payment;

                            $New_Total_Balance = $New_Saving_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $New_Saving_payment;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = $New_Saving_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance)) {

                            $New_Capital_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance);
                            $New_Capital_balance = $capital_balance - $New_Capital_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $New_Capital_payment;

                            $New_Total_Balance = $New_Capital_balance + $Saving_balance;


                            $current_payment_amount -= $New_Total_paid;


                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $New_Capital_payment;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = $New_Capital_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance)) {

                            $New_Interest_payment = $current_payment_amount - ($Panalty_Balance);
                            $New_Interest_balance = $Interest_Balance - $New_Interest_payment;


                            $New_Total_paid = $Panalty_Balance + $New_Interest_payment;

                            $New_Total_Balance = $New_Interest_balance + $Saving_balance + $capital_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $New_Interest_payment;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = $New_Interest_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else {

                            $New_Panelty_payment = $current_payment_amount;
                            $New_Panelty_balance = $Panalty_Balance - $New_Panelty_payment;


                            $New_Total_paid = $New_Panelty_payment;

                            $New_Total_Balance = $Interest_Balance + $Saving_balance + $capital_balance + $New_Panelty_balance;

                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $New_Panelty_payment;
                            $Interest_Balance_tot_paid += 0.00;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;


                            $Panalty_Balance = $New_Panelty_balance;
                            $Total_Balance = $New_Total_Balance;
                        }


                        DB::table('installments')
                            ->where('idInstallments', $idInstallments)
                            ->where('branch_id', session('branch_id'))
                            ->update([
                                'Status' => $Status,
                                'Panelty_status' => '2',
                                'Paid_Amount' => DB::raw('Paid_Amount + ' . $New_Total_paid),
                                'Total_Balance' => $Total_Balance,
                                'Panalty_Balance' => $Panalty_Balance,
                                'Interest_Balance' => $Interest_Balance,
                                'capital_balance' => $capital_balance,
                                'Saving_balance' => $Saving_balance,

                            ]);


                        DB::table('installment_log')->insert([
                            'Installments_idInstallments' => $idInstallments,
                            'Date' => $payment_date . ' ' . $time,
                            'Description' => 'Payment : ' . $payment_amount,
                            'Amount' => $New_Total_paid,
                            'Panalty_Total' => $Panalty_Balance,
                            'Interest_Balance' => $Interest_Balance,
                            'Capital_balance' => $capital_balance,
                            'Saving_balance' => $Saving_balance,
                            'Total_Balance' => $Total_Balance,
                            'User_idUser' => $user_id,
                            'branch_id' => session('branch_id')
                        ]);

                    }
                }


                $loan_log = tableWithBranch('Loan_Log')
                    ->where('Loan_ID', '=', $loan_id)
                    ->orderBy('Loan_Log_ID', 'desc')  // Assuming 'id' is the primary key or auto-increment column
                    ->first();

                $Panelty_Balance_Log = $loan_log->Panelty_Balance;
                $Interest_Balance_Log = $loan_log->Interest_Balance;
                $Capital_Balance_Log = $loan_log->Capital_Balance;
                $Saving_Balance_Log = $loan_log->Saving_Account_Balance;
//                $new_payment_amount


                $Panelty_Balance_Log -= $Panalty_Balance_tot_paid;
                $Interest_Balance_Log -= $Interest_Balance_tot_paid;
                $Capital_Balance_Log -= $capital_balance_tot_paid;
                $Saving_Balance_Log -= $Saving_balance_tot_paid;

                $Total_Pending_Balance_Log = $Panelty_Balance_Log + $Interest_Balance_Log + $Capital_Balance_Log + $Saving_Balance_Log;


                $this->loanLogController->index(
                    $loan_id, 'Customer Payment', $savedId,
                    'Customer Payment', $payment_amount,
                    $Panalty_Balance_tot_paid, $Interest_Balance_tot_paid,
                    $capital_balance_tot_paid, $Saving_balance_tot_paid, $Panelty_Balance_Log,
                    $Interest_Balance_Log, $Capital_Balance_Log, $Total_Pending_Balance_Log, $Saving_Balance_Log
                );
                $this->capitalBalanceController->index($loan_id);



                $loan_for_bank = tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan_id)
                    ->first();
                $bank_log_comment = "Loan Number : {$loan_for_bank->Loan_No}";
                $bank_account_company = $request->bank_account_company;

                $capital_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_1')
                    ->first();

                $interest_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_2')
                    ->first();

                $panelty_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_5')
                    ->first();


                if ($payment_type === "Bank Deposit") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Collector") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cash") {
                    $bank_account_company = DB::table('company_bank_accounts')
                        ->where('branch_id', session('branch_id'))
                        ->whereRaw('LOWER(Account_No) = ?', ['cash'])  // Case-insensitive comparison
                        ->value('Idbank');
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Cash", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Cash", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Cash", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Cash", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Cash", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Cash", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cheque") {
                    DB::table('cheque_details')->insert([
                        'Date_Time' => date('Y-m-d H:i:s'),
                        'Type' => "Receive",
                        'Company_Account' => $cheque_issue_bank,
                        'Description' => "Customer payment",
                        'Amount' => $payment_amount,
                        'Cheque_No' => $chq_number,
                        'Cheque_Type' => $chq_type,
                        'Name_On_The_Cheque' => $name_on_cheque,
                        'Cheque_Date' => $chq_date,
                        'Status' => '0',
                        'Note' => '-',
                        'Payment_id' => $savedId,
                        'branch_id' => session('branch_id')
                    ]);
                }




                if ($enable_saving_process == "Yes") {
                    $saving_account = tableWithBranch('Customer_Saving_Accounts')
                        ->where('Loan_Id', '=', $loan_id)
                        ->first();
                    $this->SavingAccountController->index($saving_account->id, 'Deposit', 'Payment', $Saving_balance_tot_paid, '0.00', $Saving_balance_tot_paid, 'Credit');
                }
                return response()->json(['item' => 'sucess', 'id' => '1', 'test' => "1", 'payment_id' => $savedId], 200);


            } else if ($type === "Reducing Balance") {

                $installments = tableWithBranch('installments')
                    ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                    ->where('Status', '=', '0')
                    ->orderBy('idInstallments')
                    ->get();

                $slipPath = null;
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $directory = 'payment_slip';

                    // Check if the directory exists on the public disk, create it if not
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Store the file on the public disk
                    $slipPath = Storage::disk('public')->putFile($directory, $file);
                }

                $savedId = 0;
                $comment='-';
                if ($cheque_accept != "0") {
                    $comment='Cheque Accepted ! Cheque No : '.$chq_number.' Cheque Date : '.$chq_date.' Cheque Type : '.$chq_type;
                }
                $savedId = DB::table('customer_payments')->insertGetId([
                    'comment' => $comment,
                    'Date' => $payment_date,
                    'Description' => 'Payment',
                    'Amount' => $payment_amount,
                    'Customer_Loan_idCustomer_Loan' => $loan_id,
                    'User_idUser' => $user_id,
                    'time' => Carbon::now()->format('H:i:s'),
                    'Slip' => $slipPath,
                    'Payment_type' => $payment_type,
                    'branch_id' => session('branch_id')
                ]);




                $customer_loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $loan_id)->first();

                $customer_table = tableWithBranch('customer')
                    ->where('idCustomer', '=', $customer_loan->Customer_idCustomer)
                    ->first();

                $newpayment_amount = number_format($payment_amount, 2);
                DB::table('customer_log')->insert([
                    'customer_id' => $customer_loan->Customer_idCustomer,
                    'customer_name' => $customer_table->First_Name . ' ' . $customer_table->Last_Name,
                    'date' => $payment_date,
                    'time' => date('H:i:s'),
                    'description' => "Payment Amount :({$newpayment_amount})\nLoan Id : {$loan_id}",
                    'description_id' => $loan_id,
                    'comment' => ' ',
                    'type' => 'Payment',
                    'user' => session('userid'),
                    'points' => $points_to_add,
                    'branch_id' => session('branch_id')
                ]);

                $Panalty_Balance_tot_paid = 0;
                $Interest_Balance_tot_paid = 0;
                $capital_balance_tot_paid = 0;
                $Saving_balance_tot_paid = 0;
                $Total_Balance_tot_paid = 0;
                $New_Total_paid = 0;
                $current_payment_amount = $payment_amount;


                foreach ($installments as $item) {
                    if ($payment_amount > 0) {
                        $Panalty_Balance = $item->Panalty_Balance;
                        $Interest_Balance = $item->Interest_Balance;
                        $capital_balance = $item->capital_balance;
                        $Saving_balance = $item->Saving_balance;
                        $Total_Balance = $item->Total_Balance;
                        $Status = $item->Status;


                        $idInstallments = $item->idInstallments;


                        if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance + $Saving_balance)) {

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $Saving_balance;


                            $New_Total_paid = $Total_Balance;
                            $current_payment_amount -= $Total_Balance;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = 0;
                            $Total_Balance = 0;
                            $Status = "1";

                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance)) {

                            $New_Saving_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance + $capital_balance);
                            $New_Saving_balance = $Saving_balance - $New_Saving_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $capital_balance + $New_Saving_payment;

                            $New_Total_Balance = $New_Saving_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $capital_balance;
                            $Saving_balance_tot_paid += $New_Saving_payment;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = 0;
                            $Saving_balance = $New_Saving_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance)) {

                            $New_Capital_payment = $current_payment_amount - ($Panalty_Balance + $Interest_Balance);
                            $New_Capital_balance = $capital_balance - $New_Capital_payment;


                            $New_Total_paid = $Panalty_Balance + $Interest_Balance + $New_Capital_payment;

                            $New_Total_Balance = $New_Capital_balance + $Saving_balance;


                            $current_payment_amount -= $New_Total_paid;


                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $Interest_Balance;
                            $capital_balance_tot_paid += $New_Capital_payment;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = 0;
                            $capital_balance = $New_Capital_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else if ($current_payment_amount >= ($Panalty_Balance)) {

                            $New_Interest_payment = $current_payment_amount - ($Panalty_Balance);
                            $New_Interest_balance = $Interest_Balance - $New_Interest_payment;


                            $New_Total_paid = $Panalty_Balance + $New_Interest_payment;

                            $New_Total_Balance = $New_Interest_balance + $Saving_balance + $capital_balance;


                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $Panalty_Balance;
                            $Interest_Balance_tot_paid += $New_Interest_payment;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;

                            $Panalty_Balance = 0;
                            $Interest_Balance = $New_Interest_balance;
                            $Total_Balance = $New_Total_Balance;
                        } else {

                            $New_Panelty_payment = $current_payment_amount;
                            $New_Panelty_balance = $Panalty_Balance - $New_Panelty_payment;


                            $New_Total_paid = $New_Panelty_payment;

                            $New_Total_Balance = $Interest_Balance + $Saving_balance + $capital_balance + $New_Panelty_balance;

                            $current_payment_amount -= $New_Total_paid;

                            $Panalty_Balance_tot_paid += $New_Panelty_payment;
                            $Interest_Balance_tot_paid += 0.00;
                            $capital_balance_tot_paid += 0.00;
                            $Saving_balance_tot_paid += 0.00;


                            $Panalty_Balance = $New_Panelty_balance;
                            $Total_Balance = $New_Total_Balance;
                        }


                        DB::table('installments')
                            ->where('idInstallments', $idInstallments)
                            ->where('branch_id', session('branch_id'))
                            ->update([
                                'Status' => $Status,
                                'Panelty_status' => '2',
                                'Paid_Amount' => DB::raw('Paid_Amount + ' . $New_Total_paid),
                                'Total_Balance' => $Total_Balance,
                                'Panalty_Balance' => $Panalty_Balance,
                                'Interest_Balance' => $Interest_Balance,
                                'capital_balance' => $capital_balance,
                                'Saving_balance' => $Saving_balance,
                            ]);


                        DB::table('installment_log')->insert([
                            'Installments_idInstallments' => $idInstallments,
                            'Date' => $payment_date . ' ' . $time,
                            'Description' => 'Payment : ' . $payment_amount,
                            'Amount' => $New_Total_paid,
                            'Panalty_Total' => $Panalty_Balance,
                            'Interest_Balance' => $Interest_Balance,
                            'Capital_balance' => $capital_balance,
                            'Saving_balance' => $Saving_balance,
                            'Total_Balance' => $Total_Balance,
                            'User_idUser' => $user_id,
                            'branch_id' => session('branch_id')
                        ]);

                    }
                }


                $loan_log = tableWithBranch('Loan_Log')
                    ->where('Loan_ID', '=', $loan_id)
                    ->orderBy('Loan_Log_ID', 'desc')  // Assuming 'id' is the primary key or auto-increment column
                    ->first();

                $Panelty_Balance_Log = $loan_log->Panelty_Balance;
                $Interest_Balance_Log = $loan_log->Interest_Balance;
                $Capital_Balance_Log = $loan_log->Capital_Balance;
                $Saving_Balance_Log = $loan_log->Saving_Account_Balance;

//                if ($new_payment_amount>0){
//                    if ($Panelty_Balance_Log<=$new_payment_amount){
//                        $Panalty_Balance_tot_paid=$Panelty_Balance_Log;
//                        $Panelty_Balance_Log=0;
//                        $new_payment_amount-=$Panalty_Balance_tot_paid;
//                    }else{
//                        $Panelty_Balance_Log-=$new_payment_amount;
//                        $Panalty_Balance_tot_paid=$new_payment_amount;
//                        $new_payment_amount=0;
//                    }
//                }
//
//                if ($new_payment_amount>0){
//                    if ($Interest_Balance_Log<=$new_payment_amount){
//                        $Interest_Balance_tot_paid=$Interest_Balance_Log;
//                        $Interest_Balance_Log=0;
//                        $new_payment_amount-=$Interest_Balance_tot_paid;
//                    }else{
//                        $Interest_Balance_Log-=$new_payment_amount;
//                        $Interest_Balance_tot_paid=$new_payment_amount;
//                        $new_payment_amount=0;
//                    }
//                }
//
//                if ($new_payment_amount>0){
//                    if ($Capital_Balance_Log<=$new_payment_amount){
//                        $capital_balance_tot_paid=$Capital_Balance_Log;
//                        $Capital_Balance_Log=0;
//                        $new_payment_amount-=$capital_balance_tot_paid;
//                    }else{
//                        $Capital_Balance_Log-=$new_payment_amount;
//                        $capital_balance_tot_paid=$new_payment_amount;
//                        $new_payment_amount=0;
//                    }
//                }
//
//                if ($new_payment_amount>0){
//                    if ($Saving_Balance_Log<=$new_payment_amount){
//                        $Saving_balance_tot_paid=$Saving_Balance_Log;
//                        $Saving_Balance_Log=0;
//                        $new_payment_amount-=$Saving_balance_tot_paid;
//                    }else{
//                        $Saving_Balance_Log-=$new_payment_amount;
//                        $Saving_balance_tot_paid=$new_payment_amount;
//                        $new_payment_amount=0;
//                    }
//                }
                $Panelty_Balance_Log -= $Panalty_Balance_tot_paid;
                $Interest_Balance_Log -= $Interest_Balance_tot_paid;
                $Capital_Balance_Log -= $capital_balance_tot_paid;
                $Saving_Balance_Log -= $Saving_balance_tot_paid;
                $Total_Pending_Balance_Log = $Panelty_Balance_Log + $Interest_Balance_Log + $Capital_Balance_Log + $Saving_Balance_Log;


                $this->loanLogController->index(
                    $loan_id, 'Customer Payment', $savedId,
                    'Customer Payment', $payment_amount,
                    $Panalty_Balance_tot_paid, $Interest_Balance_tot_paid,
                    $capital_balance_tot_paid, $Saving_balance_tot_paid, $Panelty_Balance_Log,
                    $Interest_Balance_Log, $Capital_Balance_Log, $Total_Pending_Balance_Log, $Saving_Balance_Log
                );
                $this->capitalBalanceController->index($loan_id);


                $loan_for_bank = tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $loan_id)
                    ->first();
                $bank_log_comment = "Loan Number : {$loan_for_bank->Loan_No}";
                $bank_account_company = $request->bank_account_company;

                $capital_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_1')
                    ->first();

                $interest_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_2')
                    ->first();

                $panelty_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_5')
                    ->first();


                if ($payment_type === "Bank Deposit") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Bank Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Bank Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Bank Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Collector") {
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Collector Deposit", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Collector Deposit", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Collector Deposit", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cash") {
                    $bank_account_company = DB::table('company_bank_accounts')
                        ->where('branch_id', session('branch_id'))
                        ->whereRaw('LOWER(Account_No) = ?', ['cash'])  // Case-insensitive comparison
                        ->value('Idbank');
                    if ($capital_balance_tot_paid>0){
                        //capital
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Capital", $bank_log_comment, "Cash", "debit", $capital_balance_tot_paid,$savedId);
                        $this->bankLogController->index($capital_id->Idbank, "Loan Payment-Capital", $bank_log_comment, "Cash", "credit", $capital_balance_tot_paid,$savedId);
                    }

                    if($Interest_Balance_tot_paid>0){
                        //interest
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Interest", $bank_log_comment, "Cash", "debit", $Interest_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($interest_id->Idbank, "Loan Payment-Interest", $bank_log_comment, "Cash", "credit", $Interest_Balance_tot_paid,$savedId);
                    }

                    if($Panalty_Balance_tot_paid>0){
                        //panelty
                        $this->bankLogController->index($bank_account_company, "Loan Payment-Penalty", $bank_log_comment, "Cash", "debit", $Panalty_Balance_tot_paid,$savedId);
                        $this->bankLogController->index($panelty_id->Idbank, "Loan Payment-Penalty", $bank_log_comment, "Cash", "credit", $Panalty_Balance_tot_paid,$savedId);
                    }
                } else if ($payment_type === "Cheque") {
                    DB::table('cheque_details')->insert([
                        'Date_Time' => date('Y-m-d H:i:s'),
                        'Type' => "Receive",
                        'Company_Account' => $cheque_issue_bank,
                        'Description' => "Customer payment",
                        'Amount' => $payment_amount,
                        'Cheque_No' => $chq_number,
                        'Cheque_Type' => $chq_type,
                        'Name_On_The_Cheque' => $name_on_cheque,
                        'Cheque_Date' => $chq_date,
                        'Status' => '0',
                        'Note' => '-',
                        'Payment_id' => $savedId,
                        'branch_id' => session('branch_id')
                    ]);
                }

                if ($enable_saving_process == "Yes") {
                    $saving_account = tableWithBranch('Customer_Saving_Accounts')
                        ->where('Loan_Id', '=', $loan_id)
                        ->first();
                    $this->SavingAccountController->index($saving_account->id, 'Deposit', 'Payment', $Saving_balance_tot_paid, '0.00', $Saving_balance_tot_paid, 'Credit');
                }
                return response()->json(['item' => 'sucess', 'id' => '1', 'test' => "1", 'payment_id' => $savedId], 200);


            } else {

            }
        }

    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $recovery_officer = tableWithBranch('user')->where('collector', '=', '1')->get();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $route = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        return view('pages.LatePayment', compact('group', 'lending_officer', 'recovery_officer', 'center', 'customers', 'route'));
    }


    public function collector()
    {
        // Aggregate unique route names for each recovery officer
        $load = tableWithBranch('user','user')
            ->leftJoin('collector_has_route', 'collector_has_route.collector_id', '=', 'user.id')
            ->leftJoin('route', 'collector_has_route.route_id', '=', 'route.id_route')
            ->leftJoin('customer', function ($join) {
                $join->on('customer.route_id', '=', 'route.id_route');
            })
            ->leftJoin('customer_loan', function ($join) {
                $join->on('customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                    ->on('customer_loan.collector_id', '=', 'user.id'); // Ensure loan is for the specific collector
            })
            ->where('user.collector', '=', '1') // Only for collectors
            ->select(
                'user.Full_Name as recovery_officer',
                'user.id as user_id',
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(route.Name, "-") SEPARATOR ", ") as route_names'), // Use DISTINCT to avoid duplicate route names
                DB::raw('COUNT(customer_loan.idCustomer_Loan) as loan_count'), // Count loans without DISTINCT for accuracy
                DB::raw('COALESCE(SUM(customer_loan.Amount), 0.00) as loan_amount') // Sum loan amounts without DISTINCT
            )
            ->groupBy('user.id', 'user.Full_Name') // Group by user
            ->get();

        $center = tableWithBranch('center')->get();
        $routes = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();

        $recovery_officer = tableWithBranch('user')
            ->where('collector', '=', '1')
            ->get();

        return view('pages.ChangeCollector', compact('recovery_officer', 'load', 'center', 'routes'));
    }


    public function collector_filter(Request $request)
    {
        $route = $request->route;
        $recovery = $request->recovery;

        $load = tableWithBranch('user', 'user')
            ->leftJoin('customer_loan', 'customer_loan.collector_id', '=', 'user.id')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('collector_has_route', 'collector_has_route.collector_id', '=', 'user.id')
            ->leftJoin('route', 'collector_has_route.route_id', '=', 'route.id_route')
            ->where('collector', '=', '1')
            ->when($route != 0, function ($query) use ($route) {
                return $query->where('route.id_route', $route);
            })
            ->when($recovery, function ($query) use ($recovery) {
                return $query->where('user.id', $recovery);
            })
            ->select(
                'user.Full_Name as recovery_officer',
                'user.id as user_id',
                DB::raw('COALESCE(route.Name, "-") as route_name'),
                DB::raw('CASE WHEN MAX(route.id_route) IS NULL THEN 0 ELSE COUNT(customer_loan.idCustomer_Loan) END as loan_count'),
                DB::raw('CASE WHEN MAX(route.id_route) IS NULL THEN 0.00 ELSE SUM(customer_loan.Amount) END as loan_amount')
            )
            ->groupBy('user.id', 'user.Full_Name', 'route_name')
            ->get();

        $center = tableWithBranch('center')->get();
        $routes = tableWithBranch('route', 'route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();

        $recovery_officer = tableWithBranch('user')
            ->where('collector', '=', '1')
            ->get();

        return view('pages.ChangeCollector', compact('recovery_officer', 'load', 'center', 'routes', 'route', 'recovery'));
    }

    public function updateRoute(Request $request)
    {
        $request->validate([
            'officer_id' => 'required',
            'route_ids' => 'required', // Validate as an array
        ]);

        try {
            $officerId = $request->officer_id;
            $routeIds = $request->route_ids;

            // Remove existing routes for this officer
            DB::table('collector_has_route') ->where('branch_id', session('branch_id'))->where('collector_id', $officerId)->delete();

            // Insert new routes
            $data = array_map(function ($routeId) use ($officerId) {
                return [
                    'collector_id' => $officerId,
                    'route_id' => $routeId,
                    'branch_id' => session('branch_id')
                ];
            }, $routeIds);

            DB::table('collector_has_route')->insert($data);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $today = date('Y-m-d');
        $date_90_days_ago = date('Y-m-d', strtotime('-90 days', strtotime($today)));
        $date_60_days_ago = date('Y-m-d', strtotime('-60 days', strtotime($today)));
        $date_30_days_ago = date('Y-m-d', strtotime('-30 days', strtotime($today)));

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
            ->where('installments.Status', '=', '0')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer.idCustomer',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('ROUND(SUM(installments.capital_balance + installments.interest_balance), 2) as Installment_Balance'),
                DB::raw('ROUND(SUM(installments.Panalty_Balance), 2) as Panalty_Balance'),
                DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                DB::raw('DATEDIFF(NOW(), installments.Installment_Date) as Late_Days')
            )
            ->groupBy(
                'customer.idCustomer',
                'center.No',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Nic',
                'customer_loan.Loan_No',
                'customer_loan.type',
                'customer_loan.idCustomer_Loan',
                'subquery.group_name',
                'installments.Installment_Date'
            );

        $loan_90_days = (clone $loanQuery)
            ->whereDate('installments.Installment_Date', '<', $date_90_days_ago)
            ->orderBy('Late_Days', 'desc') // Order by Late_Days descending
            ->get();

        $loan_60_days = (clone $loanQuery)
            ->whereDate('installments.Installment_Date', '<', $date_60_days_ago)
            ->whereDate('installments.Installment_Date', '>=', $date_90_days_ago)
//            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_90_days->pluck('idCustomer_Loan')->toArray())
            ->orderBy('Late_Days', 'desc') // Order by Late_Days descending
            ->get();

        $loan_30_days = (clone $loanQuery)
            ->whereDate('installments.Installment_Date', '<', $date_30_days_ago)
            ->whereDate('installments.Installment_Date', '>=', $date_60_days_ago)
//            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_90_days->pluck('idCustomer_Loan')->toArray())
//            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_60_days->pluck('idCustomer_Loan')->toArray())
            ->orderBy('Late_Days', 'desc') // Order by Late_Days descending
            ->get();

        $loan_less_30_days = (clone $loanQuery)
            ->whereDate('installments.Installment_Date', '>=', $date_30_days_ago)
            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_90_days->pluck('idCustomer_Loan')->toArray())
            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_60_days->pluck('idCustomer_Loan')->toArray())
            ->whereNotIn('customer_loan.idCustomer_Loan', $loan_30_days->pluck('idCustomer_Loan')->toArray())
            ->orderBy('Late_Days', 'desc') // Order by Late_Days descending
            ->get();

        $All = (clone $loanQuery)
            ->whereDate('installments.Installment_Date', '<', $today)
            ->orderBy('Late_Days', 'desc') // Order by Late_Days descending
            ->get();

        return response()->json([
            'loan_90_days' => $loan_90_days,
            'loan_60_days' => $loan_60_days,
            'loan_30_days' => $loan_30_days,
            'loan_less_30_days' => $loan_less_30_days,
            'All' => $All,
            'message' => 'all'
        ], 200);
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

    public function view_payment()
    {
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $company = DB::table('company')->first();
        $user = tableWithBranch('user')->get();
        $loan = tableWithBranch('customer_loan')->whereIn('Status', [0, 1])->get();
        return view('pages.ViewPayment', compact('group', 'loan', 'center', 'customers', 'company', 'user'));
    }

    public function view_DateWise_CashFlow()
    {
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $company = DB::table('company')->first();
        $user = tableWithBranch('user')->get();
        return view('pages.ViewDateWiseCashFlow', compact('group', 'center', 'customers', 'company', 'user'));
    }

    public function loadMonthlyCollectionSummary($center)
    {

        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'center.No',
                'customer.cus_number',
                'customer_group.Group_No as groupNo',
                'customer.First_Name as First_Name',
                'customer.Last_Name as Last_Name',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.Amount as capitalAmount',
                'customer_loan.capital_balance as capitalBalance',
                'installments.Total_Amount as installment',
                'customer_loan.Balance_Amount',
                'installments.Total_Balance as balance',
                'installments.Paid_Amount as paid',
                'installments.Installment_Date',
                'customer_loan.idCustomer_Loan'
            );

        if ($center != '0') {
            $loanQuery->where('center.idCenter', '=', $center);
        }

        $installments = $loanQuery->get();

        $items = [];

        foreach ($installments as $installment) {
            $centerNo = $installment->No;
            $groupNo = $installment->groupNo;
            $memberNo = $installment->cus_number;
            $memberName = $installment->First_Name . ' ' . $installment->Last_Name;
            $loanAmount = $installment->Total_Loan_Amount;
            $installmentAmount = $installment->installment;
            $loanBalance = $installment->Balance_Amount;
            $paymentDate = $installment->Installment_Date;
            $capitalBalance = $installment->capitalBalance;
            $capitalAmount = $installment->capitalAmount;
            $Installmentbalance = $installment->balance;
            $Installmentpaid = $installment->paid;

            // Unique key combining member number and loan id
            $uniqueKey = $memberNo . '_' . $installment->idCustomer_Loan;

            if (!isset($items[$uniqueKey])) {
                // If not found, create a new entry
                $items[$uniqueKey] = [
                    'centerNo' => $centerNo,
                    'groupNo' => $groupNo,
                    'memberNo' => $memberNo,
                    'memberName' => $memberName,
                    'loanAmount' => $loanAmount,
                    'capitalAmount' => $capitalAmount,
                    'capitalBalance' => $capitalBalance,
                    'installment' => $installmentAmount,
                    'installment_Balance' => $Installmentbalance,
                    'installment_Paid' => $Installmentpaid,
                    'loanBalance' => $loanBalance,
                    'payments' => [
                        $paymentDate => number_format($installmentAmount, 2),
                    ],
                    'payids' => [
                        $paymentDate => number_format($Installmentpaid, 2),
                    ],
                    'pay_Balances' => [
                        $paymentDate => number_format($Installmentbalance, 2),
                    ],
                ];
            } else {
                // If found, update the existing entry

                // Update payments
                if (!isset($items[$uniqueKey]['payments'][$paymentDate])) {
                    $items[$uniqueKey]['payments'][$paymentDate] = number_format($installmentAmount, 2);
                } else {
                    $uniqueDateKey = $paymentDate . '_' . $installment->idCustomer_Loan;
                    $items[$uniqueKey]['payments'][$uniqueDateKey] = number_format($installmentAmount, 2);
                }

                // Update pay_Balances
                if (!isset($items[$uniqueKey]['pay_Balances'][$paymentDate])) {
                    $items[$uniqueKey]['pay_Balances'][$paymentDate] = number_format($Installmentbalance, 2);
                } else {
                    $uniqueDateKey = $paymentDate . '_' . $installment->idCustomer_Loan;
                    $items[$uniqueKey]['pay_Balances'][$uniqueDateKey] = number_format($Installmentbalance, 2);
                }

                // Update payids
                if (!isset($items[$uniqueKey]['payids'][$paymentDate])) {
                    $items[$uniqueKey]['payids'][$paymentDate] = number_format($Installmentpaid, 2);
                } else {
                    $uniqueDateKey = $paymentDate . '_' . $installment->idCustomer_Loan;
                    $items[$uniqueKey]['payids'][$uniqueDateKey] = number_format($Installmentpaid, 2);
                }
            }
        }

        // Re-index items to be a simple array
        $items = array_values($items);

        return response()->json(['items' => $items]);
    }


    public function MonthlyCollectionSummary()
    {
        $center = tableWithBranch('center')->get();
        return view('pages.MonthlyCollectionSummary', compact('center'));
    }


    public function view_DateWise_CashFlow_Data(Request $request)
    {
        $center_details = $request->center_details;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        // Base query for loans
        $baseQuery = tableWithBranch('customer_loan','customer_loan')
            ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->whereBetween('customer_loan.Date_Time', [$date_from, $date_to])
            ->select(
                'center.Name as center_name',
                'customer_group.Name as group_name',
                'customer.cus_number as customer_number',
                'customer_loan.Loan_No as loan_number',
                DB::raw('CONCAT(customer.Title, " ", customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_loan.Date_Time as date',
                'customer_loan.Amount as loan_amount',
                'customer_loan.Total_Other_Amount as total_other_amount'
            );

        // Add center filter if center_details is not 0
        if ($center_details != 0) {
            $baseQuery->where('center.idCenter', $center_details);
        }

        // Fetch loan issues
        $loanIssues = $baseQuery->clone()
            ->where('customer_loan.Amount', '>', 0)
            ->get()
            ->map(function ($loan) {
                return (object)[
                    'center_name' => $loan->center_name,
                    'group_name' => $loan->group_name,
                    'transaction_type' => 'Loan Issue',
                    'customer_number' => $loan->customer_number,
                    'loan_number' => $loan->loan_number,
                    'customer_name' => $loan->customer_name,
                    'date' => $loan->date,
                    'amount' => $loan->loan_amount,
                ];
            });

        // Fetch other charges
        $otherCharges = $baseQuery->clone()
            ->where('customer_loan.Total_Other_Amount', '>', 0)
            ->get()
            ->map(function ($charge) {
                return (object)[
                    'center_name' => $charge->center_name,
                    'group_name' => $charge->group_name,
                    'transaction_type' => 'Other Charges',
                    'customer_number' => $charge->customer_number,
                    'loan_number' => $charge->loan_number,
                    'customer_name' => $charge->customer_name,
                    'date' => $charge->date,
                    'amount' => $charge->total_other_amount,
                ];
            });

        // Fetch customer payments
        $customerPayments = tableWithBranch('customer_loan','customer_loan')
            ->join('customer_payments', 'customer_loan.idCustomer_Loan', '=', 'customer_payments.Customer_Loan_idCustomer_Loan')
            ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->whereBetween('customer_payments.Date', [$date_from, $date_to])
            ->select(
                'center.Name as center_name',
                'customer_group.Name as group_name',
                DB::raw('"Customer Payment" as transaction_type'),
                'customer.cus_number as customer_number',
                'customer_loan.Loan_No as loan_number',
                DB::raw('CONCAT(customer.Title, ".", customer.First_Name, " ", customer.Last_Name) as customer_name'),
                'customer_payments.Date as date',
                'customer_payments.Amount as amount'
            );

        // Add center filter if center_details is not 0
        if ($center_details != 0) {
            $customerPayments->where('center.idCenter', $center_details);
        }

        $customerPaymentsData = $customerPayments->get();

        // Combine all data
        $data = $loanIssues->merge($otherCharges)->merge($customerPaymentsData);

        // Order by date
        $data = $loanIssues->merge($otherCharges)->merge($customerPaymentsData)
            ->sortBy(function ($item) {
                return [$item->date, $item->loan_number]; // Sort by date, then loan number
            })->values(); // Reset the keys after sorting

        return response()->json(['items' => $data], 200);
    }


    public function date_wise_installment_view()
    {
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $company = DB::table('company')->first();
        return view('pages.DateWiseInstallmentView', compact('group', 'center', 'customers', 'company'));
    }


    public function view_payment_load(Request $request)
    {
        $date = $request->date;
        $date_to = $request->date_to;
        $center_details = $request->center_details;
        $group = $request->group;
        $customer = $request->customer;
        $user = $request->user;
        $loan_number_search = $request->loan_number_search;

        $loanQuery = tableWithBranch('customer_payments','customer_payments')
            ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_payments.*',
                'customer.cus_number as cus_number',
                'customer_loan.Loan_No as Loan_No',
                'center.Name as center_name',
                'center.Location as Location',
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer_group.Group_No as group_no',
                'customer_group.Name as group_name',
                'user.Full_Name as Full_Name',
                'customer_payments.idCustomer_Payments as Inv_no'
            );

        // Check if search_box is provided, if yes, ignore the date range filter
        if (!empty($loan_number_search)) {
            $loanQuery->where('customer_loan.idCustomer_Loan', '=', $loan_number_search);
        }


        // Apply filters if not equal to '0'
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
        }

        if ($customer != '0') {
            $loanQuery->where('customer.idCustomer', '=', $customer);
        }

        if ($user != '0') {
            $loanQuery->where('user.id', '=', $user);
        }
        $loanQuery->whereBetween('customer_payments.date', [$date, $date_to]);
        // Fetch the results
        $loan = $loanQuery->get();

        $user_id = (int)session('userid');
        $payment_delete=tableWithBranch('user')->where('id','=',$user_id)->first();
        $payment_delete_status=0;
        if ($payment_delete){
            $payment_delete_status=$payment_delete->payment_delete;
        }

        return response()->json(['item' => $loan, 'test' => $date,'payment_delete_status'=>$payment_delete_status], 200);
    }


    public function view_date_wise_installment(Request $request)
    {
        $date = $request->date;
        $date_2 = $request->date_to;
        $center_details = $request->center_details;
        $group = $request->group;

// Ensure dates are in the correct format (Y-m-d)
        try {
            $date = Carbon::createFromFormat('Y-m-d', $date)->startOfDay()->toDateString();
            $date_2 = Carbon::createFromFormat('Y-m-d', $date_2)->endOfDay()->toDateString();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $loanQuery = tableWithBranch('installments','installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->whereBetween('Installment_Date', [$date, $date_2])
            ->where('Total_Balance', '>', 0)
            ->select('installments.*', 'customer.cus_number as cus_number', 'customer_loan.Loan_No as Loan_No', 'center.Name as center_name', 'customer.First_Name as customer_name', 'customer.Last_Name as customer_lastname', 'customer.Nic as NIC', 'customer_group.Group_No as group_no', 'customer_group.Name as group_name');

        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        if ($group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
        }

        $loan = $loanQuery->get();

// Debugging: Log the query for inspection
        Log::info($loanQuery->toSql(), $loanQuery->getBindings());

        return response()->json(['item' => $loan, 'test' => $date], 200);

    }


    public function addSlip(Request $request)
    {

        $paymentId = $request->input('payment_id');

        // Check if the directory exists on the public disk, create it if not
        $directory = 'payment_slip';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store the file on the public disk
        $file = $request->file('file');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $documentPath = Storage::disk('public')->putFileAs($directory, $file, $fileName);

        // Update the Slip column in the database
        DB::table('customer_payments')
            ->where('branch_id', session('branch_id'))
            ->where('idCustomer_Payments', $paymentId)
            ->update(['Slip' => $documentPath]);

        return response()->json(['message' => 'Slip uploaded successfully!'], 200);
    }


    public function update_reduce_balance(Request $request)
    {
        $user_id = (int)session('userid');
        $installmentsData = $request->input('installments');
        $payment_amount = $request->input('payment_amount');

        // Initialize total variables
        $Panalty_Balance_tot_paid = 0;
        $Interest_Balance_tot_paid = 0;
        $capital_balance_tot_paid = 0;
        $Saving_balance_tot_paid = 0;

// Initialize log variables
        $Panelty_Balance_Log = 0;
        $Interest_Balance_Log = 0;
        $Capital_Balance_Log = 0;
        $Total_Pending_Balance_Log = 0;
        $Saving_Balance_Log = 0;

        foreach ($installmentsData as $data) {
            $id = $data['id'];
            $amount = $data['installmentAmount'];
            DB::table('installments')
                ->where('idInstallments', $id)
                ->update([
                    'Installment_Amount' => $amount,
                    'capital_amount' => $data['capitalAmount'],
                    'interest_amount' => $data['interestAmount'],
                    'Panalty_Amount' => $data['penaltyAmount'],
                    'Saving_amount' => $data['savingAmount'],
                    'Total_Amount' => $data['totalAmount'],
                    'Paid_Amount' => $data['paidAmount'],
                    'Panalty_Balance' => $data['penaltyBalance'],
                    'Interest_Balance' => $data['installmentBalance'],
                    'Saving_balance' => $data['savingBalance'],
                    'Total_Balance' => $data['totalBalance'],
                    'Paid_Date' => date('Y-m-d'),
                ]);

            // Sum up the total paid amounts
            $Panalty_Balance_tot_paid += $data['penaltyBalance'];
            $Interest_Balance_tot_paid += $data['installmentBalance'];
            $capital_balance_tot_paid += $data['capitalAmount'];
            $Saving_balance_tot_paid += $data['savingBalance'];

            // Add log entries (for example)
            $Panelty_Balance_Log += $data['penaltyBalance'];
            $Interest_Balance_Log += $data['installmentBalance'];
            $Capital_Balance_Log += $data['capitalAmount'];
            $Total_Pending_Balance_Log += $data['totalBalance'];
            $Saving_Balance_Log += $data['savingBalance'];
        }

        $loan = DB::table('installments')
            ->where('idInstallments', $id)
            ->first();
        $loan_id = $loan->Customer_Loan_idCustomer_Loan;

        DB::table('customer_loan')
            ->where('idCustomer_Loan', $loan_id)
            ->update([
                'capital_balance' => $request->new_loan_capital_balance
            ]);

        $slipPath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $directory = 'payment_slip';

            // Check if the directory exists on the public disk, create it if not
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the file on the public disk
            $slipPath = Storage::disk('public')->putFile($directory, $file);
        }

        $savedId = DB::table('customer_payments')->insertGetId([
            'Date' => date('Y-m-d'),
            'Description' => 'Reduce Capital',
            'Amount' => $payment_amount,
            'Customer_Loan_idCustomer_Loan' => $loan_id,
            'User_idUser' => $user_id,
            'time' => Carbon::now()->format('H:i:s'),
            'Slip' => $slipPath,
            'Payment_type' => 'Cash',
        ]);

        // Now pass these calculated values to your loanLogController
        $this->loanLogController->index(
            $loan_id, 'Reduce Capital', $savedId,
            'Reduce Capital Payment', $payment_amount,
            $Panalty_Balance_tot_paid, $Interest_Balance_tot_paid,
            $capital_balance_tot_paid, $Saving_balance_tot_paid,
            $Panelty_Balance_Log, $Interest_Balance_Log,
            $Capital_Balance_Log, $Total_Pending_Balance_Log,
            $Saving_Balance_Log
        );


        return response()->json(['message' => 'Installments updated successfully', 'payment_id' => $savedId]);
    }


    public function fetchLoanLog($loanId)
    {
        // Fetch loan log data from Loan_Log table based on Loan_ID
        $loanLogs = DB::table('Loan_Log')
            ->where('Loan_ID', $loanId)
            ->get();

        // Return the data as a JSON response
        return response()->json($loanLogs);
    }


    public function payment_reciept($id, $status)
    {
        $customer_payment = DB::table('customer_payments')->where('idCustomer_Payments', '=', $id)->first();
        $user = DB::table('user')->where('id', '=', $customer_payment->User_idUser)->first();
        $loan = DB::table('customer_loan')->where('idCustomer_Loan', '=', $customer_payment->Customer_Loan_idCustomer_Loan)->first();
        $customer = DB::table('customer')->where('idCustomer', '=', $loan->Customer_idCustomer)->first();

        $cheque_details = "";
        if ($customer_payment->Payment_type == "Cheque") {
            $cheque_details = DB::table('cheque_details')
                ->where('Payment_id', '=', $id)
                ->first();
        }

        $company = DB::table('company')->first();
        $points_message = "";
        $points_to_add = 0;
        if ($company->points === "1") {
            // Retrieve the points percentage and payment amount
            $points_percentage = $company->points_percentage;
            $payment_amount_for_points = $customer_payment->Amount;

            // Calculate the points to be added
            $points_to_add = ($payment_amount_for_points * $points_percentage) / 100;

            // Retrieve the current points of the customer
            $customer = DB::table('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
            $current_points = $customer->points;
            // Prepare the points message
            $points_message = "\n\nCongratulations!\nYou have earned " . number_format($points_to_add, 2) . " points for this transaction. Your total loyalty points are now " . number_format($current_points + $points_to_add, 2) . ".";
        }

        $sms_template = DB::table('sms_template')->where('type', '=', 'loan_payment')->where('status', '=', '1')->first();
        if ($sms_template) {
            $customer = DB::table('customer')->where('idCustomer', '=', $loan->Customer_idCustomer)->first();
            $placeholders = [
                '@Member_No@' => $customer->cus_number,
                '@Member_Name@' => $customer->First_Name . ' ' . $customer->Last_Name,
                '@Loan_No@' => $loan->Loan_No,
                '@Payment_Date@' => $customer_payment->Date,
                '@Paid_Amount@' => number_format($customer_payment->Amount, 2, '.', ','),
                '@Loan_Balance@' => number_format($loan->Balance_Amount, 2, '.', ','),
                '@Capital_Balance@' => number_format($loan->capital_balance, 2, '.', ','),
            ];

            // Step 3: Replace placeholders in the loan_format
            $loan_number_txt = $sms_template->template;
            foreach ($placeholders as $placeholder => $value) {
                $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
            }

            // Append the points message if applicable
            $loan_number_txt .= $points_message;


            if ($status == '1') {
// Log the SMS message
                $this->smsLogController->index($loan->Customer_idCustomer, $loan_number_txt, "Customer Loan Payment");
            }

        }

        $panelty_balance = DB::table('installments')->where('Customer_Loan_idCustomer_Loan', '=', $customer_payment->Customer_Loan_idCustomer_Loan)->sum('Panalty_Balance');
        $tot_balance = DB::table('installments')->where('Customer_Loan_idCustomer_Loan', '=', $customer_payment->Customer_Loan_idCustomer_Loan)->sum('Total_Balance');


        return response()->json(['tot_balance'=>$tot_balance,'panelty_balance'=>$panelty_balance,'payment' => $customer_payment, 'cheque_details' => $cheque_details, 'loan' => $loan, 'customer' => $customer, 'user' => $user, 'points_to_add' => $points_to_add, 'point_check' => $company->points], 200);
    }


    public function daily_repayment()
    {
        $user = DB::table('user')
            ->where('collector', '=', '1')
            ->get();
        $lending_officer = DB::table('user')
            ->where('lending_officer', '=', '1')
            ->get();
        return view('pages.DailyCollection', compact('user', 'lending_officer'));
    }


    public function dailycollection(Request $request)
    {

        $collector = $request->collector;
        $lending_officer = $request->lending_officer;


        $today = date('Y-m-d');
        $collector_user = DB::table('user')->where('collector', '=', '1')->get();


        $check = DB::table('collector_data')->where('date', '=', $today)->get();
        if (!$check->isEmpty()) {

        } else {

            $loanQuery = DB::table('installments')
                ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
         FROM group_has_customer
         LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->join('user', 'customer_loan.collector_id', '=', 'user.id')
                ->where('installments.Status', '=', '0')
                ->where('customer_loan.Status', '=', '0')
                ->select(
                    'customer.idCustomer',
                    DB::raw('IFNULL(center.No, "-") as center_no'),
                    'customer.First_Name as customer_name',
                    'customer.Last_Name as customer_lastname',
                    'customer.Nic as NIC',
                    'customer_loan.Loan_No as Loan_No',
                    'customer_loan.idCustomer_Loan as idCustomer_Loan',
                    'customer_loan.type as type',
                    'user.Full_Name as collector',
                    'user.Full_Name as collector_show',
                    'user.id as collector_id',
                    'customer_loan.Vehicle_No as Vehicle_No',
                    'customer_loan.idCustomer_Loan as idCustomer_Loan',
                    DB::raw('COUNT(installments.idInstallments) as Installment_Count'),
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                    DB::raw("
    ROUND(
        SUM(
            CASE 
                WHEN installments.Installment_Date < '$today' 
                THEN (installments.Interest_Balance + installments.capital_balance + installments.Saving_balance)
                ELSE 0 
            END
        ), 2
    ) as Installment_Balance_Before_Today
"),

                    DB::raw("ROUND(SUM(CASE WHEN installments.Installment_Date < '$today' THEN installments.Panalty_Balance ELSE 0 END), 2) as Panalty_Balance_Before_Today"),
                    DB::raw('ROUND(SUM(installments.Total_Balance), 2) as Total_Balance'),
                    DB::raw("ROUND(SUM(CASE WHEN installments.Installment_Date = '$today' THEN installments.Installment_Amount ELSE 0 END), 2) as Today_Installment")
                )
                ->groupBy(
                    'customer.idCustomer',
                    'center.No',
                    'customer.First_Name',
                    'customer.Last_Name',
                    'customer.Nic',
                    'customer_loan.Loan_No',
                    'customer_loan.type',
                    'customer_loan.Vehicle_No',
                    'customer_loan.idCustomer_Loan',
                    'user.Full_Name',
                    'user.id',
                    'subquery.group_name'
                );
            $loan = $loanQuery->get();
//            return response()->json(['array' => $loan], 200);
            foreach ($loan as $data) {
                DB::table('collector_data')->insert([
                    'date' => date('Y-m-d'),
                    'loan_id' => $data->idCustomer_Loan,
                    'Loan_no' => $data->Loan_No,
                    'center_no' => $data->center_no,
                    'group_no' => $data->group_name,
                    'mem_nic' => $data->NIC,
                    'f_name' => $data->customer_name,
                    'l_name' => $data->customer_lastname,
                    'ins_amount' => $data->Installment_Balance_Before_Today,
                    'panelty_amount' => $data->Panalty_Balance_Before_Today,
                    'pending_total' => $data->Installment_Balance_Before_Today,
                    'collector_name' => $data->collector,
                    'current_collector' => $data->collector_show,
                    'collector_id' => $data->collector_id,
                    'Today_Installment' => $data->Today_Installment,
                    'selected_user' => $data->collector_id,
                ]);
            }


        }


        $query = DB::table('collector_data')
            ->join('customer_loan', 'customer_loan.idCustomer_Loan', '=', 'collector_data.loan_id')
            ->join('user', 'user.id', '=', 'customer_loan.lending_officer_id')
            ->where('collector_data.date', '=', $today)
            ->select(
                'collector_data.f_name as customer_name',
                'collector_data.l_name as customer_lastname',
                'collector_data.mem_nic as NIC',
                'collector_data.loan_no as Loan_No',
                'collector_data.loan_id as idCustomer_Loan',
                'collector_data.group_no as group_name',
                'collector_data.ins_amount as Installment_Balance_Before_Today',
                'collector_data.panelty_amount as Panalty_Balance_Before_Today',
                'collector_data.pending_total as Total_Balance',
                'collector_data.collector_name as collector',
                'collector_data.Today_Installment as Today_Installment',
                'collector_data.current_collector as collector_show',
                'collector_data.collector_id as collector_id',
                'collector_data.center_no as center_no',
                'user.Full_Name as lending'
            );

// Apply filters based on the conditions
        if ($collector != "0") {
            $query->where('selected_user', '=', $collector);
        }

        if ($lending_officer != "0") {
            $query->where('user.id', '=', $lending_officer);
        }

        $collectorData = $query->get();

        if (!$collectorData->isEmpty()) {
            return response()->json(['item' => $collectorData, 'collector' => $collector_user, 'message' => 'collector'], 200);
        }


    }


    public function change_collector(Request $request)
    {
        try {
            $tableData = $request->input('tableData');
            $collector = $request->input('collector');

            DB::table('collector_data')
                ->where('date', '=', date('Y-m-d'))
//                ->where('collector_id', '=', $collector)
                ->delete();

            foreach ($tableData as $data) {
                DB::table('collector_data')->insert([
                    'date' => date('Y-m-d'),
                    'loan_id' => $data['idCustomer_Loan'] ?? null,
                    'Loan_no' => $data['Loan_No'] ?? null,
                    'center_no' => $data['center_no'] ?? null,
                    'group_no' => $data['group_name'] ?? null,
                    'mem_nic' => $data['NIC'] ?? null,
                    'f_name' => $data['customer_name'] ?? null,
                    'l_name' => $data['customer_lastname'] ?? null,
                    'ins_amount' => $data['Installment_Balance'] ?? null,
                    'panelty_amount' => $data['Panalty_Balance'] ?? null,
                    'pending_total' => $data['Total_Balance'] ?? null,
                    'collector_name' => $data['collector_name'] ?? null,
                    'current_collector' => $data['current_collector'] ?? null,
                    'collector_id' => $data['collector_id'] ?? null,
                    'Today_Installment' => $data['Today_Installment'] ?? null,
                    'selected_user' => $data['collector_id'] ?? null,
                ]);
            }

            return response()->json(['success' => 'Data saved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in change_collector: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    public function undoPayment(Request $request,$payment_id)
    {
        $reason = $request->input('reason');
        // Fetch the payment details to undo
        $payment = DB::table('customer_payments')->where('idCustomer_Payments', $payment_id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $loan_id = $payment->Customer_Loan_idCustomer_Loan;
        $loan=DB::table('customer_loan')->where('idCustomer_Loan','=',$loan_id)->first();
        $undo_amount = $payment->Amount;
        $undo_payment = $payment->Amount;
        $user_id = $payment->User_idUser;

        // Get current date and time
        $currentDateTime = now()->format('Y-m-d H:i:s');

        // Get current user
        $currentUser = session('username');

        // Revert the payment entry
        DB::table('customer_payments')
            ->where('idCustomer_Payments', $payment_id)
            ->update([
                'status' => 'Removed',
                'Description' => $reason,
                'Amount' => 0.00,
                'comment' => "Payment of $undo_amount undone ($currentDateTime - $currentUser)",
            ]);

        // Fetch related installments
        $installments = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan', $loan_id)
            ->where('Paid_Amount', '>', 0)
            ->orderBy('idInstallments', 'desc')
            ->get();


        foreach ($installments as $item) {
            $idInstallments = $item->idInstallments;
            $Paid_Amount = $item->Paid_Amount;

            // Undo installment amount first
            if ($undo_amount > 0) {
                if ($undo_amount >= $Paid_Amount) {
                    // Reduce undo amount by full installment amount
                    $undo_amount -= $Paid_Amount;

                    DB::table('installments')
                        ->where('idInstallments', $idInstallments)
                        ->update([
                            'Paid_Amount' => 0.00,
                            'Interest_Balance' => $item->interest_amount,
                            'Panalty_Balance' => $item->Panalty_Amount,
                            'Saving_balance' => $item->Saving_amount,
                            'capital_balance' => $item->capital_amount,
                            'Total_Balance' => $item->Installment_Amount + $item->Panalty_Amount + $item->Saving_amount,
                            'Status' => '0',
                        ]);

                    // Log the installment undo
                    DB::table('installment_log')->insert([
                        'Installments_idInstallments' => $idInstallments,
                        'Date' => now(),
                        'Description' => 'Payment undone : ' . $undo_payment,
                        'Amount' => $Paid_Amount,
                        'Panalty_Total' => $item->Panalty_Amount,
                        'Interest_Balance' => $item->Installment_Amount,
                        'Capital_balance' => $item->capital_amount,
                        'Saving_balance' => $item->Saving_amount,
                        'Total_Balance' => $item->Installment_Amount + $item->Panalty_Amount + $item->Saving_amount,
                        'User_idUser' => $user_id,
                    ]);
                } else {

                    $Totalcapital_amount = $item->capital_amount;
                    $Totalinterest_amount = $item->interest_amount;
                    $TotalPanalty_Amount = $item->Panalty_Amount;
                    $TotalSaving_amount = $item->Saving_amount;
                    $Total_amount = $item->Total_Amount;

                    $BalancePanalty_Balance = $item->Panalty_Balance;
                    $BalanceInterest_Balance = $item->Interest_Balance;
                    $Balancecapital_balance = $item->capital_balance;
                    $BalanceSaving_balance = $item->Saving_balance;


                    if ($undo_amount > 0) {
                        if ($TotalSaving_amount > $BalanceSaving_balance) {
                            if ($undo_amount >= ($TotalSaving_amount - $BalanceSaving_balance)) {
                                $BalanceSaving_balance = $TotalSaving_amount;
                                $undo_amount -= ($TotalSaving_amount - $BalanceSaving_balance);
                            } else {
                                $BalanceSaving_balance += $undo_amount;
                                $undo_amount = 0;
                            }
                        }
                    }


                    if ($undo_amount > 0) {
                        if ($Totalcapital_amount > $Balancecapital_balance) {
                            if ($undo_amount >= ($Totalcapital_amount - $Balancecapital_balance)) {
                                $Balancecapital_balance = $Totalcapital_amount;
                                $undo_amount -= ($Totalcapital_amount - $Balancecapital_balance);
                            } else {
                                $Balancecapital_balance += $undo_amount;
                                $undo_amount = 0;
                            }
                        }
                    }


                    if ($undo_amount > 0) {
                        if ($Totalinterest_amount > $BalanceInterest_Balance) {
                            if ($undo_amount >= ($Totalinterest_amount - $BalanceInterest_Balance)) {
                                $BalanceInterest_Balance = $Totalinterest_amount;
                                $undo_amount -= ($Totalinterest_amount - $BalanceInterest_Balance);
                            } else {
                                $BalanceInterest_Balance += $undo_amount;
                                $undo_amount = 0;
                            }
                        }
                    }


                    if ($undo_amount > 0) {
                        if ($TotalPanalty_Amount > $BalancePanalty_Balance) {
                            if ($undo_amount >= ($TotalPanalty_Amount - $BalancePanalty_Balance)) {
                                $BalancePanalty_Balance = $TotalPanalty_Amount;
                                $undo_amount -= ($TotalPanalty_Amount - $BalancePanalty_Balance);
                            } else {
                                $BalancePanalty_Balance += $undo_amount;
                                $undo_amount = 0;
                            }
                        }
                    }


                    $Installment_Paid_Amount = $Total_amount-($BalancePanalty_Balance + $BalanceInterest_Balance + $Balancecapital_balance + $BalanceSaving_balance);

                    DB::table('installments')
                        ->where('idInstallments', $idInstallments)
                        ->update([
                            'Paid_Amount' => $Installment_Paid_Amount,
                            'Interest_Balance' => $BalanceInterest_Balance,
                            'Panalty_Balance' => $BalancePanalty_Balance,
                            'Saving_balance' => $BalanceSaving_balance,
                            'capital_balance' => $Balancecapital_balance,
                            'Total_Balance' => $BalancePanalty_Balance + $BalanceInterest_Balance + $Balancecapital_balance + $BalanceSaving_balance,
                            'Status' => '0',
                        ]);

                    // Log the installment undo
                    DB::table('installment_log')->insert([
                        'Installments_idInstallments' => $idInstallments,
                        'Date' => now(),
                        'Description' => 'Payment undone : ' . $undo_payment,
                        'Amount' => $undo_amount,
                        'Panalty_Total' => $BalancePanalty_Balance,
                        'Interest_Balance' => $item->Installment_Amount,
                        'Capital_balance' => $Balancecapital_balance,
                        'Saving_balance' => $BalanceSaving_balance,
                        'Total_Balance' => $BalancePanalty_Balance + $BalanceInterest_Balance + $Balancecapital_balance + $BalanceSaving_balance,
                        'User_idUser' => $user_id,
                    ]);

                    $undo_amount = 0;
                }
            }

            // Stop if no undo amount remains
            if ($undo_amount <= 0) {
                break;
            }
        }

        //customer savings
        $saving = DB::table('Savings_Account_Log')->where('Payment_id', '=', $payment_id)->first();
        if ($saving){
            $this->SavingAccountController->index($saving->Saving_Acount_Id, 'Payment Undo', "Payment of $undo_payment undone ($currentDateTime - $currentUser)", '0.00', $saving->Credit, $saving->Balance, 'Debit');
        }

        //capital balance
        $this->capitalBalanceController->index($loan_id);
        $request = new Request([
            'customer_id' => $loan->Customer_idCustomer,
            'description' => 'Payment Undo',
            'description_id' => $payment_id,
            'comment' => "Payment of $undo_payment undone ($currentDateTime - $currentUser)",
            'type' => "Payment Undo",
        ]);
        //customer balance
        $this->customerLogController->store($request);

        //bank balance
        $banklog=DB::table('company_bank_has_log')->where('payment_id','=',$payment_id)->get();
        foreach ($banklog as $banklogs){
            $description=$banklogs->Description;
            $bank_log_comment="Payment Undone (".$description.")";
            $type=$banklogs->Type;
            $credit=$banklogs->Credit;
            $debit=$banklogs->Debit;
            $bank_id=$banklogs->Bank_Account_Id;

            if ($type=="Loan Payment-Capital"){
                //capital
                if ($credit>0){
                    $this->bankLogController->index($bank_id, "Loan Payment-Capital", $bank_log_comment, "Cash", "debit", $credit);
                }else{
                    $this->bankLogController->index($bank_id, "Loan Payment-Capital", $bank_log_comment, "Cash", "credit", $debit);
                }
            }

            if ($type=="Loan Payment-Interest"){
                //interest
                if ($credit>0){
                    $this->bankLogController->index($bank_id, "Loan Payment-Interest", $bank_log_comment, "Cash", "debit", $credit);
                }else{
                    $this->bankLogController->index($bank_id, "Loan Payment-Interest", $bank_log_comment, "Cash", "credit", $debit);
                }
            }

            if ($type=="Loan Payment-Penalty"){
                //panelty
                if ($credit>0){
                    $this->bankLogController->index($bank_id, "Loan Payment-Penalty", $bank_log_comment, "Cash", "debit", $credit);
                }else{
                    $this->bankLogController->index($bank_id, "Loan Payment-Penalty", $bank_log_comment, "Cash", "credit", $debit);
                }
            }

        }

        //loan log
        $lastLoanLog = DB::table('Loan_Log')->where('Type', '=', "Customer Payment")->where('Type_ID', '=', $payment_id)->first();
        $this->loanLogController->index(
            $loan_id, 'Payment Undo', $payment_id,
            "Payment of $undo_payment undone ($currentDateTime - $currentUser)", $undo_payment,
            $lastLoanLog->Panelty_Payment, $lastLoanLog->Interest_Payment,
            $lastLoanLog->Capital_Payment, $lastLoanLog->Savings_Payment, $lastLoanLog->Panelty_Balance,
            $lastLoanLog->Interest_Balance, $lastLoanLog->Capital_Balance, $lastLoanLog->Total_Pending_Balance, $lastLoanLog->Saving_Account_Balance
        );

        //customer points
        $company = tableWithBranch('company')->first();


        $points_to_add = 0;
        if ($company->points === "1") {
            $points_percentage = $company->points_percentage;
            $payment_amount_for_points = $undo_payment;

            // Calculate the points to be added
            $points_to_add = ($payment_amount_for_points * $points_percentage) / 100;

            // Retrieve the current points of the customer
            $customer = DB::table('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();
            $current_points = $customer->points;

            // Update the customer's points
            DB::table('customer')
                ->where('idCustomer', $loan->Customer_idCustomer)
                ->update([
                    'points' => $current_points - $points_to_add
                ]);
        }

        // Send SMS
        $sms_template = DB::table('sms_template')->where('type', '=', 'payment_undo')->where('status', '=', '1')->first();
        if ($sms_template) {
            $customer = DB::table('customer')->where('idCustomer', '=', $loan->Customer_idCustomer)->first();

            // Step 2: Define the mapping
            $placeholders = [
                '@Member_No@' => $customer->cus_number,
                '@Member_Name@' => $customer->First_Name . ' ' . $customer->Last_Name,
                '@Loan_No@' => $loan->Loan_No,
                '@Paid_Amount@' => $undo_payment,
            ];

            // Step 3: Replace placeholders in the loan_format
            $loan_number_txt = $sms_template->template;
            foreach ($placeholders as $placeholder => $value) {
                $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
            }

            // Log the SMS message
            $this->smsLogController->index($loan_id, $loan_number_txt, "Undo Payment");
        }

        return response()->json(['item' => 'success', 'id' => '1'], 200);
    }



    public function check_all_capital()
    {
        $loan = DB::table('customer_loan')->get();
        foreach ($loan as $loans) {
            $loan_id = $loans->idCustomer_Loan;
            Log::info($loan_id);
            $this->capitalBalanceController->index($loan_id);
        }
        return redirect()->intended(route('home'));
    }


    public function getLoanDetails(Request $request)
    {
        $idCustomer_Loan = $request->idCustomer_Loan;

        // Fetch data from the installments table
        $loanDetails = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan', $idCustomer_Loan)
            ->select(
                DB::raw('SUM(Panalty_Balance) as Panalty_Balance'),
                DB::raw('SUM(Interest_Balance) as Interest_Balance'),
                DB::raw('SUM(capital_balance) as capital_balance'),
                DB::raw('SUM(Saving_balance) as Saving_balance'),
                DB::raw('SUM(Total_Balance) as Total_Balance')
            )
            ->first();


        // Return the response as JSON
        return response()->json($loanDetails);
    }

}
