<?php

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




