<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BankLogController;
use App\Http\Controllers\CapitalBalanceController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Store $session)
    {
        // Head Office aggregated dashboard: show all branches overview
        if ((int)session('head_branch') == session('branch_id')) {
            // Fetch active branches from session (excluding head office which is filtered in login)
            $branches = user_data('branches') ?? [];

            $branchMetrics = [];
            foreach ($branches as $b) {
                // Ensure array access for session data
                $branchId = $b['idBranch'];
                $branchName = $b['Name'];

                // Helper closure forcing branch scope manually
                $scoped = function ($table) use ($branchId) {
                    // dd($table,  $branchId);
                    return DB::table($table)->where($table . '.branch_id', $branchId);
                };

                $customers = $scoped('customer')->count();
                $loanPendingQ = $scoped('customer_loan')->where('Status', '-1');
                $loanCurrentQ = $scoped('customer_loan')->where('Status', '0');
                $loanSettledQ = $scoped('customer_loan')->where('Status', '1');
                $pendingCount = $loanPendingQ->count();
                $pendingAmount = $scoped('customer_loan')->where('Status', '-1')->sum('Amount');
                $currentCount = $loanCurrentQ->count();
                $currentAmount = $scoped('customer_loan')->where('Status', '0')->sum('Amount');
                $settledCount = $loanSettledQ->count();
                $portfolio = $scoped('installments')->sum('capital_balance');
                $todayInstallment = DB::table('installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id', $branchId)
                    ->where('customer_loan.branch_id', $branchId)
                    ->whereDate('installments.Installment_Date', date('Y-m-d'))
                    ->where('customer_loan.Status', '0')
                    ->sum('installments.Total_Balance');
                $todayCollected = $scoped('customer_payments')->where('Date', date('Y-m-d'))->sum('Amount');
                // arrears: overdue installments (date < today) still active
                $arrears = DB::table('installments')
                    ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
                    ->where('installments.branch_id', $branchId)
                    ->where('customer_loan.branch_id', $branchId)
                    ->where('customer_loan.Status', '0')
                    ->whereDate('installments.Installment_Date', '<', date('Y-m-d'))
                    ->sum('installments.Total_Balance');

                $branchMetrics[] = [
                    'id' => $branchId,
                    'name' => $branchName,
                    'customers' => $customers,
                    'pending_loans_count' => $pendingCount,
                    'pending_loans_amount' => (float)$pendingAmount,
                    'current_loans_count' => $currentCount,
                    'current_loans_amount' => (float)$currentAmount,
                    'settled_loans_count' => $settledCount,
                    'portfolio' => (float)$portfolio,
                    'today_installment' => (float)$todayInstallment,
                    'today_collected' => (float)$todayCollected,
                    'arrears' => (float)$arrears,
                ];
            }

            return view('ho-dashboard', [
                'branchMetrics' => $branchMetrics,
            ]);
        }

        if (!Schema::hasColumn('installments', 'Panelty_count')) {
            DB::statement(
                "ALTER TABLE `installments`
         ADD COLUMN `Panelty_count` VARCHAR(45) NOT NULL
         DEFAULT '0'"
            );
        }


        if (!Schema::hasColumn('loan_category', 'collection_date_type')) {
            DB::statement(
                "ALTER TABLE `loan_category`
         ADD COLUMN `collection_date_type` VARCHAR(45) NOT NULL
         DEFAULT 'same_as_installment'"
            );
        }


        if (!Schema::hasColumn('route', 'collection_type')) {
            DB::statement(
                "ALTER TABLE `route`
         ADD COLUMN `collection_type` VARCHAR(45) NOT NULL
         DEFAULT 'customizable'"
            );
        }

        if (!Schema::hasColumn('route', 'collection_date')) {
            DB::statement(
                "ALTER TABLE `route`
         ADD COLUMN `collection_date` VARCHAR(45) NOT NULL
         DEFAULT 'Monday'"
            );
        }

        if (!Schema::hasColumn('company', 'inv_customer_number')) {
            DB::statement("
        ALTER TABLE `company`
        ADD COLUMN `inv_customer_number` VARCHAR(45) NOT NULL DEFAULT '1'
    ");
        }



        $loan = tableWithBranch('customer_loan')->where('Status', '!=', '1')->get();
        $CapitalBalanceController = new CapitalBalanceController();
        foreach ($loan as $loans) {
            $CapitalBalanceController->create($loans->idCustomer_Loan);
        }


        //        $CapitalBalanceController->panelty_remove();


        // Call to the penalty creation function
        // $this->create_panelty();


        $customerCount = tableWithBranch('customer')->count();
        $customer_loan_pending_Count = tableWithBranch('customer_loan')->where('Status', '=', '-1')->count();
        $customer_loan_pending_Amount = tableWithBranch('customer_loan')->where('Status', '=', '-1')->sum('Amount');
        $customer_loan_current_Count = tableWithBranch('customer_loan')->where('Status', '=', '0')->count();
        $customer_loan_current_Amount = tableWithBranch('customer_loan')->where('Status', '=', '0')->sum('Amount');
        $setteled_loan_Count = tableWithBranch('customer_loan')->where('Status', '=', '1')->count();
        $setteled_loan_current_Amount = tableWithBranch('customer_loan')->where('Status', '=', '1')->sum('Amount');
        $deleted_loan_Count = tableWithBranch('customer_loan')->where('Status', '=', '3')->count(); // Added this

        $portfolio = tableWithBranch('installments')->sum('capital_balance');
        $currentMonthStart = date('Y-m-01 00:00:00'); // Start of month
        $todayEnd = date('Y-m-d 23:59:59');          // End of today

        $currentMonthLending = tableWithBranch('customer_loan')
            ->whereBetween('Date_Time', [$currentMonthStart, $todayEnd])
            ->where('Status', '0')
            ->sum('Amount');


        $todayinstallment_balance = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->whereDate('Installment_Date', '=', date('Y-m-d'))
            ->where('installments.Status', '=', '0')
            ->where('customer_loan.Status', '=', '0')
            ->sum('installments.Total_Balance');


        $todaycollection = tableWithBranch('customer_payments')
            ->whereDate('Date', '=', date('Y-m-d'))
            ->get();

        $todaycollected = tableWithBranch('customer_payments')
            ->whereDate('Date', '=', date('Y-m-d'))
            ->sum('Amount');


        $todayNotPaid = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('installments.Status', '=', '0')
            ->where('customer_loan.Status', '=', '0')
            ->whereDate('installments.Installment_Date', '=', date('Y-m-d'))
            ->sum('installments.Total_Balance');

        $checqueamount = tableWithBranch('Cheque_payment')->where('payment_date', date('Y-m-d'))->where('chq_status', '=', '0')->sum('payment_amount');
        $all_loan = $customer_loan_current_Count + $setteled_loan_Count;


        // Weekly Unpaid Calculation
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek(Carbon::SUNDAY);

        // Fetch weekly unpaid installments
        $weeklyUnpaidQuery = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('installments.Status', '0') // Not paid
            ->where('customer_loan.Status', '0') // Active loan
            ->whereBetween('installments.Installment_Date', [$startOfWeek, $today]);

        // Aggregates for Weekly Unpaid
        $weeklyUnpaidCount = $weeklyUnpaidQuery->count();
        $weeklyUnpaidAmount = $weeklyUnpaidQuery->sum('installments.Total_Balance');
        $weeklyUnpaidCustomerCount = $weeklyUnpaidQuery->distinct('customer_loan.Customer_idCustomer')->count('customer_loan.Customer_idCustomer');


        // Current Week Pending Payment Calculation
        // "Current Week" implies the full week (Sunday to Saturday), or maybe remaining days?
        // Usually "Pending Payment" means what is due for this week (future + past in this week)
        // Let's assume the full current week (Sunday to Saturday)
        $endOfWeek = $today->copy()->endOfWeek(Carbon::SATURDAY);

        $currentWeekPendingQuery = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->where('installments.Status', '0') // Not paid
            ->where('customer_loan.Status', '0') // Active loan
            ->whereBetween('installments.Installment_Date', [$startOfWeek, $endOfWeek]);

        $currentWeekPendingCount = $currentWeekPendingQuery->count();
        $currentWeekPendingAmount = $currentWeekPendingQuery->sum('installments.Total_Balance');
        $currentWeekPendingCustomerCount = $currentWeekPendingQuery->distinct('customer_loan.Customer_idCustomer')->count('customer_loan.Customer_idCustomer');


        // Shortcut Data
        $shortcut_count = tableWithBranch('shortcut')->count();
        $shortcut = tableWithBranch('shortcut')->get();


        $loanQuery = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->select('customer.*', 'customer_group.Name as group_name', 'installments.*', 'customer_loan.*', 'loan_category.Name as loan_name')
            ->whereDate('Installment_Date', '=', date('Y-m-d'))
            ->where('installments.Status', '=', '0')
            ->where('customer_loan.Status', '=', '0')
            ->get();


        $loanQuery_2 = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->select(
                DB::raw('SUM(CASE WHEN Installment_Date = CURDATE() THEN Total_Balance ELSE 0 END) as Today_installment'),
                DB::raw('SUM(CASE WHEN Installment_Date < CURDATE() THEN Total_Balance ELSE 0 END) as arrease'),
                DB::raw('SUM(CASE WHEN Installment_Date <= CURDATE() THEN Total_Balance ELSE 0 END) as Total_Balance_until')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();  // Try without grouping for now

        // Assign the values to variables
        $todayinstallment = $loanQuery_2->Today_installment;

        $arrease = $loanQuery_2->arrease;
        $totalBalanceUntil = $loanQuery_2->Total_Balance_until;
        $totalBalanceUntil = $totalBalanceUntil + $checqueamount;

        // Total Outstanding: capital balance + interest balance where status = 0
        $totalOutstanding = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.capital_balance + installments.Interest_Balance) as total_outstanding')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $totalOutstanding = $totalOutstanding->total_outstanding ?? 0;

        // Penalty Balance: sum of penalty balance where status = 0
        $penaltyBalance = tableWithBranch('installments', 'installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->select(
                DB::raw('SUM(installments.Panalty_Balance) as penalty_balance')
            )
            ->where('customer_loan.Status', '=', '0')
            ->first();
        $penaltyBalance = $penaltyBalance->penalty_balance ?? 0;

        $userid = user_data('idUser');

        $dashboard = 0;
        $privileges = user_data('privileges') ?? [];
        if (is_array($privileges)) {
            foreach ($privileges as $priv) {
                if (isset($priv['Description']) && strtolower($priv['Description']) === 'dashboard') {
                    $dashboard = 1;
                    break;
                }
            }
        }
        $currentYear = date('Y');

        $monthlyRevenue = tableWithBranch('customer_payments')
            ->select(
                DB::raw('MONTH(Date) as month'),
                DB::raw('SUM(Amount) as total')
            )
            ->whereYear('Date', $currentYear) // Filter by current year
            ->groupBy(DB::raw('MONTH(Date)'))
            ->orderBy(DB::raw('MONTH(Date)'))
            ->get();

        $monthlyData = array_fill(0, 12, 0); // Initialize with 12 zeros

        foreach ($monthlyRevenue as $item) {
            $monthlyData[$item->month - 1] = (float) $item->total;
        }


        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY); // Sun 2025-04-20 00:00:00
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY);     // Sat 2025-04-26 23:59:59
        $startOfLastWeek = $startOfWeek->copy()->subWeek();
        $endOfLastWeek = $endOfWeek->copy()->subWeek();


        $getPaymentsPerDay = function ($start, $end) {
            $results = tableWithBranch('customer_payments')
                ->select('Date', DB::raw('SUM(Amount) as total'))
                ->whereBetween('Date', [$start->toDateString(), $end->toDateString()])
                ->groupBy('Date')
                ->get();

            $week = array_fill(0, 7, 0);
            foreach ($results as $row) {
                $dayIndex = Carbon::parse($row->Date)->dayOfWeek; // 0 = Sun, ..., 6 = Sat
                $week[$dayIndex] += (float) $row->total;
            }

            return $week;
        };



        $weeklyComparison = [
            'current' => $getPaymentsPerDay($startOfWeek, $endOfWeek),
            'last' => $getPaymentsPerDay($startOfLastWeek, $endOfLastWeek),
        ];



        $profit = 907195;
        $profitTarget = 1000000; // 1 million





        //
        //          $sms=new Sms();
        //          $sms->index("0743513689","test");


        return view('home', compact(
            'currentMonthLending',
            'portfolio',
            'profit',
            'todaycollected',
            'profitTarget',
            'weeklyComparison',
            'deleted_loan_Count',
            'all_loan',
            'monthlyData',
            'dashboard',
            'checqueamount',
            'totalBalanceUntil',
            'arrease',
            'setteled_loan_current_Amount',
            'customer_loan_pending_Amount',
            'customer_loan_current_Amount',
            'setteled_loan_Count',
            'shortcut_count',
            'shortcut',
            'customerCount',
            'customer_loan_pending_Count',
            'customer_loan_current_Count',
            'todayinstallment',
            'todaycollection',
            'todayNotPaid',
            'weeklyUnpaidCount',
            'weeklyUnpaidAmount',
            'weeklyUnpaidCustomerCount',
            'totalOutstanding',
            'penaltyBalance',
            'currentWeekPendingCount',
            'currentWeekPendingAmount',
            'currentWeekPendingCustomerCount',
            'todayinstallment_balance'
        ));
    }
}
