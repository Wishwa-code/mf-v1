<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashFlowController extends Controller
{
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
        //
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

    public function getCashFlowData(Request $request)
    {
        $selectedOption=$request->input('searchOption');

        // Get today's date
        $today = Carbon::today();

        $currentYear = $today->year;
        $april_1 = Carbon::create($currentYear, 4, 1);
        $march_1 = $today;

        if ($today->lessThan($april_1)) {
            $april_1->subYear();
        }

        $april_2 = $april_1->copy()->subYear();
        $march_2 = $april_1->copy()->subDay();

        $april_3 = $april_2->copy()->subYear();
        $march_3 = $april_2->copy()->subDay();

        $april_4 = $april_3->copy()->subYear();
        $march_4 = $april_3->copy()->subDay();

        $april_5 = $april_4->copy()->subYear();
        $march_5 = $april_4->copy()->subDay();





        $balance_before_one=0;
        $balance_before_two=0;
        $balance_before_three=0;
        $balance_before_four=0;
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_1->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_1->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_1)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_1)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_1)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_1)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_one = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_2->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_2->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_2)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_2)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_2)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_2)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_two = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
            if (true) {
            // Query to get the sum of the specified columns
                $sums = tableWithBranch('Loan_Log')
                    ->where('Date_Time', '<', $april_3->startOfDay())
                    ->select(
                        DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                        DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                        DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                        DB::raw('SUM(Savings_Payment) as total_savings_payment')
                    )
                    ->first();


                $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


                $loan_capital = tableWithBranch('Loan_Log')
                    ->where('Date_Time', '<', $april_3->startOfDay())
                    ->select(
                        DB::raw('SUM(Amount) as total_Capital_Balance')
                    )
                    ->first();


                $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

                $sums_income = tableWithBranch('expences')
                    ->where('date', '<', $april_3)
                    ->where('type', '=', 'Income')
                    ->select(
                        DB::raw('SUM(amount) as Income_total')
                    )
                    ->first();

                $assest_income = tableWithBranch('asset_management')
                    ->where('sold_date', '<', $april_3)
                    ->select(
                        DB::raw('SUM(sold_amount) as sold_amount_total')
                    )
                    ->first();


                $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



                $assest_purchased = tableWithBranch('asset_management')
                    ->where('purchase_date', '<', $april_3)
                    ->select(
                        DB::raw('SUM(purchase_value) as purchased_amount_total')
                    )
                    ->first();

                $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


                $sums_expense = tableWithBranch('expences')
                    ->where('date', '<', $april_3)
                    ->where('type', '=', 'Expense')
                    ->select(
                        DB::raw('SUM(amount) as Expense_total')
                    )
                    ->first();

                $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_three = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
            if (true) {
            // Query to get the sum of the specified columns
                $sums = tableWithBranch('Loan_Log')
                    ->where('Date_Time', '<', $april_4->startOfDay())
                    ->select(
                        DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                        DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                        DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                        DB::raw('SUM(Savings_Payment) as total_savings_payment')
                    )
                    ->first();


                $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


                $loan_capital = tableWithBranch('Loan_Log')
                    ->where('Date_Time', '<', $april_4->startOfDay())
                    ->select(
                        DB::raw('SUM(Amount) as total_Capital_Balance')
                    )
                    ->first();


                $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

                $sums_income = tableWithBranch('expences')
                    ->where('date', '<', $april_4)
                    ->where('type', '=', 'Income')
                    ->select(
                        DB::raw('SUM(amount) as Income_total')
                    )
                    ->first();

                $assest_income = tableWithBranch('asset_management')
                    ->where('sold_date', '<', $april_4)
                    ->select(
                        DB::raw('SUM(sold_amount) as sold_amount_total')
                    )
                    ->first();


                $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



                $assest_purchased = tableWithBranch('asset_management')
                    ->where('purchase_date', '<', $april_4)
                    ->select(
                        DB::raw('SUM(purchase_value) as purchased_amount_total')
                    )
                    ->first();

                $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


                $sums_expense = tableWithBranch('expences')
                    ->where('date', '<', $april_4)
                    ->where('type', '=', 'Expense')
                    ->select(
                        DB::raw('SUM(amount) as Expense_total')
                    )
                    ->first();

                $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_four = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }


        if ($selectedOption === 'oneYear') {

            // Query to get the sum of the specified columns using the helper function
            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            // Query to get the total capital balance using the helper function
            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            // Query to get the total income from expenses using the helper function
            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get the total sold amount from asset management using the helper function
            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            // Query to get the total purchased amount from asset management using the helper function
            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            // Query to get the total expenses using the helper function
            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
            ];

        }else if($selectedOption==="twoYear"){
// Query to get the sum of the specified columns
            // Query to get the sum of the specified columns using the helper function
            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            // Query to get the sum of the specified columns using the helper function
            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');


            // Query to get the total capital balance using the helper function
            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            // Query to get the total capital balance for the second period using the helper function
            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            // Query to get the total income from expenses using the helper function
            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get the total sold amount from asset management using the helper function
            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            // Query to get the total income from expenses using the helper function
            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get the total sold amount from asset management using the helper function
            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            // Query to get the total purchased amount from asset management using the helper function
            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            // Query to get the total expense amount using the helper function
            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            // Query to get the total asset purchase amount using the helper function
            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            // Query to get the total expense amount using the helper function
            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
            ];
        }else if($selectedOption==="threeYear"){

            // Query to get the sum of specified columns using the helper function
            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            // Query to get the sum of specified columns using the helper function
            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');




            // Query to get the sum of specified columns using the helper function
            $sums_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3->startOfDay(), $march_3->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_3 = number_format($sums_3->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_3 = number_format($sums_3->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_3 = number_format($sums_3->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_3 = number_format($sums_3->total_savings_payment, 2, '.', '');






            // Query to get the total capital balance using the helper function
            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            // Query to get the total capital balance for the second period using the helper function
            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            // Query to get the total capital balance for the third period using the helper function
            $loan_capital_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_3 = number_format($loan_capital_3->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_3 = (float) str_replace(',', '', $totalPaneltyPayment_3);
            $totalInterestPayment_3 = (float) str_replace(',', '', $totalInterestPayment_3);
            $totalCapitalPayment_3 = (float) str_replace(',', '', $totalCapitalPayment_3);
            $totalSavingsPayment_3 = (float) str_replace(',', '', $totalSavingsPayment_3);



            // Query to get total income for the specified period using the helper function
            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get total sold amount for asset management using the helper function
            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            // Query to get total income for the specified period using the helper function
            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get total sold amount for asset management using the helper function
            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            // Query to get total income for the specified period using the helper function
            $sums_income_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

// Query to get total sold amount for asset management using the helper function
            $assest_income_3 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_3 = number_format($sums_income_3->Income_total, 2);
            $soldAmountTotal_3 = number_format($assest_income_3->sold_amount_total, 2);

            $incomeTotal_3 = (float) str_replace(',', '', $incomeTotal_3);
            $soldAmountTotal_3 = (float) str_replace(',', '', $soldAmountTotal_3);



            // Query to get total asset purchases for the specified period using the helper function
            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $assest_purchased_3 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_3 = number_format($assest_purchased_3->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_3 = number_format(
                $totalCapitalPayment_3 +
                $totalInterestPayment_3+
                $totalPaneltyPayment_3+
                $totalSavingsPayment_3+
                $incomeTotal_3+
                $soldAmountTotal_3,
                2,
                '.',
                ''
            );


            $sums_expense_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_3 = number_format($sums_expense_3->Expense_total, 2);

            $total_payments_3 = number_format($total_Capital_Balance_3+(float) str_replace(',', '', $purchased_amount_total_3)+(float) str_replace(',', '', $sums_expense_3), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loan_capital_repayments_3' => number_format($totalCapitalPayment_3, 2, '.', ','),
                'loan_interest_repayments_3' => number_format($totalInterestPayment_3, 2, '.', ','),
                'loan_panalty_repayments_3' => number_format($totalPaneltyPayment_3, 2, '.', ','),
                'savings_deposits_3' => number_format($totalSavingsPayment_3, 2, '.', ','),
                'other_income_3' => number_format($incomeTotal_3, 2, '.', ','),
                'assets_sale_3' => number_format($soldAmountTotal_3, 2, '.', ','),
                'total_receipts_3' => number_format($totalReceipts_3, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),

                'loans_released_3' => number_format($total_Capital_Balance_3, 2, '.', ','),
                'asset_purchased_3' => $purchased_amount_total_3,
                'other_expenses_3' => $sums_expense_3,
                'total_payments_3' => $total_payments_3,
                'total_cash_balance_3' => number_format(
                    (float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_3' => number_format($balance_before_three, 2, '.', ','),
                'total_balance_3' => number_format($balance_before_three+(float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
                'date_5' => $april_3->toFormattedDateString(),
                'date_6' => $march_3->toFormattedDateString(),
            ];
        }else if($selectedOption==="fourYear"){

            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');




            $sums_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3->startOfDay(), $march_3->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_3 = number_format($sums_3->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_3 = number_format($sums_3->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_3 = number_format($sums_3->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_3 = number_format($sums_3->total_savings_payment, 2, '.', '');



            $sums_4 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_4->startOfDay(), $march_4->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_4 = number_format($sums_4->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_4 = number_format($sums_4->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_4 = number_format($sums_4->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_4 = number_format($sums_4->total_savings_payment, 2, '.', '');



            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            $loan_capital_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_3 = number_format($loan_capital_3->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_3 = (float) str_replace(',', '', $totalPaneltyPayment_3);
            $totalInterestPayment_3 = (float) str_replace(',', '', $totalInterestPayment_3);
            $totalCapitalPayment_3 = (float) str_replace(',', '', $totalCapitalPayment_3);
            $totalSavingsPayment_3 = (float) str_replace(',', '', $totalSavingsPayment_3);


            $loan_capital_4 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_4 = number_format($loan_capital_4->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_4 = (float) str_replace(',', '', $totalPaneltyPayment_4);
            $totalInterestPayment_4 = (float) str_replace(',', '', $totalInterestPayment_4);
            $totalCapitalPayment_4 = (float) str_replace(',', '', $totalCapitalPayment_4);
            $totalSavingsPayment_4 = (float) str_replace(',', '', $totalSavingsPayment_4);


            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            $sums_income_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_3 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_3 = number_format($sums_income_3->Income_total, 2);
            $soldAmountTotal_3 = number_format($assest_income_3->sold_amount_total, 2);

            $incomeTotal_3 = (float) str_replace(',', '', $incomeTotal_3);
            $soldAmountTotal_3 = (float) str_replace(',', '', $soldAmountTotal_3);




            $sums_income_4 = tableWithBranch('expences')
                ->whereBetween('date', [$april_4, $march_4])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_4 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();

            $incomeTotal_4 = number_format($sums_income_4->Income_total, 2);
            $soldAmountTotal_4 = number_format($assest_income_4->sold_amount_total, 2);

            $incomeTotal_4 = (float) str_replace(',', '', $incomeTotal_4);
            $soldAmountTotal_4 = (float) str_replace(',', '', $soldAmountTotal_4);






            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $assest_purchased_3 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_3 = number_format($assest_purchased_3->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_3 = number_format(
                $totalCapitalPayment_3 +
                $totalInterestPayment_3+
                $totalPaneltyPayment_3+
                $totalSavingsPayment_3+
                $incomeTotal_3+
                $soldAmountTotal_3,
                2,
                '.',
                ''
            );


            $sums_expense_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_3 = number_format($sums_expense_3->Expense_total, 2);

            $total_payments_3 = number_format($total_Capital_Balance_3+(float) str_replace(',', '', $purchased_amount_total_3)+(float) str_replace(',', '', $sums_expense_3), 2, '.', ',');









            $assest_purchased_4 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_4 = number_format($assest_purchased_4->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_4 = number_format(
                $totalCapitalPayment_4 +
                $totalInterestPayment_4+
                $totalPaneltyPayment_4+
                $totalSavingsPayment_4+
                $incomeTotal_4+
                $soldAmountTotal_4,
                2,
                '.',
                ''
            );


            $sums_expense_4 = tableWithBranch('expences')
                ->whereBetween('date', [$april_4, $march_4])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_4 = number_format($sums_expense_4->Expense_total, 2);

            $total_payments_4 = number_format($total_Capital_Balance_4+(float) str_replace(',', '', $purchased_amount_total_4)+(float) str_replace(',', '', $sums_expense_4), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loan_capital_repayments_3' => number_format($totalCapitalPayment_3, 2, '.', ','),
                'loan_interest_repayments_3' => number_format($totalInterestPayment_3, 2, '.', ','),
                'loan_panalty_repayments_3' => number_format($totalPaneltyPayment_3, 2, '.', ','),
                'savings_deposits_3' => number_format($totalSavingsPayment_3, 2, '.', ','),
                'other_income_3' => number_format($incomeTotal_3, 2, '.', ','),
                'assets_sale_3' => number_format($soldAmountTotal_3, 2, '.', ','),
                'total_receipts_3' => number_format($totalReceipts_3, 2, '.', ','),


                'loan_capital_repayments_4' => number_format($totalCapitalPayment_4, 2, '.', ','),
                'loan_interest_repayments_4' => number_format($totalInterestPayment_4, 2, '.', ','),
                'loan_panalty_repayments_4' => number_format($totalPaneltyPayment_4, 2, '.', ','),
                'savings_deposits_4' => number_format($totalSavingsPayment_4, 2, '.', ','),
                'other_income_4' => number_format($incomeTotal_4, 2, '.', ','),
                'assets_sale_4' => number_format($soldAmountTotal_4, 2, '.', ','),
                'total_receipts_4' => number_format($totalReceipts_4, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),

                'loans_released_3' => number_format($total_Capital_Balance_3, 2, '.', ','),
                'asset_purchased_3' => $purchased_amount_total_3,
                'other_expenses_3' => $sums_expense_3,
                'total_payments_3' => $total_payments_3,
                'total_cash_balance_3' => number_format(
                    (float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_3' => number_format($balance_before_three, 2, '.', ','),
                'total_balance_3' => number_format($balance_before_three+(float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3), 2, '.', ','),


                'loans_released_4' => number_format($total_Capital_Balance_4, 2, '.', ','),
                'asset_purchased_4' => $purchased_amount_total_4,
                'other_expenses_4' => $sums_expense_4,
                'total_payments_4' => $total_payments_4,
                'total_cash_balance_4' => number_format(
                    (float) str_replace(',', '', $totalReceipts_4) - (float) str_replace(',', '', $total_payments_4),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_4' => number_format($balance_before_four, 2, '.', ','),
                'total_balance_4' => number_format($balance_before_four+(float) str_replace(',', '', $totalReceipts_4) - (float) str_replace(',', '', $total_payments_4), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
                'date_5' => $april_3->toFormattedDateString(),
                'date_6' => $march_3->toFormattedDateString(),
                'date_7' => $april_4->toFormattedDateString(),
                'date_8' => $march_4->toFormattedDateString(),
            ];


        }

        return response()->json($data);


    }





    public function getCashFlowDataMonthly(Request $request)
    {
        $selectedOption=$request->input('searchOption');

        // Get today's date
        $today = Carbon::today();

        $currentYear = $today->year;
        $currentMonth = $today->month;
        $april_1 = Carbon::create($currentYear, $currentMonth, 01);
        $march_1 = $today;

        // Calculate dates for the previous periods
        $april_2 = $april_1->copy()->subMonth();
        $march_2 = $april_2->copy()->addMonth()->subDay();

        $april_3 = $april_2->copy()->subMonth();
        $march_3 = $april_3->copy()->addMonth()->subDay();

        $april_4 = $april_3->copy()->subMonth();
        $march_4 = $april_4->copy()->addMonth()->subDay();

        $april_5 = $april_4->copy()->subMonth();
        $march_5 = $april_5->copy()->addMonth()->subDay();


        Log::info($april_3);
        Log::info($march_3);

        $balance_before_one=0;
        $balance_before_two=0;
        $balance_before_three=0;
        $balance_before_four=0;
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_1->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_1->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_1)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_1)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_1)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_1)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_one = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_2->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_2->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_2)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_2)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_2)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_2)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_two = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_3->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_3->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_3)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_3)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_3)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_3)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_three = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }
        if (true) {
            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_4->startOfDay())
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->where('Date_Time', '<', $april_4->startOfDay())
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->where('date', '<', $april_4)
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->where('sold_date', '<', $april_4)
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->where('purchase_date', '<', $april_4)
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->where('date', '<', $april_4)
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $balance_before_four = (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments);

        }


        if ($selectedOption === 'oneYear') {

            // Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');

            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);

            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);



            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
            ];

        }else if($selectedOption==="twoYear"){
// Query to get the sum of the specified columns
            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');


            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();

            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
            ];
        }else if($selectedOption==="threeYear"){

            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');




            $sums_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3->startOfDay(), $march_3->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_3 = number_format($sums_3->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_3 = number_format($sums_3->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_3 = number_format($sums_3->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_3 = number_format($sums_3->total_savings_payment, 2, '.', '');






            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            $loan_capital_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_3 = number_format($loan_capital_3->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_3 = (float) str_replace(',', '', $totalPaneltyPayment_3);
            $totalInterestPayment_3 = (float) str_replace(',', '', $totalInterestPayment_3);
            $totalCapitalPayment_3 = (float) str_replace(',', '', $totalCapitalPayment_3);
            $totalSavingsPayment_3 = (float) str_replace(',', '', $totalSavingsPayment_3);



            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            $sums_income_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_3 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_3 = number_format($sums_income_3->Income_total, 2);
            $soldAmountTotal_3 = number_format($assest_income_3->sold_amount_total, 2);

            $incomeTotal_3 = (float) str_replace(',', '', $incomeTotal_3);
            $soldAmountTotal_3 = (float) str_replace(',', '', $soldAmountTotal_3);



            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $assest_purchased_3 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_3 = number_format($assest_purchased_3->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_3 = number_format(
                $totalCapitalPayment_3 +
                $totalInterestPayment_3+
                $totalPaneltyPayment_3+
                $totalSavingsPayment_3+
                $incomeTotal_3+
                $soldAmountTotal_3,
                2,
                '.',
                ''
            );


            $sums_expense_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_3 = number_format($sums_expense_3->Expense_total, 2);

            $total_payments_3 = number_format($total_Capital_Balance_3+(float) str_replace(',', '', $purchased_amount_total_3)+(float) str_replace(',', '', $sums_expense_3), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loan_capital_repayments_3' => number_format($totalCapitalPayment_3, 2, '.', ','),
                'loan_interest_repayments_3' => number_format($totalInterestPayment_3, 2, '.', ','),
                'loan_panalty_repayments_3' => number_format($totalPaneltyPayment_3, 2, '.', ','),
                'savings_deposits_3' => number_format($totalSavingsPayment_3, 2, '.', ','),
                'other_income_3' => number_format($incomeTotal_3, 2, '.', ','),
                'assets_sale_3' => number_format($soldAmountTotal_3, 2, '.', ','),
                'total_receipts_3' => number_format($totalReceipts_3, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),

                'loans_released_3' => number_format($total_Capital_Balance_3, 2, '.', ','),
                'asset_purchased_3' => $purchased_amount_total_3,
                'other_expenses_3' => $sums_expense_3,
                'total_payments_3' => $total_payments_3,
                'total_cash_balance_3' => number_format(
                    (float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_3' => number_format($balance_before_three, 2, '.', ','),
                'total_balance_3' => number_format($balance_before_three+(float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
                'date_5' => $april_3->toFormattedDateString(),
                'date_6' => $march_3->toFormattedDateString(),
            ];
        }else if($selectedOption==="fourYear"){

            $sums = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1->startOfDay(), $march_1->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment = number_format($sums->total_panelty_payment, 2, '.', '');
            $totalInterestPayment = number_format($sums->total_interest_payment, 2, '.', '');
            $totalCapitalPayment = number_format($sums->total_capital_payment, 2, '.', '');
            $totalSavingsPayment = number_format($sums->total_savings_payment, 2, '.', '');

            $sums_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2->startOfDay(), $march_2->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_2 = number_format($sums_2->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_2 = number_format($sums_2->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_2 = number_format($sums_2->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_2 = number_format($sums_2->total_savings_payment, 2, '.', '');




            $sums_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3->startOfDay(), $march_3->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_3 = number_format($sums_3->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_3 = number_format($sums_3->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_3 = number_format($sums_3->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_3 = number_format($sums_3->total_savings_payment, 2, '.', '');



            $sums_4 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_4->startOfDay(), $march_4->endOfDay()])
                ->select(
                    DB::raw('SUM(Panelty_Payment) as total_panelty_payment'),
                    DB::raw('SUM(Interest_Payment) as total_interest_payment'),
                    DB::raw('SUM(Capital_Payment) as total_capital_payment'),
                    DB::raw('SUM(Savings_Payment) as total_savings_payment')
                )
                ->first();


            $totalPaneltyPayment_4 = number_format($sums_4->total_panelty_payment, 2, '.', '');
            $totalInterestPayment_4 = number_format($sums_4->total_interest_payment, 2, '.', '');
            $totalCapitalPayment_4 = number_format($sums_4->total_capital_payment, 2, '.', '');
            $totalSavingsPayment_4 = number_format($sums_4->total_savings_payment, 2, '.', '');



            $loan_capital = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance = number_format($loan_capital->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment = (float) str_replace(',', '', $totalPaneltyPayment);
            $totalInterestPayment = (float) str_replace(',', '', $totalInterestPayment);
            $totalCapitalPayment = (float) str_replace(',', '', $totalCapitalPayment);
            $totalSavingsPayment = (float) str_replace(',', '', $totalSavingsPayment);



            $loan_capital_2 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_2 = number_format($loan_capital_2->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_2 = (float) str_replace(',', '', $totalPaneltyPayment_2);
            $totalInterestPayment_2 = (float) str_replace(',', '', $totalInterestPayment_2);
            $totalCapitalPayment_2 = (float) str_replace(',', '', $totalCapitalPayment_2);
            $totalSavingsPayment_2 = (float) str_replace(',', '', $totalSavingsPayment_2);



            $loan_capital_3 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_3 = number_format($loan_capital_3->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_3 = (float) str_replace(',', '', $totalPaneltyPayment_3);
            $totalInterestPayment_3 = (float) str_replace(',', '', $totalInterestPayment_3);
            $totalCapitalPayment_3 = (float) str_replace(',', '', $totalCapitalPayment_3);
            $totalSavingsPayment_3 = (float) str_replace(',', '', $totalSavingsPayment_3);


            $loan_capital_4 = tableWithBranch('Loan_Log')
                ->whereBetween('Date_Time', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(Amount) as total_Capital_Balance')
                )
                ->first();


            $total_Capital_Balance_4 = number_format($loan_capital_4->total_Capital_Balance, 2, '.', '');

            // Remove commas from formatted numbers and convert to float
            $totalPaneltyPayment_4 = (float) str_replace(',', '', $totalPaneltyPayment_4);
            $totalInterestPayment_4 = (float) str_replace(',', '', $totalInterestPayment_4);
            $totalCapitalPayment_4 = (float) str_replace(',', '', $totalCapitalPayment_4);
            $totalSavingsPayment_4 = (float) str_replace(',', '', $totalSavingsPayment_4);


            $sums_income = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal = number_format($sums_income->Income_total, 2);
            $soldAmountTotal = number_format($assest_income->sold_amount_total, 2);

            $incomeTotal = (float) str_replace(',', '', $incomeTotal);
            $soldAmountTotal = (float) str_replace(',', '', $soldAmountTotal);





            $sums_income_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_2 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_2 = number_format($sums_income_2->Income_total, 2);
            $soldAmountTotal_2 = number_format($assest_income_2->sold_amount_total, 2);

            $incomeTotal_2 = (float) str_replace(',', '', $incomeTotal_2);
            $soldAmountTotal_2 = (float) str_replace(',', '', $soldAmountTotal_2);





            $sums_income_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_3 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_3 = number_format($sums_income_3->Income_total, 2);
            $soldAmountTotal_3 = number_format($assest_income_3->sold_amount_total, 2);

            $incomeTotal_3 = (float) str_replace(',', '', $incomeTotal_3);
            $soldAmountTotal_3 = (float) str_replace(',', '', $soldAmountTotal_3);




            $sums_income_4 = tableWithBranch('expences')
                ->whereBetween('date', [$april_4, $march_4])
                ->where('type', '=', 'Income')
                ->select(
                    DB::raw('SUM(amount) as Income_total')
                )
                ->first();

            $assest_income_4 = tableWithBranch('asset_management')
                ->whereBetween('sold_date', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(sold_amount) as sold_amount_total')
                )
                ->first();


            $incomeTotal_4 = number_format($sums_income_4->Income_total, 2);
            $soldAmountTotal_4 = number_format($assest_income_4->sold_amount_total, 2);

            $incomeTotal_4 = (float) str_replace(',', '', $incomeTotal_4);
            $soldAmountTotal_4 = (float) str_replace(',', '', $soldAmountTotal_4);






            $assest_purchased = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_1, $march_1])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total = number_format($assest_purchased->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts = number_format(
                $totalCapitalPayment +
                $totalInterestPayment+
                $totalPaneltyPayment+
                $totalSavingsPayment+
                $incomeTotal+
                $soldAmountTotal,
                2,
                '.',
                ''
            );


            $sums_expense = tableWithBranch('expences')
                ->whereBetween('date', [$april_1, $march_1])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense = number_format($sums_expense->Expense_total, 2);

            $total_payments = number_format($total_Capital_Balance+(float) str_replace(',', '', $purchased_amount_total)+(float) str_replace(',', '', $sums_expense), 2, '.', ',');







            $assest_purchased_2 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_2, $march_2])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_2 = number_format($assest_purchased_2->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_2 = number_format(
                $totalCapitalPayment_2 +
                $totalInterestPayment_2+
                $totalPaneltyPayment_2+
                $totalSavingsPayment_2+
                $incomeTotal_2+
                $soldAmountTotal_2,
                2,
                '.',
                ''
            );


            $sums_expense_2 = tableWithBranch('expences')
                ->whereBetween('date', [$april_2, $march_2])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_2 = number_format($sums_expense_2->Expense_total, 2);

            $total_payments_2 = number_format($total_Capital_Balance_2+(float) str_replace(',', '', $purchased_amount_total_2)+(float) str_replace(',', '', $sums_expense_2), 2, '.', ',');






            $assest_purchased_3 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_3, $march_3])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_3 = number_format($assest_purchased_3->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_3 = number_format(
                $totalCapitalPayment_3 +
                $totalInterestPayment_3+
                $totalPaneltyPayment_3+
                $totalSavingsPayment_3+
                $incomeTotal_3+
                $soldAmountTotal_3,
                2,
                '.',
                ''
            );


            $sums_expense_3 = tableWithBranch('expences')
                ->whereBetween('date', [$april_3, $march_3])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_3 = number_format($sums_expense_3->Expense_total, 2);

            $total_payments_3 = number_format($total_Capital_Balance_3+(float) str_replace(',', '', $purchased_amount_total_3)+(float) str_replace(',', '', $sums_expense_3), 2, '.', ',');









            $assest_purchased_4 = tableWithBranch('asset_management')
                ->whereBetween('purchase_date', [$april_4, $march_4])
                ->select(
                    DB::raw('SUM(purchase_value) as purchased_amount_total')
                )
                ->first();

            $purchased_amount_total_4 = number_format($assest_purchased_4->purchased_amount_total, 2);

            // Clean numbers before summing
            $totalReceipts_4 = number_format(
                $totalCapitalPayment_4 +
                $totalInterestPayment_4+
                $totalPaneltyPayment_4+
                $totalSavingsPayment_4+
                $incomeTotal_4+
                $soldAmountTotal_4,
                2,
                '.',
                ''
            );


            $sums_expense_4 = tableWithBranch('expences')
                ->whereBetween('date', [$april_4, $march_4])
                ->where('type', '=', 'Expense')
                ->select(
                    DB::raw('SUM(amount) as Expense_total')
                )
                ->first();

            $sums_expense_4 = number_format($sums_expense_4->Expense_total, 2);

            $total_payments_4 = number_format($total_Capital_Balance_4+(float) str_replace(',', '', $purchased_amount_total_4)+(float) str_replace(',', '', $sums_expense_4), 2, '.', ',');



            $data = [
                'loan_capital_repayments_1' => number_format($totalCapitalPayment, 2, '.', ','),
                'loan_interest_repayments_1' => number_format($totalInterestPayment, 2, '.', ','),
                'loan_panalty_repayments_1' => number_format($totalPaneltyPayment, 2, '.', ','),
                'savings_deposits_1' => number_format($totalSavingsPayment, 2, '.', ','),
                'other_income_1' => number_format($incomeTotal, 2, '.', ','),
                'assets_sale_1' => number_format($soldAmountTotal, 2, '.', ','),
                'total_receipts_1' => number_format($totalReceipts, 2, '.', ','),

                'loan_capital_repayments_2' => number_format($totalCapitalPayment_2, 2, '.', ','),
                'loan_interest_repayments_2' => number_format($totalInterestPayment_2, 2, '.', ','),
                'loan_panalty_repayments_2' => number_format($totalPaneltyPayment_2, 2, '.', ','),
                'savings_deposits_2' => number_format($totalSavingsPayment_2, 2, '.', ','),
                'other_income_2' => number_format($incomeTotal_2, 2, '.', ','),
                'assets_sale_2' => number_format($soldAmountTotal_2, 2, '.', ','),
                'total_receipts_2' => number_format($totalReceipts_2, 2, '.', ','),

                'loan_capital_repayments_3' => number_format($totalCapitalPayment_3, 2, '.', ','),
                'loan_interest_repayments_3' => number_format($totalInterestPayment_3, 2, '.', ','),
                'loan_panalty_repayments_3' => number_format($totalPaneltyPayment_3, 2, '.', ','),
                'savings_deposits_3' => number_format($totalSavingsPayment_3, 2, '.', ','),
                'other_income_3' => number_format($incomeTotal_3, 2, '.', ','),
                'assets_sale_3' => number_format($soldAmountTotal_3, 2, '.', ','),
                'total_receipts_3' => number_format($totalReceipts_3, 2, '.', ','),


                'loan_capital_repayments_4' => number_format($totalCapitalPayment_4, 2, '.', ','),
                'loan_interest_repayments_4' => number_format($totalInterestPayment_4, 2, '.', ','),
                'loan_panalty_repayments_4' => number_format($totalPaneltyPayment_4, 2, '.', ','),
                'savings_deposits_4' => number_format($totalSavingsPayment_4, 2, '.', ','),
                'other_income_4' => number_format($incomeTotal_4, 2, '.', ','),
                'assets_sale_4' => number_format($soldAmountTotal_4, 2, '.', ','),
                'total_receipts_4' => number_format($totalReceipts_4, 2, '.', ','),

                'loans_released_1' => number_format($total_Capital_Balance, 2, '.', ','),
                'asset_purchased_1' => $purchased_amount_total,
                'other_expenses_1' => $sums_expense,
                'total_payments_1' => $total_payments,
                'total_cash_balance_1' => number_format(
                    (float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_1' => number_format($balance_before_one, 2, '.', ','),
                'total_balance_1' => number_format($balance_before_one+(float) str_replace(',', '', $totalReceipts) - (float) str_replace(',', '', $total_payments), 2, '.', ','),

                'loans_released_2' => number_format($total_Capital_Balance_2, 2, '.', ','),
                'asset_purchased_2' => $purchased_amount_total_2,
                'other_expenses_2' => $sums_expense_2,
                'total_payments_2' => $total_payments_2,
                'total_cash_balance_2' => number_format(
                    (float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_2' => number_format($balance_before_two, 2, '.', ','),
                'total_balance_2' => number_format($balance_before_two+(float) str_replace(',', '', $totalReceipts_2) - (float) str_replace(',', '', $total_payments_2), 2, '.', ','),

                'loans_released_3' => number_format($total_Capital_Balance_3, 2, '.', ','),
                'asset_purchased_3' => $purchased_amount_total_3,
                'other_expenses_3' => $sums_expense_3,
                'total_payments_3' => $total_payments_3,
                'total_cash_balance_3' => number_format(
                    (float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_3' => number_format($balance_before_three, 2, '.', ','),
                'total_balance_3' => number_format($balance_before_three+(float) str_replace(',', '', $totalReceipts_3) - (float) str_replace(',', '', $total_payments_3), 2, '.', ','),


                'loans_released_4' => number_format($total_Capital_Balance_4, 2, '.', ','),
                'asset_purchased_4' => $purchased_amount_total_4,
                'other_expenses_4' => $sums_expense_4,
                'total_payments_4' => $total_payments_4,
                'total_cash_balance_4' => number_format(
                    (float) str_replace(',', '', $totalReceipts_4) - (float) str_replace(',', '', $total_payments_4),
                    2,
                    '.',
                    ','
                ),
                'previous_balance_4' => number_format($balance_before_four, 2, '.', ','),
                'total_balance_4' => number_format($balance_before_four+(float) str_replace(',', '', $totalReceipts_4) - (float) str_replace(',', '', $total_payments_4), 2, '.', ','),


                'date_1' => $april_1->toFormattedDateString(),
                'date_2' => $march_1->toFormattedDateString(),
                'date_3' => $april_2->toFormattedDateString(),
                'date_4' => $march_2->toFormattedDateString(),
                'date_5' => $april_3->toFormattedDateString(),
                'date_6' => $march_3->toFormattedDateString(),
                'date_7' => $april_4->toFormattedDateString(),
                'date_8' => $march_4->toFormattedDateString(),
            ];


        }

        return response()->json($data);


    }


    public function trialBalance(Request $request){
// Example trial balance data
        $searchOption = $request->input('searchOption');
        $dateRange = explode(' to ', $searchOption); // Split the string by ' to '
        $dateTo = trim($dateRange[1]);

        $loan = tableWithBranch('customer_loan') // Use DB facade if you're not using Eloquent
                ->where('Date_Time', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
                ->where('Status', 0) // Filter by Status
                ->sum('capital_balance'); // Sum the capital_balance column

        $client=tableWithBranch('Savings_Account_Log')
            ->where('Date_Time', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
            ->where('Type', "=","Deposit")
            ->sum('Credit');

        $interest_loan=tableWithBranch('Loan_Log')
            ->where('Date_Time', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
            ->sum('Interest_Payment');

        $interest_saving=tableWithBranch('Savings_Account_Log')
            ->where('Date_Time', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
            ->where('Type', "=","Interest")
            ->sum('Credit');

        $expenses=tableWithBranch('expences')
            ->where('date', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
            ->where('type', "=","Expense")
            ->sum('amount');

        $earning = tableWithBranch('expences')
            ->where('date', '<=', $dateTo) // Filter by date
            ->where('type', '=', 'Income') // Filter by type
            ->where('reason', 'NOT LIKE', '%Other loan charges for loan number:%') // Correctly check for 'not contains'
            ->sum('amount'); // Sum the amount column


        $otherchargers = tableWithBranch('expences') // Use DB facade if you're not using Eloquent
        ->where('date', '<=', $dateTo) // Correctly filter by date
        ->where('type', '=', 'Income') // Filter by type being "Income"
        ->where('reason', 'LIKE', '%Other loan charges for loan number:%') // Use LIKE for 'contains' functionality
        ->sum('amount'); // Sum the amount column


        $loan_interest=tableWithBranch('customer_loan')
            ->where('Date_Time', '<=', $dateTo) // Correct the syntax for 'less than or equal to'
            ->where('Status', 0)
            ->sum('installment_balance');

        $data = [
            ['account' => 'Loan Portfolio', 'debit' => $loan, 'credit' => 0.00],
            ['account' => 'Client Savings', 'debit' => 0.00, 'credit' => $client],
            ['account' => 'Interest Income (Loans)', 'debit' => 0.00, 'credit' => $interest_loan],
            ['account' => 'Loan Other Chargers', 'debit' => 0.00, 'credit' => $otherchargers],
            ['account' => 'Interest Payable (Savings)', 'debit' =>$interest_saving, 'credit' => 0.00],
            ['account' => 'Other Earnings', 'debit' => 0.00, 'credit' => $earning],
            ['account' => 'Expenses', 'debit' => $expenses, 'credit' => 0.00],
            ['account' => 'Loan Interest Receivable', 'debit' => $loan_interest, 'credit' => 0.00],
            ['account' => 'Capital', 'debit' => 0.00, 'credit' => 0.00],
            ['account' => 'Retained Earnings', 'debit' => 0.00, 'credit' => 0.00],
        ];

        return response()->json($data);
    }




}
