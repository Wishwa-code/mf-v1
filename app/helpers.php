<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function numberToWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function tableWithBranch($table, $useBranchIdFromTable = null)
{
    $query = DB::table($table);

    // Check if we need to use a specific table's branch_id for filtering
    if ($useBranchIdFromTable) {
        return $query->where("$useBranchIdFromTable.branch_id", session('branch_id'));
    }

    // If table is loan_category, also apply status = 1 filter
    if ($table === 'loan_category') {
        $query->where('status', 1);
    }

    return $query->where('branch_id', session('branch_id'));
}


function insertWithBranch($table, $data)
{
    // Add the branch_id from the session to the data
    $data['branch_id'] = session('branch_id');

    // Insert into the table and return the inserted ID
    return DB::table($table)->insertGetId($data);
}

function updateWithBranch($table, $idField, $idValue, $data)
{
    // Update the row where idField matches and the branch_id from the session
    return DB::table($table)
        ->where($idField, $idValue)
        ->where('branch_id', session('branch_id')) // Ensure it's scoped by branch
        ->update($data);
}


function deleteWithBranch($table, $idField, $idValue)
{
    return DB::table($table)
        ->where($idField, $idValue)
        ->where('branch_id', session('branch_id')) // Ensure it's scoped by branch
        ->delete();
}


function updateOrInsertWithBranch($table, $conditions, $data)
{
    // Ensure branch_id is included in the conditions
    $conditions['branch_id'] = session('branch_id');

    // Use updateOrInsert
    return DB::table($table)->updateOrInsert($conditions, $data);
}



if (!function_exists('formatNegativeInParentheses')) {
    function formatNegativeInParentheses($value) {
        if ($value < 0) {
            return '(' . number_format(abs($value), 2, '.', ',') . ')';
        }
        return number_format($value, 2, '.', ',');
    }
}


if (!function_exists('formatName')) {
    function formatName($firstName, $lastName) {
        $firstInitial = strtoupper(substr(explode(' ', trim($firstName))[0], 0, 1)) . '.';
        $lastNameParts = explode(' ', trim($lastName));
        $lastInitial = count($lastNameParts) > 1 ? strtoupper(substr($lastNameParts[0], 0, 1)) . '.' : '';
        $formattedLastName = end($lastNameParts);
        return trim(($lastInitial ? $firstInitial . $lastInitial : $firstInitial) . ' ' . $formattedLastName);
    }
}


function getTargetLoans($skipFor, $targetId)
{
    if ($skipFor === 'all') {
        return tableWithBranch('customer_loan')->where('Status', '0')->get();
    }

    if ($skipFor === 'loan') {
        return tableWithBranch('customer_loan')->where('idCustomer_Loan', $targetId)->get();
    }

    if ($skipFor === 'branch') {
        return DB::table('customer_loan')->where('branch_id', $targetId)->get();
    }

    if ($skipFor === 'center') {
        return tableWithBranch('customer_loan')->where('Center_Id', $targetId)->where('Status', '0')->get();
    }

    if ($skipFor === 'product') {
        return tableWithBranch('customer_loan')->where('Loan_Category_idLoan_Category', $targetId)->where('Status', '0')->get();
    }

    return collect(); // empty if none match
}

function processInstallmentSkip($loan, $installment, $companySetting,$branch_id)
{
    $product = DB::table('loan_category')->where('branch_id','=',$branch_id)->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();
    $Repayment_type = $product->Repayment_type;
    $max_date = DB::table('installments')
        ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
        ->where('branch_id','=',$branch_id)
        ->max('Installment_Date');

    $newDate = Carbon::parse($max_date);

    switch ($Repayment_type) {
        case 'Daily':
            $newDate->addDay();
            break;
        case 'Weekly':
            $newDate->addDays(7);
            break;
        case 'Twice A Month':
            $newDate->addDays(14);
            break;
        case 'First Of The Month':
            $newDate = $newDate->addMonthNoOverflow()->startOfMonth();
            break;
        case 'End Of The Month':
            $newDate = $newDate->addMonthNoOverflow()->endOfMonth();
            break;
        case 'On A Selected Date':
            $newDate = $newDate->addMonthNoOverflow(); // same date next month
            break;
    }

    // Adjust if invalid date
    while (
        DB::table('holidays')->where('branch_id','=',$branch_id)->where('date', $newDate->toDateString())->exists() ||
        ($companySetting == "1" && ($newDate->isSaturday() || $newDate->isSunday()))
    ) {
        if ($Repayment_type == "End Of The Month") {
            $newDate->subDay();
        } else {
            $newDate->addDay();
        }
    }

    $newPaneltyDate = $newDate->copy()->addDays((int) $product->Panelty_date)->toDateString();

    DB::table('installments')->where('branch_id','=',$branch_id)->where('idInstallments', $installment->idInstallments)->update([
        'Installment_Date' => $newDate->toDateString(),
        'Panelty_date' => $newPaneltyDate,
    ]);
}


function processDaySkip($loan, $installment, $holidayDate, $companySetting,$branch_id)
{
    $newDate = Carbon::parse($holidayDate)->addDay();
    $loan_id = $installment->Customer_Loan_idCustomer_Loan;

    while (
        DB::table('holidays')->where('branch_id','=',$branch_id)->where('date', $newDate->toDateString())->exists() ||
        ($companySetting == "1" && ($newDate->isSaturday() || $newDate->isSunday())) ||
        DB::table('installments')->where('branch_id','=',$branch_id)->where('Customer_Loan_idCustomer_Loan', $loan_id)->where('Installment_Date', $newDate->toDateString())->exists()
    ) {
        $newDate->addDay();
    }

    $product = DB::table('loan_category')->where('branch_id','=',$branch_id)->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();
    $newPaneltyDate = $newDate->copy()->addDays((int) $product->Panelty_date)->toDateString();

    DB::table('installments')->where('branch_id','=',$branch_id)->where('idInstallments', $installment->idInstallments)->update([
        'Installment_Date' => $newDate->toDateString(),
        'Panelty_date' => $newPaneltyDate,
    ]);
}






