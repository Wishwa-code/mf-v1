<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{

    protected $bankLogController;

    public function __construct(BankLogController $bankLogController)
    {
        $this->bankLogController = $bankLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_id = session('user_data')["idUser"];
            $branch_id = session('branch_id');

            $lastDayEnd = DB::table('day_end_summary')
                ->where('branch_id', $branch_id)
                ->whereDate('date', '<', Carbon::today()) // last day before today
                ->orderByDesc('date')
                ->first();


            if ($lastDayEnd && floatval($lastDayEnd->balance_difference) != 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot proceed. Previous Day End has a balance difference of ' . number_format($lastDayEnd->balance_difference, 2) . '. Please resolve it before continuing.'
                ], 400);
            }



            $today = Carbon::now()->toDateString();

            // Check if today's record exists in `plot`
            $existingPlot = tableWithBranch('plot')
                ->whereDate('Date_Time', $today)
                ->first();

            if ($existingPlot) {

                $total = (float) $existingPlot->total_amount;
                $newTotal = (float) $request->grandTotal;

                // Update total_amount and last updated fields
                DB::table('plot')
                    ->where('id_plot', $existingPlot->id_plot)
                    ->where('branch_id', $branch_id)
                    ->update([
                        'total_amount'      => $request->grandTotal,
                        'last_updated_time' => Carbon::now(),
                        'last_updated_user' => $user_id
                    ]);


                $user = tableWithBranch('user')->where('id', '=', $user_id)->first();
                if ($user) {
                    $cashier = $user->cashier;
                    if ($cashier == "1") {
                        $balance_amount = $total - $newTotal;
                        $bank = tableWithBranch('company_bank_accounts')->where('Account_No', '=', session('userid'))->first();
                        if ($bank) {
                            $cash = tableWithBranch('company_bank_accounts')->where('Account_No', '=', "Cash")->first();
                            if ($balance_amount < 0) {
                                $this->bankLogController->index($bank->Idbank, "Deposit", "Update Morning Plot", "Update Morning Plot", "credit", $newTotal, $cash->Idbank);
                                $this->bankLogController->index($cash->Idbank, "Withdraw", "Update Morning Plot", "Update Morning Plot", "debit", $newTotal, $cash->Idbank);
                            } else {
                                $this->bankLogController->index($bank->Idbank, "Deposit", "Update Morning Plot", "Update Morning Plot", "debit", $newTotal, $cash->Idbank);
                                $this->bankLogController->index($cash->Idbank, "Withdraw", "Update Morning Plot", "Update Morning Plot", "credit", $newTotal, $cash->Idbank);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json(['status' => 'error', 'message' => "Bank log not updated"], 500);
                        }
                    } else {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => "This account has no access to cashier"], 500);
                    }
                } else {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => "This account has no access to cashier"], 500);
                }





                $plotId = $existingPlot->id_plot;

                // ✅ DELETE all existing records in `plot_has_money` for this `plot_id`
                DB::table('plot_has_money')->where('plot_id', $plotId)->where('branch_id', $branch_id)->delete();
            } else {
                $newTotal = (float) $request->grandTotal;
                // Insert new plot record
                $plotId = DB::table('plot')->insertGetId([
                    'total_amount'      => $request->grandTotal,
                    'Date_Time'         => Carbon::now(),
                    'user'              => $user_id,
                    'last_updated_time' => Carbon::now(),
                    'last_updated_user' => $user_id,
                    'branch_id' => $branch_id
                ]);
                $user = tableWithBranch('user')->where('id', '=', $user_id)->first();
                if ($user) {
                    $cashier = $user->cashier;
                    if ($cashier == "1") {
                        $bank = tableWithBranch('company_bank_accounts')->where('Account_No', '=', session('userid'))->first();
                        if ($bank) {
                            $cash = tableWithBranch('company_bank_accounts')->where('Account_No', '=', "Cash")->first();
                            $this->bankLogController->index($bank->Idbank, "Deposit", "Morning Plot", "Morning Plot", "debit", $newTotal, $cash->Idbank);
                            $this->bankLogController->index($cash->Idbank, "Withdraw", "Morning Plot", "Morning Plot", "credit", $newTotal, $bank->Idbank);
                        } else {
                            DB::rollBack();
                            return response()->json(['status' => 'error', 'message' => "Bank log not updated"], 500);
                        }
                    } else {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => "This account has no access to cashier"], 500);
                    }
                } else {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => "This account has no access to cashier"], 500);
                }
            }


            // INSERT new entries in `plot_has_money`
            foreach ($request->entries as $entry) {
                DB::table('plot_has_money')->insert([
                    'plot_id' => $plotId,
                    'money'   => $entry['amount'],
                    'qty'     => $entry['quantity'],
                    'amount'  => $entry['totalAmount'],
                    'branch_id' => $branch_id
                ]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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

    public function getTodayData()
    {
        $today = \Carbon\Carbon::now()->toDateString();
        $plot = tableWithBranch('plot')
            ->whereDate('Date_Time', $today)
            ->first();

        if ($plot) {
            $entries = tableWithBranch('plot_has_money')
                ->where('plot_id', $plot->id_plot)
                ->get();

            return response()->json(['status' => 'success', 'entries' => $entries, 'isFinalized' => true]);
        } else {
            return response()->json(['status' => 'success', 'entries' => [], 'isFinalized' => false]);
        }
    }

    public function getDayEndData()
    {
        $today = \Carbon\Carbon::today();
        $branchId = session('branch_id');
        $userId = session('user_data')["idUser"];

        $plot = tableWithBranch('plot')->whereDate('Date_Time', $today)->first();
        $startingCash = $plot ? floatval($plot->total_amount) : 0;

        // Fetch account for the logged-in user
        $bankAccount = DB::table('company_bank_accounts')
            ->where('Account_No', $userId)
            ->where('branch_id', $branchId)
            ->first();

        $cashIn = [];
        $cashOut = [];

        if ($bankAccount) {

            $cashIn = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', $bankAccount->Idbank)
                ->whereDate('Date_Time', $today)
                ->whereNotNull('Debit')
                ->where('Debit', '>', 0)
                ->get();

            $cashOut = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', $bankAccount->Idbank)
                ->whereDate('Date_Time', $today)
                ->whereNotNull('Credit')
                ->where('Credit', '>', 0)
                ->get();
        }

        $totalCashIn = collect($cashIn)->sum('Debit');
        $totalCashOut = collect($cashOut)->sum('Credit');

        return response()->json([
            'startingCash' => $startingCash,
            'balanceAmount' => $totalCashIn - $totalCashOut,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'totalIncome' => $totalCashIn,
            'totalExpenses' => $totalCashOut,
        ]);
    }


    public function saveDayEnd(Request $request)
    {
        $branchId = session('branch_id');
        DB::beginTransaction();
        try {
            // Save main day-end data
            $dayEndId = DB::table('day_end_summary')->insertGetId([
                'date' => now()->toDateString(),
                'starting_cash' => $request->starting_cash,
                'total_income' => $request->total_income,
                'total_expense' => $request->total_expense,
                'balance_amount' => $request->balance_amount,
                'cash_drawer_total' => $request->cash_drawer_total,
                'balance_difference' => $request->balance_difference,
                'created_at' => now(),
                'branch_id' => $branchId
            ]);
            $selectedBankId = $request->input('selected_bank_id');

            $bank = DB::table('company_bank_accounts')->where('Account_No', '=', session('userid'))->first();
            $this->bankLogController->index($bank->Idbank, "Withdraw", "Day End", 'Day End', "debit", $request->cash_drawer_total, $selectedBankId);
            $this->bankLogController->index($selectedBankId, "Deposit", "Day End", 'Day End', "credit", $request->cash_drawer_total, $bank->Idbank);

            // Save cash drawer entries
            $cashDrawerEntries = $request->cash_drawer_entries;
            if (!empty($cashDrawerEntries)) {
                foreach ($cashDrawerEntries as $entry) {
                    DB::table('cash_drawer_entries')->insert([
                        'day_end_id' => $dayEndId,
                        'denomination' => $entry['denomination'],
                        'quantity' => $entry['quantity'],
                        'total_amount' => $entry['total_amount'],
                        'created_at' => now(),
                        'branch_id' => $branchId
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Day End data saved successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getSavedDayEndData()
    {
        $today = \Carbon\Carbon::today();
        $branchId = session('branch_id');
        $userId = session('user_data')["idUser"];

        // Check for existing saved day end
        $dayEnd = DB::table('day_end_summary')
            ->whereDate('date', $today)
            ->where('branch_id', $branchId)
            ->first();

        // Get plot amount
        $startingCash = DB::table('plot')
            ->whereDate('Date_Time', $today)
            ->where('branch_id', $branchId)
            ->value('total_amount') ?? 0;

        // Find bank account for the logged-in user
        $bankAccount = DB::table('company_bank_accounts')
            ->where('Account_No', $userId)
            ->where('branch_id', $branchId)
            ->first();

        $cashIn = collect();
        $cashOut = collect();

        if ($bankAccount) {

            $cashIn = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', $bankAccount->Idbank)
                ->whereDate('Date_Time', $today)
                ->whereNotNull('Debit')
                ->where('Debit', '>', 0)
                ->get();

            $cashOut = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id', $bankAccount->Idbank)
                ->whereDate('Date_Time', $today)
                ->whereNotNull('Credit')
                ->where('Credit', '>', 0)
                ->get();
        }

        $totalIncome = $cashIn->sum('Debit');
        $totalExpenses = $cashOut->sum('Credit');
        $balanceAmount = $startingCash + $totalIncome - $totalExpenses;

        $cashDrawer = $dayEnd
            ? DB::table('cash_drawer_entries')->where('day_end_id', $dayEnd->id)->get()
            : [];

        return response()->json([
            'startingCash' => $startingCash,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'balanceAmount' => $balanceAmount,
            'dayEndExists' => (bool) $dayEnd,
            'savedData' => $dayEnd,
            'cashDrawerEntries' => $cashDrawer,
        ]);
    }

    public function getBankList()
    {
        $banks = tableWithBranch('company_bank_accounts')
            ->where('Bank_Type', 'Bank')
            ->get(['Idbank', 'Bank_Name', 'Account_Name']);

        return response()->json($banks);
    }
}
