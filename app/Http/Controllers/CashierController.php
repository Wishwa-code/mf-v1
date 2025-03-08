<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $user_id = session('userid');
            $today = Carbon::now()->toDateString();

            // Check if today's record exists in `plot`
            $existingPlot = DB::table('plot')
                ->whereDate('Date_Time', $today)
                ->first();

            if ($existingPlot) {

                $total = (float) $existingPlot->total_amount;
                $newTotal = (float) $request->grandTotal;

                // Update total_amount and last updated fields
                DB::table('plot')
                    ->where('id_plot', $existingPlot->id_plot)
                    ->update([
                        'total_amount'      => $request->grandTotal,
                        'last_updated_time' => Carbon::now(),
                        'last_updated_user' => $user_id
                    ]);

                $balance_amount=$total-$newTotal;
                $bank=DB::table('company_bank_accounts')->where('Account_No','=',session('userid'))->first();
                if ($balance_amount < 0) {
                    $this->bankLogController->index($bank->Idbank, "Deposit", "Update Morning Plot", 'Update Morning Plot', "credit", abs($balance_amount),'1');
                    $this->bankLogController->index("1", "Withdraw", "Update Morning Plot", 'Update Morning Plot', "debit", abs($balance_amount),$bank->Idbank);
                } else {
                    $this->bankLogController->index($bank->Idbank, "Deposit", "Update Morning Plot", 'Update Morning Plot', "debit", $balance_amount,'1');
                    $this->bankLogController->index("1", "Withdraw", "Update Morning Plot", 'Update Morning Plot', "credit", $balance_amount,$bank->Idbank);
                }

                $plotId = $existingPlot->id_plot;

                // ✅ DELETE all existing records in `plot_has_money` for this `plot_id`
                DB::table('plot_has_money')->where('plot_id', $plotId)->delete();
            } else {
                // Insert new plot record
                $plotId = DB::table('plot')->insertGetId([
                    'total_amount'      => $request->grandTotal,
                    'Date_Time'         => Carbon::now(),
                    'user'              => $user_id,
                    'last_updated_time' => Carbon::now(),
                    'last_updated_user' => $user_id
                ]);
                $bank=DB::table('company_bank_accounts')->where('Account_No','=',session('userid'))->first();
                $this->bankLogController->index($bank->Idbank,"Deposit","Morning Plot",'Morning Plot',"credit",$request->grandTotal,'1');
                $this->bankLogController->index("1","Withdraw","Morning Plot",'Morning Plot',"debit",$request->grandTotal,$bank->Idbank);
            }


            // INSERT new entries in `plot_has_money`
            foreach ($request->entries as $entry) {
                DB::table('plot_has_money')->insert([
                    'plot_id' => $plotId,
                    'money'   => $entry['amount'],
                    'qty'     => $entry['quantity'],
                    'amount'  => $entry['totalAmount']
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
        $today = Carbon::now()->toDateString();

        // Fetch today's plot record
        $plot = DB::table('plot')
            ->whereDate('Date_Time', $today)
            ->first();

        if (!$plot) {
            return response()->json(['status' => 'success', 'entries' => [], 'grandTotal' => 0]);
        }

        // Fetch associated money entries
        $entries = DB::table('plot_has_money')
            ->where('plot_id', $plot->id_plot)
            ->get();

        return response()->json([
            'status' => 'success',
            'entries' => $entries,
            'grandTotal' => $plot->total_amount
        ]);
    }

    public function getDayEndData()
    {
        try {
            $today = Carbon::now()->toDateString();

            $bank=DB::table('company_bank_accounts')->where('Account_No','=',session('userid'))->first();
            // Fetch the starting cash (Plot Amount)
            $startingCash =DB::table('plot')
                ->whereDate('Date_Time', $today)
                ->value('total_amount');

            // Fetch total payments and bank amounts
            $paymentAmounts = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', 'LIKE', 'Customer Payment%')
                ->whereDate('Date_Time','=',$today)
                ->sum('Credit') ?? 0.00;;
            $deposit = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', '=', 'Deposit')
                ->whereDate('Date_Time','=',$today)
                ->sum('Credit') ?? 0.00;;

            $withdrawal = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', '=', 'Withdrawal')
                ->whereDate('Date_Time','=',$today)
                ->sum('Debit') ?? 0.00;;

            // Fetch other incomes
            $incomes = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', 'LIKE', 'Income%')
                ->whereDate('Date_Time','=',$today)
                ->get();;

            // Fetch pawning and expenses
            $pawningAmount = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', '=', 'Issued Pawning Ticket')
                ->whereDate('Date_Time','=',$today)
                ->sum('Debit') ?? 0.00;;

            $otherExpenses = DB::table('company_bank_has_log')
                ->where('Bank_Account_Id','=',$bank->Idbank)
                ->where('Type', 'LIKE', 'Expenses%')
                ->whereDate('Date_Time','=',$today)
                ->get();;





            return response()->json([
                'status' => 'success',
                'startingCash' => $startingCash,
                'paymentAmounts' => $paymentAmounts,
                'deposit' => $deposit,
                'withdrawal' => $withdrawal,
                'incomes' => $incomes,
                'pawningAmount' => $pawningAmount,
                'otherExpenses' => $otherExpenses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function saveDayEnd(Request $request)
    {
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
                'created_at' => now()
            ]);


            $bank=DB::table('company_bank_accounts')->where('Account_No','=',session('userid'))->first();
            $this->bankLogController->index($bank->Idbank,"Withdraw","Day End",'Day End',"debit",$request->cash_drawer_total,'1');
            $this->bankLogController->index("1","Deposit","Day End",'Day End',"credit",$request->cash_drawer_total,$bank->Idbank);

            // Save cash drawer entries
            $cashDrawerEntries = $request->cash_drawer_entries;
            if (!empty($cashDrawerEntries)) {
                foreach ($cashDrawerEntries as $entry) {
                    DB::table('cash_drawer_entries')->insert([
                        'day_end_id' => $dayEndId,
                        'denomination' => $entry['denomination'],
                        'quantity' => $entry['quantity'],
                        'total_amount' => $entry['total_amount'],
                        'created_at' => now()
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
        $today = now()->toDateString();
        $savedData = DB::table('day_end_summary')->where('date', $today)->first();

        if ($savedData) {
            $cashDrawerEntries = DB::table('cash_drawer_entries')
                ->where('day_end_id', $savedData->id)
                ->get();

            return response()->json([
                'status' => 'success',
                'saved' => true,
                'starting_cash' => $savedData->starting_cash,
                'total_income' => $savedData->total_income,
                'total_expense' => $savedData->total_expense,
                'balance_amount' => $savedData->balance_amount,
                'cash_drawer_total' => $savedData->cash_drawer_total,
                'balance_difference' => $savedData->balance_difference,
                'cash_drawer_entries' => $cashDrawerEntries,
            ]);
        }

        return response()->json(['status' => 'success', 'saved' => false]);
    }



}
