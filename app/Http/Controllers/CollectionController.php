<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');
        $userId = (int)session('userid');
        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('User_idUser', '=', $userId)
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

        return view('pages.TodayCollection', compact('userPayments', 'dates', 'date_2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $dates = $request->date;
        $date_2 = $request->date_2;

        $userId = (int)session('userid');
        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->where('User_idUser', '=', $userId)
            ->select('customer_payments.*', 'user.Full_Name')
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

        return view('pages.TodayCollection', compact('userPayments', 'dates', 'date_2'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $comment = $request->input('comment');
        $checkedItems = $request->input('checkedItems');
        $confirmUserId = user_data('idUser'); // Assuminguser_data('idUser') gets the current logged-in user's ID

        try {
            foreach ($checkedItems as $item) {

                if ($request->isChecked == 0) {
                    $comment = $item['comment'];
                }
                // Assuming each item in checkedItems has an idCustomer_Payments field
                updateWithBranch('customer_payments', 'idCustomer_Payments', $item['idCustomer_Payments'], [
                    'confirm_user' => $confirmUserId,
                    'confirm_date_time' => now()->format('Y-m-d H:i:s'), // Using Laravel's now() helper to get current date and time
                    'comment' => $comment,
                ]);
            }

            return response()->json(['message' => 'Customer payments updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update customer payments', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $date = $request->date;
        $userid = $request->userid;
        $type = $request->type;

        if ($type === "1") {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
                ->join('user as u2', 'customer_payments.confirm_user', '=', 'u2.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('u1.id', '=', $userid)
                ->where('customer_payments.confirm_user', '!=', '-')
                ->select('customer_payments.*', 'customer_payments.Amount as PayedAmount', 'u1.Full_Name as username', 'u2.Full_Name as confirm_user_name', 'customer_loan.*', 'customer.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid, $type], 200);
        } else if ($type === "0") {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
                ->join('user as u2', 'customer_payments.confirm_user', '=', 'u2.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('u1.id', '=', $userid)
                ->select('customer_payments.*', 'customer_payments.Amount as PayedAmount', 'u1.Full_Name as username', 'u2.Full_Name as confirm_user_name', 'customer_loan.*', 'customer.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid, $type], 200);
        } else if ($type === "2") {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('u1.id', '=', $userid)
                ->select('customer_payments.*', 'customer_payments.Amount as PayedAmount', 'u1.Full_Name as username', 'customer_loan.*', 'customer.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid, $type], 200);
        } else {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
                ->join('user as u2', 'customer_payments.confirm_user', '=', 'u2.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('u1.id', '=', $userid)
                ->select('customer_payments.*', 'customer_payments.Amount as PayedAmount', 'u1.Full_Name as username', 'u2.Full_Name as confirm_user_name', 'customer_loan.*', 'customer.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid, $type], 200);
        }
    }


    public function show_2(Request $request)
    {
        $date = $request->date;
        $userid = $request->userid;
        $type = $request->type;

        if ($type === "1") {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('user.id', '=', $userid)
                ->where('customer_payments.confirm_user', '!=', '-')
                ->select('customer_payments.*', 'customer_loan.Loan_No', 'customer.*', 'user.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid], 200);
        } else if ($type === "0") {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('user.id', '=', $userid)
                ->where('customer_payments.confirm_user', '=', '-') // Adjusted from '!=' to '='
                ->select('customer_payments.*', 'customer_loan.Loan_No', 'customer.*', 'user.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid], 200);
        } else {
            $collection = tableWithBranch('customer_payments', 'customer_payments')
                ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer')
                ->where('customer_payments.Date', '=', $date)
                ->where('u1.id', '=', $userid)
                ->select('customer_payments.*', 'customer_loan.Loan_No', 'customer.*', 'u1.*')
                ->orderBy('customer_payments.idCustomer_Payments', 'asc')
                ->get();


            return response()->json(['item' => $collection, 'date' => $date, 'userid' => $userid], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    public function pending_payment()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');

        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('customer_payments.confirm_user', '=', '-')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->get();


        $userPayments = [];

        // Iterate over the collection to group amounts by user
        foreach ($collection as $item) {
            if (!isset($userPayments[$item->User_idUser])) {
                $userPayments[$item->User_idUser] = [
                    'confirm_user' => $item->confirm_user,
                    'user_id' => $item->User_idUser,
                    'username' => $item->Full_Name,
                    'date' => $item->Date,
                    'total_amount' => 0
                ];
            }
            $userPayments[$item->User_idUser]['total_amount'] += $item->Amount;
        }

        return view('pages.PendingCollection', compact('userPayments', 'dates', 'date_2'));
    }


    public function create_pending_payment(Request $request)
    {

        $dates = $request->date;
        $date_2 = $request->date_2;


        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('customer_payments.confirm_user', '=', '-')
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


        return view('pages.PendingCollection', compact('userPayments', 'dates', 'date_2'));
    }



    public function approved_payment()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');

        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_payments.confirm_user', '=', 'u2.id')
            ->where('customer_payments.confirm_user', '!=', '-')
            ->select('customer_payments.*', 'u1.Full_Name as username', 'u2.Full_Name as confirm_user_name')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->get();



        $userPayments = [];

        // Iterate over the collection to group amounts by user
        foreach ($collection as $item) {
            if (!isset($userPayments[$item->User_idUser])) {
                $userPayments[$item->User_idUser] = [
                    'confirm_user' => $item->confirm_user,
                    'user_id' => $item->User_idUser,
                    'username' => $item->username,
                    'confirm_user_name' => $item->confirm_user_name,
                    'date' => $item->Date,
                    'total_amount' => 0
                ];
            }
            $userPayments[$item->User_idUser]['total_amount'] += $item->Amount;
        }

        return view('pages.ApprovedCollection', compact('userPayments', 'dates', 'date_2'));
    }


    public function create_approved_payment(Request $request)
    {

        $dates = $request->date;
        $date_2 = $request->date_2;


        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('customer_payments.confirm_user', '!=', '-')
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


        return view('pages.ApprovedCollection', compact('userPayments', 'dates', 'date_2'));
    }

    public function repaymentreportindex()
    {
        $group = tableWithBranch('customer_group')->get();
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $company = tableWithBranch('company')->first();
        $user = tableWithBranch('user')->get();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        return view('pages.CollectionReport', compact('group', 'center', 'customers', 'company', 'user', 'lending_officer'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createrepaymentreport(Request $request)
    {
        $date_from       = $request->date_from;
        $date_to         = $request->date_to;
        $center_details  = $request->center_details;
        $group           = $request->group;
        $customer        = $request->customer;
        $user            = $request->user;
        $lending_officer = $request->lending_officer;

        try {
            $loanQuery = tableWithBranch('customer_payments', 'customer_payments')
                ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')

                // Agent who recorded the payment
                ->join('user', 'customer_payments.User_idUser', '=', 'user.id')

                // Lending officer (LEFT JOIN because some loans may not have one)
                ->leftJoin('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')

                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')

                ->whereBetween('customer_payments.date', [$date_from, $date_to])

                ->select(
                    'customer_payments.*',
                    'customer.cus_number as cus_number',
                    'customer_loan.Loan_No as Loan_No',
                    'center.Name as center_name',
                    'customer.First_Name as customer_name',
                    'customer.Last_Name as customer_lastname',
                    'customer.Nic as NIC',
                    'customer_group.Group_No as group_no',
                    'customer_group.Name as group_name',

                    // Use distinct aliases:
                    'user.Full_Name as agent_name',             // the cashier/collector who entered payment
                    'u2.Full_Name as lending_officer_name'      // the lending officer’s name
                );

            if ($center_details != '0') {
                $loanQuery->where('center.idCenter', $center_details);
            }
            if ($group != '0') {
                $loanQuery->where('customer_group.idCustomer_Group', $group);
            }
            if ($customer != '0') {
                $loanQuery->where('customer.idCustomer', $customer);
            }
            if ($user != '0') {
                $loanQuery->where('user.id', $user);
            }
            if ($lending_officer != '0') {
                $loanQuery->where('customer_loan.lending_officer_id', $lending_officer);
            }

            $loan = $loanQuery->get();

            return response()->json(['item' => $loan, 'test' => $date_from], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching loan details.',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function customerrepaymentreport()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');
        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
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



        return view('pages.CustomerWiseCollectionReport', compact('userPayments', 'dates', 'date_2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function customerrepaymentreportview(Request $request)
    {
        $dates = $request->date;
        $date_2 = $request->date_2;


        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->select('customer_payments.*', 'user.Full_Name', 'customer_loan.*', 'customer.*')
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

        return view('pages.CustomerWiseCollectionReport', compact('userPayments', 'dates', 'date_2'));
    }

    public function cashbookreport()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');
        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
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

        return view('pages.CashBookReport', compact('userPayments', 'dates', 'date_2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function cashbookreportview(Request $request)
    {
        $dates = $request->date;
        $date_2 = $request->date_2;


        $collection = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->join('customer_loan', 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->select('customer_payments.*', 'user.Full_Name', 'customer_loan.*', 'customer.*')
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

        return view('pages.CashBookReport', compact('userPayments', 'dates', 'date_2'));
    }


    public function parreport()
    {
        $center = tableWithBranch('center')->get();
        // Get loans with necessary data
        $loans = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->leftJoin(
                DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer_loan.Customer_idCustomer',
                '=',
                'subquery.cus_id'
            )
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.Status'
            )
            ->where('customer_loan.Status', '=', '0')
            ->groupBy('customer_loan.idCustomer_Loan', 'customer_loan.Loan_No', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount', 'customer_loan.Status')
            ->get();

        // Define the date ranges
        $today = now()->toDateString();
        $thirtydays = now()->subDays(30)->toDateString();
        $sixtydays = now()->subDays(60)->toDateString();
        $ninetydays = now()->subDays(90)->toDateString();

        // Initialize totals for the outstanding balances
        $totalOutstanding = 0;
        $par = 0;
        $totalThirtyDaysOutstanding = 0;
        $totalSixtyDaysOutstanding = 0;
        $totalNinetyDaysOutstanding = 0;
        $totalAllDaysOutstanding = 0;

        $thirtycount = 0;
        $sixtycount = 0;
        $nintycount = 0;
        $allcount = 0;

        $thirtycapital = 0;
        $sixtycapital = 0;
        $ninetycapital = 0;
        $allcapital = 0;



        foreach ($loans as $loan) {
            // Get outstanding balances for each loan
            $installments = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                ->selectRaw("
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as ninetydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as sixtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as thirtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as alldays_outstanding
            ", [$ninetydays, $sixtydays, $thirtydays, $today])
                ->first();

            // Assign the calculated values to each loan
            $loan->ninetydays_outstanding = $installments->ninetydays_outstanding;
            $loan->sixtydays_outstanding = $installments->sixtydays_outstanding;
            $loan->thirtydays_outstanding = $installments->thirtydays_outstanding;
            $loan->alldays_outstanding = $installments->alldays_outstanding;

            // Sum up outstanding values
            $totalOutstanding += $loan->ninetydays_outstanding + $loan->sixtydays_outstanding + $loan->thirtydays_outstanding + $loan->alldays_outstanding;

            // Calculate total values for each outstanding category
            $totalNinetyDaysOutstanding += $loan->ninetydays_outstanding;
            $totalSixtyDaysOutstanding += $loan->sixtydays_outstanding;
            $totalThirtyDaysOutstanding += $loan->thirtydays_outstanding;
            $totalAllDaysOutstanding += $loan->alldays_outstanding;

            // Increment counts for loans with outstanding balances in each category
            if ($loan->thirtydays_outstanding > 0) {
                $thirtycount++;
                $thirtycapital += $loan->Amount;
            }
            if ($loan->sixtydays_outstanding > 0) {
                $sixtycount++;
                $sixtycapital += $loan->Amount;
            }
            if ($loan->ninetydays_outstanding > 0) {
                $nintycount++;
                $ninetycapital += $loan->Amount;
            }

            if ($loan->alldays_outstanding > 0) {
                $allcount++;
                $allcapital += $loan->Amount;
            }
        }
        $thirtypercentage = 0;
        $sixtypercentage = 0;
        $ninetypercentage = 0;
        $allpercentage = 0;


        // Increment counts for loans with outstanding balances in each category
        if ($totalThirtyDaysOutstanding > 0 && $thirtycapital > 0) {
            $thirtypercentage = ($totalThirtyDaysOutstanding / $thirtycapital) * 100;
        }
        if ($totalSixtyDaysOutstanding > 0 && $sixtycapital > 0) {
            $sixtypercentage = ($totalSixtyDaysOutstanding / $sixtycapital) * 100;
        }
        if ($totalNinetyDaysOutstanding > 0 && $ninetycapital > 0) {
            $ninetypercentage = ($totalNinetyDaysOutstanding / $ninetycapital) * 100;
        }

        if ($totalAllDaysOutstanding > 0 && $allcapital > 0) {
            $allpercentage = ($totalAllDaysOutstanding / $allcapital) * 100;
        }




        return view('pages.PAR', compact('center', 'thirtypercentage', 'sixtypercentage', 'ninetypercentage', 'allpercentage', 'thirtycapital', 'sixtycapital', 'ninetycapital', 'allcapital', 'loans', 'par', 'totalOutstanding', 'totalNinetyDaysOutstanding', 'totalSixtyDaysOutstanding', 'totalThirtyDaysOutstanding', 'totalAllDaysOutstanding', 'thirtycount', 'sixtycount', 'nintycount', 'allcount'));
    }


    public function partview(Request $request)
    {
        $center_details = $request->center_details;


        $center = tableWithBranch('center')->get();
        // Get loans with necessary data
        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->leftJoin(
                DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer_loan.Customer_idCustomer',
                '=',
                'subquery.cus_id'
            )
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.Status'
            )
            ->where('customer_loan.Status', '=', '0')
            ->groupBy('customer_loan.idCustomer_Loan', 'customer_loan.Loan_No', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount', 'customer_loan.Status');
        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        $loans = $loanQuery->get();

        // Define the date ranges
        $today = now()->toDateString();
        $thirtydays = now()->subDays(30)->toDateString();
        $sixtydays = now()->subDays(60)->toDateString();
        $ninetydays = now()->subDays(90)->toDateString();

        // Initialize totals for the outstanding balances
        $totalOutstanding = 0;
        $par = 0;
        $totalThirtyDaysOutstanding = 0;
        $totalSixtyDaysOutstanding = 0;
        $totalNinetyDaysOutstanding = 0;
        $totalAllDaysOutstanding = 0;

        $thirtycount = 0;
        $sixtycount = 0;
        $nintycount = 0;
        $allcount = 0;

        $thirtycapital = 0;
        $sixtycapital = 0;
        $ninetycapital = 0;
        $allcapital = 0;



        foreach ($loans as $loan) {
            // Get outstanding balances for each loan
            $installments = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                ->selectRaw("
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as ninetydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as sixtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as thirtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as alldays_outstanding
            ", [$ninetydays, $sixtydays, $thirtydays, $today])
                ->first();

            // Assign the calculated values to each loan
            $loan->ninetydays_outstanding = $installments->ninetydays_outstanding;
            $loan->sixtydays_outstanding = $installments->sixtydays_outstanding;
            $loan->thirtydays_outstanding = $installments->thirtydays_outstanding;
            $loan->alldays_outstanding = $installments->alldays_outstanding;

            // Sum up outstanding values
            $totalOutstanding += $loan->ninetydays_outstanding + $loan->sixtydays_outstanding + $loan->thirtydays_outstanding + $loan->alldays_outstanding;

            // Calculate total values for each outstanding category
            $totalNinetyDaysOutstanding += $loan->ninetydays_outstanding;
            $totalSixtyDaysOutstanding += $loan->sixtydays_outstanding;
            $totalThirtyDaysOutstanding += $loan->thirtydays_outstanding;
            $totalAllDaysOutstanding += $loan->alldays_outstanding;

            // Increment counts for loans with outstanding balances in each category
            if ($loan->thirtydays_outstanding > 0) {
                $thirtycount++;
                $thirtycapital += $loan->Amount;
            }
            if ($loan->sixtydays_outstanding > 0) {
                $sixtycount++;
                $sixtycapital += $loan->Amount;
            }
            if ($loan->ninetydays_outstanding > 0) {
                $nintycount++;
                $ninetycapital += $loan->Amount;
            }

            if ($loan->alldays_outstanding > 0) {
                $allcount++;
                $allcapital += $loan->Amount;
            }
        }
        $thirtypercentage = 0;
        $sixtypercentage = 0;
        $ninetypercentage = 0;
        $allpercentage = 0;


        // Increment counts for loans with outstanding balances in each category
        if ($totalThirtyDaysOutstanding > 0 && $thirtycapital > 0) {
            $thirtypercentage = ($totalThirtyDaysOutstanding / $thirtycapital) * 100;
        }
        if ($totalSixtyDaysOutstanding > 0 && $sixtycapital > 0) {
            $sixtypercentage = ($totalSixtyDaysOutstanding / $sixtycapital) * 100;
        }
        if ($totalNinetyDaysOutstanding > 0 && $ninetycapital > 0) {
            $ninetypercentage = ($totalNinetyDaysOutstanding / $ninetycapital) * 100;
        }

        if ($totalAllDaysOutstanding > 0 && $allcapital > 0) {
            $allpercentage = ($totalAllDaysOutstanding / $allcapital) * 100;
        }




        return view('pages.PAR', compact('center', 'thirtypercentage', 'sixtypercentage', 'ninetypercentage', 'allpercentage', 'thirtycapital', 'sixtycapital', 'ninetycapital', 'allcapital', 'loans', 'par', 'totalOutstanding', 'totalNinetyDaysOutstanding', 'totalSixtyDaysOutstanding', 'totalThirtyDaysOutstanding', 'totalAllDaysOutstanding', 'thirtycount', 'sixtycount', 'nintycount', 'allcount'));
    }




    public function parweeklyreport()
    {


        $center = tableWithBranch('center')->get();

        // Get loans with necessary data
        $loans = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->leftJoin(
                DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer_loan.Customer_idCustomer',
                '=',
                'subquery.cus_id'
            )
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.Status'
            )
            ->where('customer_loan.Status', '=', '0')
            ->groupBy('customer_loan.idCustomer_Loan', 'center.No', 'customer_loan.Loan_No', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount', 'customer_loan.Status')
            ->get();

        // Define the date ranges
        $today = now()->toDateString();
        $thirtydays = now()->subDays(7)->toDateString();
        $sixtydays = now()->subDays(14)->toDateString();
        $ninetydays = now()->subDays(21)->toDateString();

        // Initialize totals for the outstanding balances
        $totalOutstanding = 0;
        $par = 0;
        $totalThirtyDaysOutstanding = 0;
        $totalSixtyDaysOutstanding = 0;
        $totalNinetyDaysOutstanding = 0;
        $totalAllDaysOutstanding = 0;

        $thirtycount = 0;
        $sixtycount = 0;
        $nintycount = 0;
        $allcount = 0;

        $thirtycapital = 0;
        $sixtycapital = 0;
        $ninetycapital = 0;
        $allcapital = 0;



        foreach ($loans as $loan) {
            // Get outstanding balances for each loan
            $installments = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                ->selectRaw("
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as ninetydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as sixtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as thirtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as alldays_outstanding
            ", [$ninetydays, $sixtydays, $thirtydays, $today])
                ->first();

            // Assign the calculated values to each loan
            $loan->ninetydays_outstanding = $installments->ninetydays_outstanding;
            $loan->sixtydays_outstanding = $installments->sixtydays_outstanding;
            $loan->thirtydays_outstanding = $installments->thirtydays_outstanding;
            $loan->alldays_outstanding = $installments->alldays_outstanding;

            // Sum up outstanding values
            $totalOutstanding += $loan->ninetydays_outstanding + $loan->sixtydays_outstanding + $loan->thirtydays_outstanding + $loan->alldays_outstanding;

            // Calculate total values for each outstanding category
            $totalNinetyDaysOutstanding += $loan->ninetydays_outstanding;
            $totalSixtyDaysOutstanding += $loan->sixtydays_outstanding;
            $totalThirtyDaysOutstanding += $loan->thirtydays_outstanding;
            $totalAllDaysOutstanding += $loan->alldays_outstanding;

            // Increment counts for loans with outstanding balances in each category
            if ($loan->thirtydays_outstanding > 0) {
                $thirtycount++;
                $thirtycapital += $loan->Amount;
            }
            if ($loan->sixtydays_outstanding > 0) {
                $sixtycount++;
                $sixtycapital += $loan->Amount;
            }
            if ($loan->ninetydays_outstanding > 0) {
                $nintycount++;
                $ninetycapital += $loan->Amount;
            }

            if ($loan->alldays_outstanding > 0) {
                $allcount++;
                $allcapital += $loan->Amount;
            }
        }
        $thirtypercentage = 0;
        $sixtypercentage = 0;
        $ninetypercentage = 0;
        $allpercentage = 0;


        // Increment counts for loans with outstanding balances in each category
        if ($totalThirtyDaysOutstanding > 0 && $thirtycapital > 0) {
            $thirtypercentage = ($totalThirtyDaysOutstanding / $thirtycapital) * 100;
        }
        if ($totalSixtyDaysOutstanding > 0 && $sixtycapital > 0) {
            $sixtypercentage = ($totalSixtyDaysOutstanding / $sixtycapital) * 100;
        }
        if ($totalNinetyDaysOutstanding > 0 && $ninetycapital > 0) {
            $ninetypercentage = ($totalNinetyDaysOutstanding / $ninetycapital) * 100;
        }

        if ($totalAllDaysOutstanding > 0 && $allcapital > 0) {
            $allpercentage = ($totalAllDaysOutstanding / $allcapital) * 100;
        }




        return view('pages.PAR_Weekly', compact('center', 'thirtypercentage', 'sixtypercentage', 'ninetypercentage', 'allpercentage', 'thirtycapital', 'sixtycapital', 'ninetycapital', 'allcapital', 'loans', 'par', 'totalOutstanding', 'totalNinetyDaysOutstanding', 'totalSixtyDaysOutstanding', 'totalThirtyDaysOutstanding', 'totalAllDaysOutstanding', 'thirtycount', 'sixtycount', 'nintycount', 'allcount'));
    }


    public function partweeklyview(Request $request)
    {

        $center_details = $request->center_details;

        $center = tableWithBranch('center')->get();
        // Get loans with necessary data
        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->join('installments', 'customer_loan.idCustomer_Loan', '=', 'installments.Customer_Loan_idCustomer_Loan')
            ->leftJoin(
                DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer_loan.Customer_idCustomer',
                '=',
                'subquery.cus_id'
            )
            ->leftJoin('group_has_customer', 'customer_loan.Customer_idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select(
                DB::raw('IFNULL(center.No, "-") as center_no'),
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Amount',
                'customer_loan.Total_Loan_Amount',
                'customer_loan.Status'
            )
            ->where('customer_loan.Status', '=', '0')
            ->groupBy('customer_loan.idCustomer_Loan', 'center.No', 'customer_loan.Loan_No', 'customer_loan.Amount', 'customer_loan.Total_Loan_Amount', 'customer_loan.Status');

        if ($center_details != '0') {
            $loanQuery->where('center.idCenter', '=', $center_details);
        }

        $loans = $loanQuery->get();


        // Define the date ranges
        $today = now()->toDateString();
        $thirtydays = now()->subDays(7)->toDateString();
        $sixtydays = now()->subDays(14)->toDateString();
        $ninetydays = now()->subDays(21)->toDateString();

        // Initialize totals for the outstanding balances
        $totalOutstanding = 0;
        $par = 0;
        $totalThirtyDaysOutstanding = 0;
        $totalSixtyDaysOutstanding = 0;
        $totalNinetyDaysOutstanding = 0;
        $totalAllDaysOutstanding = 0;

        $thirtycount = 0;
        $sixtycount = 0;
        $nintycount = 0;
        $allcount = 0;

        $thirtycapital = 0;
        $sixtycapital = 0;
        $ninetycapital = 0;
        $allcapital = 0;



        foreach ($loans as $loan) {
            // Get outstanding balances for each loan
            $installments = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                ->selectRaw("
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as ninetydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as sixtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as thirtydays_outstanding,
                SUM(CASE WHEN Installment_Date < ? THEN (Interest_Balance+Capital_Balance) ELSE 0 END) as alldays_outstanding
            ", [$ninetydays, $sixtydays, $thirtydays, $today])
                ->first();

            // Assign the calculated values to each loan
            $loan->ninetydays_outstanding = $installments->ninetydays_outstanding;
            $loan->sixtydays_outstanding = $installments->sixtydays_outstanding;
            $loan->thirtydays_outstanding = $installments->thirtydays_outstanding;
            $loan->alldays_outstanding = $installments->alldays_outstanding;

            // Sum up outstanding values
            $totalOutstanding += $loan->ninetydays_outstanding + $loan->sixtydays_outstanding + $loan->thirtydays_outstanding + $loan->alldays_outstanding;

            // Calculate total values for each outstanding category
            $totalNinetyDaysOutstanding += $loan->ninetydays_outstanding;
            $totalSixtyDaysOutstanding += $loan->sixtydays_outstanding;
            $totalThirtyDaysOutstanding += $loan->thirtydays_outstanding;
            $totalAllDaysOutstanding += $loan->alldays_outstanding;

            // Increment counts for loans with outstanding balances in each category
            if ($loan->thirtydays_outstanding > 0) {
                $thirtycount++;
                $thirtycapital += $loan->Amount;
            }
            if ($loan->sixtydays_outstanding > 0) {
                $sixtycount++;
                $sixtycapital += $loan->Amount;
            }
            if ($loan->ninetydays_outstanding > 0) {
                $nintycount++;
                $ninetycapital += $loan->Amount;
            }

            if ($loan->alldays_outstanding > 0) {
                $allcount++;
                $allcapital += $loan->Amount;
            }
        }
        $thirtypercentage = 0;
        $sixtypercentage = 0;
        $ninetypercentage = 0;
        $allpercentage = 0;


        // Increment counts for loans with outstanding balances in each category
        if ($totalThirtyDaysOutstanding > 0 && $thirtycapital > 0) {
            $thirtypercentage = ($totalThirtyDaysOutstanding / $thirtycapital) * 100;
        }
        if ($totalSixtyDaysOutstanding > 0 && $sixtycapital > 0) {
            $sixtypercentage = ($totalSixtyDaysOutstanding / $sixtycapital) * 100;
        }
        if ($totalNinetyDaysOutstanding > 0 && $ninetycapital > 0) {
            $ninetypercentage = ($totalNinetyDaysOutstanding / $ninetycapital) * 100;
        }

        if ($totalAllDaysOutstanding > 0 && $allcapital > 0) {
            $allpercentage = ($totalAllDaysOutstanding / $allcapital) * 100;
        }




        return view('pages.PAR_Weekly', compact('center_details', 'center', 'thirtypercentage', 'sixtypercentage', 'ninetypercentage', 'allpercentage', 'thirtycapital', 'sixtycapital', 'ninetycapital', 'allcapital', 'loans', 'par', 'totalOutstanding', 'totalNinetyDaysOutstanding', 'totalSixtyDaysOutstanding', 'totalThirtyDaysOutstanding', 'totalAllDaysOutstanding', 'thirtycount', 'sixtycount', 'nintycount', 'allcount'));
    }
}
