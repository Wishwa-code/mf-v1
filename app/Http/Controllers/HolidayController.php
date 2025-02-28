<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Fetch the saturday_sunday setting from the company table
        $companySetting = tableWithBranch('company')->value('saturday_sunday');
        $holidays=tableWithBranch('holidays')->get();

        foreach ($holidays as $holiday) {
            $holidayDate = $holiday->date; // Extracting the date correctly
            $installments = tableWithBranch('installments')->where('Installment_Date', $holidayDate)->where('Total_Balance','<',1)->get();

            foreach ($installments as $installment) {
                $newDate = Carbon::parse($holidayDate)->addDay(); // Start by adding one day

                // Loop to find the next valid date
                while (
                    tableWithBranch('holidays')->where('date', $newDate->toDateString())->exists() || // Avoid holidays
                    ($companySetting == "1" && ($newDate->isSaturday() || $newDate->isSunday())) // Avoid weekends if setting is enabled
                ) {
                    $newDate->addDay(); // Keep adding days until a valid one is found
                }

                // Update the installment with the new valid date
                tableWithBranch('installments')->where('idInstallments', $installment->idInstallments)->update([
                    'Installment_Date' => $newDate->toDateString(),
                ]);
            }
        }
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
}
