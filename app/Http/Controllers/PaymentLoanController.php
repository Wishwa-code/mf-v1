<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentLoanController extends Controller
{


    protected $capitalBalanceController;
    protected $loanLogController;

    // Single constructor to inject both controllers
    public function __construct(CapitalBalanceController $capitalBalanceController,LoanLogController $loanLogController)
    {
        $this->capitalBalanceController = $capitalBalanceController;
        $this->loanLogController = $loanLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Instantiate UserController
        $userController = new UserController();

        // Call the create_panelty function
        $userController->create_panelty();


        $group = tableWithBranch('customer_group')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        $route = tableWithBranch('route','route')
            ->join('user', 'route.id_officer', '=', 'user.id')
            ->get();
        $center = tableWithBranch('center')->get();

        return view('pages.Payment', compact('route','center','group', 'loan_category', 'customers'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $group = $request->group;
        $category = $request->category;
        $customer = $request->customer;
        $center_details = $request->center_details;
        $route = $request->route;
        $loan_number = $request->loan_number_search;

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(
        SELECT 
            group_has_customer.cus_id, 
            group_has_customer.group_id,    -- Add group_id here
            customer_group.Name as group_name, 
            customer_group.center_id 
        FROM group_has_customer 
        LEFT JOIN customer_group 
        ON group_has_customer.group_id = customer_group.idCustomer_Group
    ) as subquery'), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->where('customer_loan.Status', '=', '0')
            ->select(
                'customer_loan.*',
                'loan_category.Name as loan_name',
                'customer.*',
                'center.idCenter',
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(route.name, "-") as route_name'),
                'u1.Full_Name as user_name',
                'u2.Full_Name as lending_officer'
            );

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

        if (!empty($loan_number)) {
            $loanQuery->where('customer_loan.Loan_No', 'LIKE', '%' . $loan_number . '%');
        }

        $loans = $loanQuery->paginate(10);


        $totals = tableWithBranch('customer_loan', 'customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(
        SELECT 
            group_has_customer.cus_id, 
            group_has_customer.group_id, 
            customer_group.Name as group_name, 
            customer_group.center_id 
        FROM group_has_customer 
        LEFT JOIN customer_group 
        ON group_has_customer.group_id = customer_group.idCustomer_Group
    ) as subquery'), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->where('customer_loan.Status', '=', '0');


        // Apply the same filters to the totals query
        if ($group != '0') {
            $totals->where('subquery.group_id', '=', $group);
        }

        if ($category != '0') {
            $totals->where('loan_category.idLoan_Category', '=', $category);
        }

        if ($customer != '0') {
            $totals->where('customer.idCustomer', '=', $customer);
        }

        if ($center_details != '0') {
            $totals->where('center.idCenter', '=', $center_details);
        }

        if ($route != '0') {
            $totals->where('customer.route_id', '=', $route);
        }

        if (!empty($loan_number)) {
            $totals->where('customer_loan.Loan_No', 'LIKE', '%' . $loan_number . '%');
        }

        // Calculate the totals
        $totalLoanCount = $totals->count();
        $totalCapitalBalance = $totals->sum('customer_loan.capital_balance');
        $totalPendingAmount = $totals->sum('customer_loan.Balance_Amount');
        $totalLoanAmount = $totals->sum('customer_loan.Amount');


        // Combine results with pagination and totals
        return response()->json([
            'item' => $loans,
            'totals' => [
                'totalLoanCount' => $totalLoanCount,
                'totalCapitalBalance' => $totalCapitalBalance,
                'totalPendingAmount' => $totalPendingAmount,
                'totalLoanAmount' => $totalLoanAmount,
            ],
            'message' => 'notall'
        ], 200);
    }




    public function settlment()
    {
        $userController = new UserController();
        $userController->create_panelty();
        $group = tableWithBranch('customer_group')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        return view('pages.Settlement', compact('group', 'loan_category', 'customers'));
    }



    public function settlement_create(Request $request)
    {
        $group=$request->group;
        $category=$request->category;
        $customer=$request->customer;

        if ($group == '0' && $category == '0' && $customer == '0') {
            $loan = tableWithBranch('customer_loan','customer_loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
                ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id') // Join for User_idUser
                ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id') // Join for lending_officer_id
                ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Name, "-") as group_name
                         FROM group_has_customer
                         LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                    'customer.idCustomer', '=', 'subquery.cus_id')
                ->where('customer_loan.Status', '=', '0')
                ->select(
                    'customer_loan.*',
                    'loan_category.Name as loan_name',
                    'customer.*',
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
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
                ->where('customer_loan.Status', '=', '0')
                ->select(
                    'customer_loan.*',
                    'loan_category.Name as loan_name',
                    'customer.*',
                    DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
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

            $loan = $loanQuery->get();
            return response()->json(['item' => $loan, 'message' => 'notall'], 200);

        }

    }


    public function getLoanDetails($id)
    {
        // Fetch the loan details by ID
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();

        // Check if loan exists
        if (!$loan) {
            return response()->json(['error' => 'Loan not found'], 404);
        }

        // Sum up the total paid amount from customer_payments table
        $totalPaidAmount = DB::table('customer_payments')
            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->sum('Amount'); // Replace 'payment_amount' with the actual column name

        // Assuming the penalty needs to be dynamically fetched or calculated
        $Penaltyamount = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->sum('Panalty_Amount'); // Replace 'penalty_amount' with the actual column name

        $Penaltybalance = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->sum('Panalty_Balance');



        $totalPaidPenalty=number_format($Penaltyamount,2,'.','')-number_format($Penaltybalance,2,'.','');
        $totalPaidPenalty=round($totalPaidPenalty);

        if ($totalPaidPenalty<0){
            $totalPaidPenalty=0.00;
        }

        // Calculate the net balances
        $netInterestBalance = $loan->installment_balance;
        $netPenaltyBalance = $Penaltybalance;

        // Return the loan details as a JSON response
        return response()->json([
            'Amount' => number_format($loan->Amount,2,'.',''),
            'Interest_Amount' => $loan->Interest_Amount,
            'Total_Loan_Amount' => $loan->Total_Loan_Amount,
            'Total_Paid_Amount' => number_format($totalPaidAmount,2,'.',''),
            'Total_Paid_Penalty' => number_format($totalPaidPenalty,2,'.',''),
            'capital_balance' => number_format($loan->capital_balance,2,'.',''),
            'Interest_Balance' => number_format($netInterestBalance,2,'.',''),
            'Panalty_Rate' => number_format($netPenaltyBalance,2,'.',''),
            'Balance_Amount' => number_format($loan->Balance_Amount,2,'.',''),
            'Tot_loan_balance' => number_format($loan->Balance_Amount,2,'.','')
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id=(int) session('userid');
        $loan_id=$request->loan_id;
        $payment_amount=$request->payment_amount;

        $installments = DB::table('installments')
            ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
            ->where('branch_id', session('branch_id'))
            ->where('Status', '=', '0')
            ->orderBy('idInstallments')
            ->get();


        DB::table('customer_payments')->insert([
            'Date' => date('Y-m-d'),
            'Description' => 'Payment',
            'Amount' => $payment_amount,
            'Customer_Loan_idCustomer_Loan' => $loan_id,
            'User_idUser' => $user_id,
            'time' => Carbon::now()->format('H:i:s'),
            'branch_id' => session('branch_id')
        ]);





        foreach ($installments as $item) {

            if ($payment_amount===0){

            }else{
                $idInstallments=$item->idInstallments;

                $Panalty_Balance=$item->Panalty_Balance;
                $Installment_Balance=$item->Installment_Balance;
                $Total_Balance=$item->Total_Balance;


                $Panalty_Total_log=$item->Panalty_Balance;
                $Installment_Balance_log=$item->Installment_Balance;
                $Total_Balance_log=$item->Total_Balance;


                if ($payment_amount>=$Total_Balance){
                    DB::table('installments')
                        ->where('idInstallments', $idInstallments)
                        ->where('branch_id', session('branch_id'))
                        ->update([
                            'Status' => '1',
                            'Panelty_status' => '2',
                            'Paid_Amount' => DB::raw('Paid_Amount + ' . $Total_Balance),
                            'Total_Balance' => '0.00',
                            'Panalty_Balance' => '0.00',
                            'Installment_Balance' => '0.00',
                        ]);




                    DB::table('installment_log')->insert([
                        'Installments_idInstallments' => $idInstallments,
                        'Date' => date('Y-m-d'),
                        'Description' => 'Payment',
                        'Amount' => $payment_amount,
                        'Panalty_Total' => '0.00',
                        'Installment_Balance' => '0.00',
                        'Total_Balance' => '0.00',
                        'User_idUser' => $user_id,
                        'branch_id' => session('branch_id')
                    ]);

                    $payment_amount=$payment_amount-$Total_Balance;
                }else{

                    if ($Panalty_Balance>=$payment_amount){
                        DB::table('installments')
                            ->where('idInstallments', $idInstallments)
                            ->where('branch_id', session('branch_id'))
                            ->update([
                                'Paid_Amount' => DB::raw('Paid_Amount + ' . $payment_amount),
                                'Total_Balance' => $Total_Balance-$payment_amount,
                                'Panalty_Balance' => $Panalty_Balance-$payment_amount,
                            ]);

                        $installment_log = tableWithBranch('installment_log')
                            ->where('Installments_idInstallments', '=', $idInstallments)
                            ->get();

                        foreach ($installment_log as $log) {
                            $Panalty_Total_log=$log->Panalty_Total;
                            $Installment_Balance_log=$log->Installment_Balance;
                            $Total_Balance_log=$log->Total_Balance;
                        }
                        $Panalty_Total_last=$Panalty_Total_log-$payment_amount;
                        $Total_Balance_last=$Total_Balance_log-$payment_amount;
                        DB::table('installment_log')->insert([
                            'Installments_idInstallments' => $idInstallments,
                            'Date' => date('Y-m-d'),
                            'Description' => 'Payment',
                            'Amount' => $payment_amount,
                            'Panalty_Total' => $Panalty_Total_last,
                            'Installment_Balance' => $Installment_Balance_log,
                            'Total_Balance' => $Total_Balance_last,
                            'User_idUser' => $user_id,
                            'branch_id' => session('branch_id')
                        ]);
                        $payment_amount=0;
                        break;
                    }else{
                        $payment_amount1=$payment_amount-$Panalty_Balance;

                            DB::table('installments')
                                ->where('idInstallments', $idInstallments)
                                ->where('branch_id', session('branch_id'))
                                ->update([

                                    'Paid_Amount' => DB::raw('Paid_Amount + ' . $payment_amount),
                                    'Total_Balance' => $Total_Balance-$payment_amount,
                                    'Panalty_Balance' => '0.00',
                                    'Installment_Balance' => $Installment_Balance-$payment_amount1,
                                ]);

                        $installment_log = tableWithBranch('installment_log')
                            ->where('Installments_idInstallments', '=', $idInstallments)
                            ->get();


                        foreach ($installment_log as $log) {

                            $Installment_Balance_log=$log->Installment_Balance;
                            $Total_Balance_log=$log->Total_Balance;
                        }

                        $Total_Balance_last=$Total_Balance_log-$payment_amount;
                        DB::table('installment_log')->insert([
                            'Installments_idInstallments' => $idInstallments,
                            'Date' => date('Y-m-d'),
                            'Description' => 'Payment',
                            'Amount' => $payment_amount,
                            'Panalty_Total' => '0.00',
                            'Installment_Balance' => $Installment_Balance_log-$payment_amount1,
                            'Total_Balance' => $Total_Balance_last,
                            'User_idUser' => $user_id,
                            'branch_id' => session('branch_id')
                        ]);

                        $payment_amount=0;

                        break;
                    }


                }
            }
        }
        return response()->json(['item' => 'sucess'], 200);



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $loan = tableWithBranch('customer_loan','customer_loan')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->where('idCustomer_Loan', '=', $id)->first();
        $customers = tableWithBranch('customer','customer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->select('customer.*', 'customer_group.Name as group_name')
            ->where('idCustomer', '=', $loan->Customer_idCustomer)->first();
        $installments = tableWithBranch('installments')
            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
            ->get();
        return view('pages.Payment_2', compact('loan','customers','installments'));
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
    public function destroy(string $id)
    {
        //
    }


    public function ins_log(string $id){

        $installment_log = tableWithBranch('installment_log')
            ->where('Installments_idInstallments', '=', $id)
            ->get();
        return response()->json(['item' => $installment_log,'message' => 'all'], 200);
    }


    public function deduct_report()
    {


        $group = tableWithBranch('customer_group')->get();
        $center = tableWithBranch('center')->get();
        $loan_category = tableWithBranch('loan_category')->get();
        $customers = tableWithBranch('customer')->get();
        return view('pages.DeductReport', compact('group', 'loan_category', 'customers','center'));
    }


    public function deduct_report_view(Request $request) {
        $group = $request->group;
        $center_details = $request->center_details;

        $loanQuery = tableWithBranch('customer_loan','customer_loan')
            ->select(
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Installment_Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.capital_balance',
                DB::raw('COALESCE(SUM(customer_payments.Amount), 0) as total_payments'),
                'customer_group.Name', // Assuming there's a column called 'group_name' in customer_group
                'customer.First_Name',     // Assuming there's a column called 'customer_name' in customer
                'customer.Last_Name'     // Assuming there's a column called 'customer_name' in customer
            )
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('customer_payments', 'customer_loan.idCustomer_Loan', '=', 'customer_payments.Customer_Loan_idCustomer_Loan')
            ->groupBy(
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Installment_Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.capital_balance',
                'customer_group.Name',
                'customer.First_Name',
                'customer.Last_Name'
            );

        if ($group != '0') {
            $loanQuery->where('customer_group.idCustomer_Group', '=', $group);
        }

        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        $loan = $loanQuery->get();

        return response()->json(['items' => $loan, 'message' => 'success'], 200);
    }


    public function payment_print(){
        $company= tableWithBranch('company')->first();
        return view('pages.Paymentinvoice',compact('company'));
    }


    public function settleLoan(Request $request)
    {
        $loanId = $request->input('loan_id');
        $user_id=(int) session('userid');
        $net_balance = $request->input('net_balance');


        $net_interest_balance_read_only = $request->input('net_interest_balance_read_only');
        $net_interest_balance = $request->input('net_interest_balance');
        $net_penalty_balance_readonly = $request->input('net_penalty_balance_readonly');
        $net_penalty_balance = $request->input('net_penalty_balance');
        $net_capital_balance = $request->input('net_capital_balance');

        $reduce_interest_amount=$net_interest_balance_read_only-$net_interest_balance;
        $reduce_penalty_amount=$net_penalty_balance_readonly-$net_penalty_balance;


// Insert a new record in the customer_payments table
        $savedId=DB::table('customer_payments')->insertGetId([
            'Date' => date('Y-m-d'),
            'Description' => 'Loan Settlement'."(Reduced interest amount:".$reduce_interest_amount."/Reduced Penalty amount :".$reduce_penalty_amount.")",
            'comment' => 'Loan Settlement'."(Paid Capital:".$net_capital_balance."/Paid Interest :".$net_interest_balance."/Paid Penalty :".$net_penalty_balance.")",
            'Amount' => $net_balance,
            'Customer_Loan_idCustomer_Loan' => $loanId,
            'User_idUser' => $user_id,
            'time' => Carbon::now()->format('H:i:s'),
            'branch_id' => session('branch_id')
        ]);

        // Update the status of the loan in the customer_loan table to 0 (settled)
        DB::table('customer_loan')->where('idCustomer_Loan', $loanId)->where('branch_id', session('branch_id'))->update([
            'Status' => 1
        ]);

        // Update the status in the installments table to 1 (completed)
        DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $loanId)->where('branch_id', session('branch_id'))->update([
            'status' => 1
        ]);
        $this->loanLogController->index(
            $loanId, 'Loan Settlement', $savedId,
            'Loan Settlement', $net_balance,
            '0.00', '0.00',
            '0.00', '0.00', '0.00',
            '0.00', '0.00', '0.00', '0.00'
        );
        $this->capitalBalanceController->index($loanId,1);
        return response()->json(['items' => $loanId, 'message' => 'success'], 200);
    }

    public function reschedule()
    {
        // Instantiate UserController
        $userController = new UserController();
        // Call the create_panelty function
        $userController->create_panelty();
        $group = DB::table('customer_group')->get();
        $loan_category = DB::table('loan_category')->get();
        $customers = DB::table('customer')->get();
        return view('pages.Reshedule', compact('group', 'loan_category', 'customers'));
    }

}
