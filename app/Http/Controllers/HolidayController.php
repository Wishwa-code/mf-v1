<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($skipFor, $targetId, $skipType, array $selectedDates = [])
    {
        $companySetting = tableWithBranch('company')->value('saturday_sunday');
        $holidays = tableWithBranch('holidays')
            ->when(!empty($selectedDates), function ($q) use ($selectedDates) {
                $q->whereIn('date', $selectedDates);
            })
            ->get();
        $branch_id=session('branch_id');
        foreach ($holidays as $holiday) {
            $holidayDate = $holiday->date;

            $loans = getTargetLoans($skipFor, $targetId);

            if ($skipFor=="branch"){
                $branch_id=$targetId;
            }

            foreach ($loans as $loan) {
                $installments = tableWithBranch('installments')
                    ->where('Installment_Date', $holidayDate)
                    ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                    ->get();

                foreach ($installments as $installment) {

                    if ($skipType === 'installment') {
                        processInstallmentSkip($loan, $installment, $companySetting,$branch_id);
                    } else {
                        processDaySkip($loan, $installment, $holidayDate, $companySetting,$branch_id);
                    }
                }
            }
        }

        $this->create($branch_id); // whatever this method does

    }



    /**
     * Show the form for creating a new resource.
     */
    public function create($branch_id)
    {
        $loan=DB::table('customer_loan')->where('branch_id','=',$branch_id)->get();
        foreach ($loan as $loans) {
            $insallment=DB::table('installments')->where('branch_id','=',$branch_id)->where('Customer_Loan_idCustomer_Loan','=',$loans->idCustomer_Loan)->get();
            $date=[];
            $panelty_date=[];
            foreach ($insallment as $installments) {
                $date[]=$installments->Installment_Date;
                $panelty_date[]=$installments->Panelty_date;
            }
            sort($date);
            sort($panelty_date);
            $count=0;
            foreach ($insallment as $new_installments) {
                DB::table('installments')->where('branch_id','=',$branch_id)->where('idInstallments', $new_installments->idInstallments)->update([
                    'Installment_Date' => $date[$count],
                    'Panelty_date' => $panelty_date[$count],
                ]);
                $count++;
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($loan_id)
    {
        // Fetch the saturday_sunday setting from the company table
        $companySetting = tableWithBranch('company')->value('saturday_sunday');
        $holidays=tableWithBranch('holidays')->get();

        foreach ($holidays as $holiday) {
            $holidayDate = $holiday->date; // Extracting the date correctly
            $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)->where('Installment_Date', $holidayDate)->get();

            foreach ($installments as $installment) {
                $loan_id=$installment->Customer_Loan_idCustomer_Loan;
                $newDate = Carbon::parse($holidayDate)->addDay(); // Start by adding one day
                Log::info("Normal installment date:".$newDate);

                // Loop to find the next valid date
                while (
                    tableWithBranch('holidays')->where('date', $newDate->toDateString())->exists() || // Avoid holidays
                    ($companySetting == "1" && ($newDate->isSaturday() || $newDate->isSunday())) || // Avoid weekends if setting is enabled
                    tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->where('Installment_Date', $newDate->toDateString())->exists() // Avoid existing installment dates
                ) {
                    $newDate->addDay(); // Keep adding days until a valid one is found
                }

                $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $installment->Customer_Loan_idCustomer_Loan)->first();
                $newpanelty_date = $newDate->toDateString();

                if ($loan) {
                    $product_id = $loan->Loan_Category_idLoan_Category;
                    $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $product_id)->first();
                    $panelty_date = $product->Panelty_date;
                    $newpanelty_date = Carbon::parse($newDate)->addDays((int) $panelty_date)->toDateString();

                }

                // Update the installment with the new valid date
                tableWithBranch('installments')->where('idInstallments', $installment->idInstallments)->update([
                    'Installment_Date' => $newDate->toDateString(),
                    'Panelty_date' => $newpanelty_date, // ✅ Fix applied here
                ]);
            }
        }
        $this->create();
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

    public function fetchPoya()
    {
        $response = Http::get('https://nextpoyawhen.com/poya.json');

        if ($response->successful()) {
            Log::info($response->json());
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Unable to fetch Poya days.'], 500);
    }
}
