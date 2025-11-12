<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenaltyDeductionController extends Controller
{

    protected $loanLogController;

    public function __construct(LoanLogController $loanLogController)
    {
        $this->loanLogController = $loanLogController;
    }


    public function index(Request $request)
    {
        // Load lightweight filters (optional – you can expand as needed)
        $groups = DB::table('customer_group')->orderBy('Name')->get();
        $loanCategories = DB::table('loan_category')->orderBy('Name')->get();
        $customers = DB::table('customer')->orderBy('First_Name')->get();

        return view('pages.penalty_deduction', [
            'group'         => $groups,
            'loan_category' => $loanCategories,
            'customers'     => $customers,
        ]);
    }

    public function load(Request $request)
    {
        // Filters (optional)
        $groupId      = (int) $request->input('group_id', 0);
        $categoryId   = (int) $request->input('category_id', 0);
        $customerId   = (int) $request->input('customer_id', 0);

        $branchId = (int) session('branch_id'); // your app uses branch scoping

        // Base loan query (scoped by branch)
        $loanQ = DB::table('customer_loan as cl')
            ->select([
                'cl.idCustomer_Loan',
                'cl.Loan_No',
                'cl.Customer_idCustomer',
                'cl.Loan_Category_idLoan_Category',
                'cl.Amount',
                'cl.capital_balance',
                'cl.Total_Loan_Amount',
                'cl.Balance_Amount',
                'cl.Date_Time',
                'cl.repayment_duration',
                'cl.Interest_period',
                'cl.branch_id',
                'c.First_Name',
                'c.Last_Name',
                'c.Nic',
                'lc.Name as Category_Name',
                'cg.Name as Group_Name',
                'cg.Leader_name',
                'cg.Contact_no',
                DB::raw('COALESCE(SUM(i.Panalty_Balance),0) as penalty_balance')
            ])
            ->join('customer as c', 'c.idCustomer', '=', 'cl.Customer_idCustomer')
            ->leftJoin('loan_category as lc', 'lc.idLoan_Category', '=', 'cl.Loan_Category_idLoan_Category')
            ->leftJoin('group_has_customer as ghc', function($j){
                $j->on('ghc.cus_id', '=', 'cl.Customer_idCustomer');
            })
            ->leftJoin('customer_group as cg', 'cg.idCustomer_Group', '=', 'ghc.group_id')
            ->join('installments as i', function($j){
                $j->on('i.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan');
            })
            ->where('cl.branch_id', $branchId)
            ->groupBy([
                'cl.idCustomer_Loan','cl.Loan_No','cl.Customer_idCustomer',
                'cl.Loan_Category_idLoan_Category','cl.Amount','cl.capital_balance',
                'cl.Total_Loan_Amount','cl.Balance_Amount','cl.Date_Time',
                'cl.repayment_duration','cl.Interest_period','cl.branch_id',
                'c.First_Name','c.Last_Name','c.Nic','lc.Name','cg.Name','cg.Leader_name','cg.Contact_no'
            ]);

        if ($groupId > 0) {
            $loanQ->where('ghc.group_id', $groupId);
        }
        if ($categoryId > 0) {
            $loanQ->where('cl.Loan_Category_idLoan_Category', $categoryId);
        }
        if ($customerId > 0) {
            $loanQ->where('cl.Customer_idCustomer', $customerId);
        }

        $loanNo = trim((string)$request->input('loan_no', ''));

        if ($loanNo !== '') {
            $loanQ->where('cl.Loan_No', 'like', '%' . $loanNo . '%');
        }


        // Only loans with positive penalty balance
        $rows = DB::query()->fromSub($loanQ, 'x')
            ->where('x.penalty_balance', '>', 0)
            ->orderBy('x.Date_Time', 'desc')
            ->get();

        // Prepare response with computed maturity date (best-effort)
        $data = [];
        foreach ($rows as $r) {
            $created = $r->Date_Time ? Carbon::parse($r->Date_Time) : null;
            $maturity = null;
            if ($created && !empty($r->repayment_duration)) {
                // assuming months
                $maturity = $created->copy()->addMonthsNoOverflow((int)$r->repayment_duration)->format('Y-m-d');
            }

            $data[] = [
                'loan_id'          => (int)$r->idCustomer_Loan,
                'loan_no'          => $r->Loan_No,
                'group'            => trim(($r->Group_Name ?? '—') . ($r->Leader_name ? ' / ' . $r->Leader_name : '')),
                'customer'         => trim($r->First_Name . ' ' . $r->Last_Name) . ' - ' . ($r->Nic ?? ''),
                'category'         => $r->Category_Name ?? '—',
                'loan_amount'      => number_format((float)$r->Amount, 2, '.', ','),
                'capital_balance'  => number_format((float)$r->capital_balance, 2, '.', ','),
                'total_amount'     => number_format((float)$r->Total_Loan_Amount, 2, '.', ','),
                'total_balance'    => number_format((float)$r->Balance_Amount, 2, '.', ','),
                'created_at'       => $created ? $created->format('Y-m-d') : '—',
                'maturity_date'    => $maturity ?? '—',
                'penalty_balance'  => number_format((float)$r->penalty_balance, 2, '.', ','),
                'penalty_raw'      => (float)$r->penalty_balance, // for action
            ];
        }

        // Summary
        $summary = [
            'count' => count($data),
            'total_penalty' => number_format(array_sum(array_map(fn($x) => (float)$x['penalty_raw'], $data)), 2, '.', ',')
        ];

        return response()->json([
            'data'    => $data,
            'summary' => $summary,
        ]);
    }

    public function deduct(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer|min:1',
            'amount'  => 'required|numeric|min:0.01',
        ]);

        $loanId   = (int) $request->input('loan_id');
        $amount   = round((float) $request->input('amount'), 2);
        $branchId = (int) session('branch_id');
        $userId   = (int) (auth()->id() ?? 0);

        try {
            DB::beginTransaction();

            // Lock loan for update
            $loan = DB::table('customer_loan')
                ->where('idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->first();

            if (!$loan) {
                DB::rollBack();
                return response()->json(['ok' => false, 'msg' => 'Loan not found.'], 404);
            }

            // Current total penalty balance
            $totalPenalty = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->sum('Panalty_Balance');

            if ($totalPenalty <= 0) {
                DB::rollBack();
                return response()->json(['ok' => false, 'msg' => 'No penalty to deduct.'], 422);
            }

            if ($amount > $totalPenalty + 1e-9) {
                DB::rollBack();
                return response()->json(['ok' => false, 'msg' => 'Amount exceeds penalty balance.'], 422);
            }

            // Oldest-first apportionment across installments with positive Panalty_Balance
            $rows = DB::table('installments')
                ->select('idInstallments','Panalty_Balance','Total_Balance')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->where('Panalty_Balance', '>', 0)
                ->orderBy('Installment_Date', 'asc')
                ->orderBy('idInstallments', 'asc')
                ->lockForUpdate()
                ->get();

            $remaining = $amount;

            foreach ($rows as $r) {
                if ($remaining <= 0) break;

                $apply = min((float)$r->Panalty_Balance, $remaining);

                // Reduce penalty + total balance on this installment
                DB::table('installments')
                    ->where('idInstallments', $r->idInstallments)
                    ->update([
                        'Panalty_Balance' => DB::raw('GREATEST(Panalty_Balance - ' . $apply . ', 0)'),
                        'Total_Balance'   => DB::raw('GREATEST(Total_Balance - '   . $apply . ', 0)'),
                        'Panelty_status'  => 2,
                    ]);

                $remaining -= $apply;
            }

            $applied = round($amount - max($remaining, 0), 2); // should equal $amount

            // Also reduce the loan's overall Balance_Amount (optional but typical)
            DB::table('customer_loan')
                ->where('idCustomer_Loan', $loanId)
                ->update([
                    'Balance_Amount' => DB::raw('GREATEST(Balance_Amount - ' . $applied . ', 0)')
                ]);

// ---- Fetch Latest Loan_Log BEFORE update (opening balances) ----
            $last_log = DB::table('Loan_Log')
                ->where('Loan_ID', $loanId)
                ->orderBy('Loan_Log_ID', 'desc')
                ->first();

            $Panelty_Balance_Log_before  = $last_log->Panelty_Balance ?? 0;
            $Interest_Balance_Log_before = $last_log->Interest_Balance ?? 0;
            $Capital_Balance_Log_before  = $last_log->Capital_Balance ?? 0;
            $Saving_Balance_Log_before   = $last_log->Saving_Account_Balance ?? 0;
            $Total_Pending_Balance_Log_before = $last_log->Total_Pending_Balance ?? 0;
            $Panelty_Balance_Log_before=$Panelty_Balance_Log_before-$applied;
            $Total_Pending_Balance_Log_before=$Total_Pending_Balance_Log_before-$applied;
// ---- Fetch NEW current balances AFTER deduction ----
            $newPenaltyBalance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->sum('Panalty_Balance');

            $newInterestBalance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->sum('Interest_Balance');

            $newCapitalBalance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->sum('capital_balance');

            $newSavingBalance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->where('branch_id', $branchId)
                ->sum('Saving_balance');

            $newTotalPendingBalance = $newPenaltyBalance + $newInterestBalance + $newCapitalBalance + $newSavingBalance;

// ---- Call Loan Log Controller (Penalty Deduction Entry) ----
            $this->loanLogController->index(
                $loanId,                    // Loan_ID
                'Penalty Deduction',        // Type
                '0',                       // Type_ID (no receipt ID needed here)
                'Penalty Deduction',        // Description
                $applied,                          // Payment Amount (not customer payment)
                0,                   // Penalty Paid (Deducted)
                0,                          // Interest Paid
                0,                          // Capital Paid
                0,                          // Saving Paid
                $Panelty_Balance_Log_before,
                $Interest_Balance_Log_before,
                $Capital_Balance_Log_before,
                $Total_Pending_Balance_Log_before,
                $Saving_Balance_Log_before
            );


            DB::commit();

            return response()->json([
                'ok'     => true,
                'msg'    => 'Penalty deducted successfully.',
                'amount' => number_format($applied, 2, '.', ','),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Penalty Deduction Error', [
                'loan_id' => $loanId,
                'ex' => $e->getMessage()
            ]);
            return response()->json(['ok' => false, 'msg' => 'Unexpected error occurred.'], 500);
        }
    }

}
