<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;

class CenterController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isHeadOffice = (int)session('branch_id') === -1;

        if ($isHeadOffice) {
            $userData = DB::table('center as center')
                ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
                ->leftJoin('branch', 'center.branch_id', '=', 'branch.branch_id')
                //Keep current view links and add branch name
                ->select(
                    'center.*',
                    DB::raw('route.name as name'), // keep `$item->name` for route
                    DB::raw('route.id_route as id_route'),
                    DB::raw('branch.Name as branch_name')
                )
                ->get();
            $route = DB::table('route')->get();
        } else {
            $userData = tableWithBranch('center','center')
                ->leftjoin('route', 'center.route_id', '=', 'route.id_route')
                ->leftJoin('branch', 'center.branch_id', '=', 'branch.branch_id')
                ->select(
                    'center.*',
                    DB::raw('route.name as name'),
                    DB::raw('route.id_route as id_route'),
                    DB::raw('branch.Name as branch_name')
                )
                ->get();
            $route = tableWithBranch('route')->get();
        }

        return view('pages.Center',compact('userData','route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $isHeadOffice = (int)session('branch_id') === -1;

        if ($isHeadOffice) {
            $customer = DB::table('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->get();
            $center = DB::table('center')->where('idCenter', '=', $id)->first();
            $customercount = DB::table('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->count();
        } else {
            $customer = tableWithBranch('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->get();
            $center = tableWithBranch('center')->where('idCenter', '=', $id)->first();
            $customercount = tableWithBranch('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->count();
        }

        return view('pages.ViewCenter', compact('customer','center','customercount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'center_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'center_incharge' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'center_number' => 'required|string|max:255',
            'route_id' => 'required',
        ]);

        // Use tableWithBranch to check if the center already exists
        $check = tableWithBranch('center')->where('No', '=', $request->center_number)->first();

        if ($check) {
            return response()->json(['message' => 'Center already exists.', 'id' => '0'], 200);
        } else {
            // Prepare the data for insertion
            $centerData = [
                'No' => $request->center_number,
                'Name' => $request->center_name,
                'Contact_no' => $request->contact,
                'Address' => $request->address,
                'Route' => '',
                'Center_incharge' => $request->center_incharge,
                'Location' => $request->location,
                'Groups' => "0",
                'Members' => "0",
                'route_id' => $request->route_id,
            ];

            // Use the helper function to insert the new center with the branch ID
            $insertedId = insertWithBranch('center', $centerData);

            if ($insertedId) {
                return response()->json(['message' => 'Data saved successfully.', 'id' => '1'], 200);
            } else {
                return response()->json(['message' => 'Failed to save data.', 'id' => '0'], 500);
            }
        }
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        $route = tableWithBranch('route')->get();
        return view('pages.CreateCenter',compact('route'));
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
    public function update(Request $request)
    {
        // Update the center record using the helper function
        $updated = updateWithBranch('center', 'idCenter', $request->center_id, [
            'No' => $request->center_number,
            'Name' => $request->center_name,
            'Contact_no' => $request->contact,
            'Address' => $request->address,
            'Route' => '',
            'Center_incharge' => $request->center_incharge,
            'Location' => $request->location,
            'route_id' => $request->route_id,
        ]);

        if ($updated) {
            return response()->json(['message' => 'Data updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to update data.'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if center ID is used in customer_group table
        $exists = DB::table('customer_group')->where('center_id', $id)->exists();

        if ($exists) {
            return response()->json(['message' => 'Cannot delete. Center is linked to one or more customer groups.'], 201);
        }

        // Proceed to delete if not linked
        $deleted = deleteWithBranch('center', 'idCenter', $id);

        if ($deleted) {
            return response()->json(['message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete data.'], 500);
        }
    }



    public function center_collection(Request $request){
        $date_from = $request->input('date_from');
        $collector_id = $request->input('collector_id');
        $center_id = $request->input('center_id');
        $center = tableWithBranch('center')->get();
        $collector = tableWithBranch('user')->where('collector','=','1')->get();

        // --- Expenses Query (Processing Fee) ---
        $expensesQuery = tableWithBranch('expences','expences')
            ->selectRaw("
            DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(reason, 'loan number: (', -1), ')', 1) as loan_number,
            expences.amount,
            expences.date,
            customer.First_Name as f_name,
            customer.Last_Name as l_name,
            customer.cus_number as cus_number,
            subquery.group_name,
            center.Name as center_name,
            u1.id as collector_id,
            u1.Full_Name as collected_user,
            company_bank_accounts.type,
            company_bank_accounts.Bank_Name as bank_name, 
            company_bank_accounts.Account_No as bank_account_number
        ")
            ->join(DB::raw("
            (SELECT Loan_No, Customer_idCustomer, Loan_Category_idLoan_Category, User_idUser, lending_officer_id 
             FROM customer_loan) as customer_loan_sub
        "), DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(expences.reason, 'loan number: (', -1), ')', 1)"), '=', 'customer_loan_sub.Loan_No')
            ->join('customer', 'customer_loan_sub.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw("
            (SELECT DISTINCT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
             FROM group_has_customer 
             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery
        "), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('user as u1', 'expences.user_id', '=', 'u1.id')  // Collected user
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'expences.bank_id')
            ->where('expences.type', '=', 'Income');

        if ($date_from) {
            $expensesQuery->where('expences.date', '=', $date_from);
        }
        if ($center_id != "0") {
            $expensesQuery->where('center.idCenter', '=', $center_id);
        }
        if (!empty($collector_id) && $collector_id != "0") {
            $expensesQuery->where('expences.user_id', '=', $collector_id);
        }

        $expenses = $expensesQuery->get()->map(function ($expense) {
            $expense->payment_type = 'Processing Fee';
            $expense->center_name = $expense->center_name ?? '-';
            $expense->group_name = $expense->group_name ?? '-';
            return $expense;
        });


        // --- Payments Query (Loan Payments) ---
        $paymentQuery = tableWithBranch('customer_payments','customer_payments')
            ->selectRaw("
            DISTINCT customer_payments.Amount as amount,
            customer_loan_sub.Loan_No as loan_number,
            CONCAT(customer_payments.Date, ' ', customer_payments.time) as date,
            customer.First_Name as f_name,
            customer.Last_Name as l_name,
            customer.cus_number as cus_number,
            subquery.group_name,
            center.Name as center_name,
            u1.id as collector_id,
            u1.Full_Name as collected_user,
            company_bank_accounts.type,
            company_bank_accounts.Bank_Name as bank_name, 
            company_bank_accounts.Account_No as bank_account_number
        ")
            ->join(DB::raw("
            (SELECT DISTINCT idCustomer_Loan, Loan_No, Customer_idCustomer, Loan_Category_idLoan_Category, User_idUser, lending_officer_id 
             FROM customer_loan) as customer_loan_sub
        "), 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan_sub.idCustomer_Loan')
            ->join('customer', 'customer_loan_sub.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw("
            (SELECT DISTINCT group_has_customer.cus_id, customer_group.Name as group_name, customer_group.center_id 
             FROM group_has_customer 
             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery
        "), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('user as u1', 'customer_payments.User_idUser', '=', 'u1.id')  // Collected user
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('company_bank_has_log', 'company_bank_has_log.payment_id', '=', 'customer_payments.idCustomer_Payments')
            ->leftJoin('company_bank_accounts', 'company_bank_accounts.Idbank', '=', 'company_bank_has_log.Bank_Account_Id')
            ->where('company_bank_accounts.Bank_Type', '=', 'Bank');

        if ($date_from) {
            $paymentQuery->where('customer_payments.Date', '=', $date_from);
        }
        if ($center_id != "0") {
            $paymentQuery->where('center.idCenter', '=', $center_id);
        }
        if (!empty($collector_id) && $collector_id != "0") {
            $paymentQuery->where('customer_payments.User_idUser', '=', $collector_id);
        }

        $payments = $paymentQuery->get()->map(function ($payment) {
            $payment->payment_type = 'Loan Payments';
            $payment->center_name = $payment->center_name ?? '-';
            $payment->group_name = $payment->group_name ?? '-';
            return $payment;
        });

        // --- Merge collections and remove duplicates ---
        $merge_query = $expenses->merge($payments)->unique();

        // --- Sort by center_name ---
        $sorted_merge_query = $merge_query->sortBy('center_name')->values();

        return view('pages.CenterWiseCollection', compact('center', 'sorted_merge_query', 'date_from', 'collector', 'center_id', 'collector_id'));
    }




    public function CenterWiseCollectionSummary(Request $request) {
        $date_from = $request->input('date_from');
        $collector_id = $request->input('collector_id');
        $collector = tableWithBranch('user')->where('collector', '=', '1')->get();

        // --- Expenses Query ---
        $expensesQuery = tableWithBranch('expences','expences')
            ->selectRaw("
            center.idCenter as center_id,
            center.No as center_No,
            center.Name as center_name,
            SUM(expences.amount) as total_amount,
            COUNT(expences.id) as transaction_count,
            'Processing Fee' as payment_type
        ")
            ->leftJoin(DB::raw("
            (SELECT Loan_No, Customer_idCustomer, Loan_Category_idLoan_Category, User_idUser, lending_officer_id 
             FROM customer_loan) as customer_loan_sub
        "), DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(expences.reason, 'loan number: (', -1), ')', 1)"), '=', 'customer_loan_sub.Loan_No')
            ->leftJoin('customer', 'customer_loan_sub.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw("
            (SELECT group_has_customer.cus_id, customer_group.center_id 
             FROM group_has_customer 
             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery
        "), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->where('expences.type', '=', 'Income')
            ->groupBy('center.idCenter', 'center.Name', 'center.No')
            ->orderBy('center.No', 'asc');  // Order by Center No

        if ($date_from) {
            $expensesQuery->where('expences.date', '=', $date_from);
        }

        if (!empty($collector_id) && $collector_id != "0") {
            $expensesQuery->where('expences.user_id', '=', $collector_id);
        }

        $expenses = $expensesQuery->get();


        // --- Payments Query ---
        $paymentQuery = tableWithBranch('customer_payments','customer_payments')
            ->selectRaw("
            center.idCenter as center_id,
            center.No as center_No,
            center.Name as center_name,
            SUM(customer_payments.Amount) as total_amount,
            COUNT(customer_payments.idCustomer_Payments) as transaction_count,
            'Loan Payments' as payment_type
        ")
            ->leftJoin(DB::raw("
            (SELECT idCustomer_Loan, Loan_No, Customer_idCustomer, Loan_Category_idLoan_Category, User_idUser, lending_officer_id 
             FROM customer_loan) as customer_loan_sub
        "), 'customer_payments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan_sub.idCustomer_Loan')
            ->leftJoin('customer', 'customer_loan_sub.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw("
            (SELECT group_has_customer.cus_id, customer_group.center_id 
             FROM group_has_customer 
             LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery
        "), 'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->groupBy('center.idCenter', 'center.Name', 'center.No')
            ->orderBy('center.No', 'asc');  // Order by Center No

        if (!empty($collector_id) && $collector_id != "0") {
            $paymentQuery->where('customer_payments.User_idUser', '=', $collector_id);
        }

        if ($date_from) {
            $paymentQuery->where('customer_payments.Date', '=', $date_from);
        }

        $payments = $paymentQuery->get();

        // --- Merge Queries (Ensuring All Payment Types Appear) ---
        $mergedData = collect();

// Push Expenses Data
        foreach ($expenses as $expense) {
            $mergedData->push($expense);
        }

// Push Payments Data
        foreach ($payments as $payment) {
            $mergedData->push($payment);
        }

// Ensure Data is Sorted by `center_No` after Merging
        $sortedData = $mergedData->sortBy('center_No')->values();

// Group by Center Name for Display, Keeping Order
        $finalGroupedData = $sortedData->groupBy('center_name');


        return view('pages.CenterWiseCollectionSummary', compact('finalGroupedData', 'date_from', 'collector', 'collector_id'));
    }



    public function root_wise(Request $request)
    {
        $date = $request->input('date_from');
        $route_id = $request->input('route_id');

        $route = tableWithBranch('route')->get();

        $hasPayments = DB::table('customer_payments')
            ->whereDate('Date', $date)
            ->exists();

        $collection = DB::table('installments')
            ->join('customer_loan as cl', 'installments.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan')
            ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
            ->leftJoin('route as r', 'c.route_id', '=', 'r.id_route')
            ->leftJoin('customer_payments as cp', function ($join) use ($date) {
                $join->on('installments.Customer_Loan_idCustomer_Loan', '=', 'cp.Customer_Loan_idCustomer_Loan')
                    ->whereDate('cp.Date', $date);
            })
            ->select(
                'r.name as route_name',
                'c.cus_number as customer_number',
                DB::raw("CONCAT(c.First_Name, ' ', c.Last_Name) as customer_name"),
                'cl.Loan_No as loan_number',
                'installments.Installment_Amount as installment_amount',
                DB::raw('SUM(cp.Amount) as paid_amount')
            )
            ->when($date, fn($q) => $q->whereDate('installments.Installment_Date', $date))
            ->when($route_id && $route_id != '0', fn($q) => $q->where('c.route_id', $route_id))
            ->where('cl.Status', 0) // ✅ Only active (ongoing) loans
            ->groupBy(
                'r.name',
                'c.cus_number',
                'c.First_Name',
                'c.Last_Name',
                'cl.Loan_No',
                'installments.Installment_Amount'
            )
            ->orderBy('r.name')
            ->orderBy('c.cus_number')
            ->get();

        return view('pages.RouteWiseCollection', compact('date', 'route', 'route_id', 'collection'));
    }

    /**
     * Get branch hierarchy data for dropdown
     */
    public function getBranchHierarchy($branchId)
    {
        try {
            // Fetch routes for the specific branch
            $routes = DB::table('route')
                ->where('branch_id', $branchId)
                ->select('id_route as id', 'name')
                ->orderBy('name')
                ->get();

            $hierarchyData = [];

            foreach ($routes as $route) {
                // Fetch centers for each route
                $centers = DB::table('center')
                    ->where('branch_id', $branchId)
                    ->where('route_id', $route->id)
                    ->select('idCenter as id', 'Name as name')
                    ->orderBy('Name')
                    ->get();

                $routeData = [
                    'id' => $route->id,
                    'name' => $route->name,
                    'centers' => []
                ];

                foreach ($centers as $center) {
                    // Fetch groups for each center
                    $groups = DB::table('customer_group')
                        ->where('branch_id', $branchId)
                        ->where('center_id', $center->id)
                        ->select('idCustomer_Group as id', 'Name as name')
                        ->orderBy('Name')
                        ->get();

                    $centerData = [
                        'id' => $center->id,
                        'name' => $center->name,
                        'groups' => []
                    ];

                    foreach ($groups as $group) {
                        // Fetch customers for each group
                        $customers = DB::table('customer')
                            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                            ->where('customer.branch_id', $branchId)
                            ->where('group_has_customer.group_id', $group->id)
                            ->select(
                                'customer.idCustomer as id',
                                DB::raw("CONCAT(customer.First_Name, ' ', customer.Last_Name) as name"),
                                'customer.cus_number'
                            )
                            ->orderBy('customer.First_Name')
                            ->get();

                        $groupData = [
                            'id' => $group->id,
                            'name' => $group->name,
                            'customer_count' => $customers->count(),
                            'customers' => $customers->toArray()
                        ];

                        $centerData['groups'][] = $groupData;
                    }

                    $routeData['centers'][] = $centerData;
                }

                $hierarchyData[] = $routeData;
            }

            return response()->json([
                'routes' => $hierarchyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch branch hierarchy',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function getGroupsByCenter($centerId)
    {
        $groups = DB::table('customer_group')
            ->where('center_id', $centerId)
            ->select('idCustomer_Group', 'Group_No', 'Name')
            ->orderBy('Group_No', 'asc')
            ->get();

        return response()->json($groups);
    }



}
