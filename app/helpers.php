<?php

use App\Http\Controllers\CustomerLogController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

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

    if (!auth()->check()) {
        return redirect()->route('login')->with("error", "Session expired! Please Login");
    }

    if (!Schema::hasColumn('customer_loan', 'panelty_method')) {
        DB::statement(
            "ALTER TABLE `customer_loan`
         ADD COLUMN `panelty_method` VARCHAR(45) NOT NULL
         DEFAULT 'every_installment'"
        );
    }

    if (!Schema::hasColumn('customer_loan', 'Panelty_period')) {
        DB::statement(
            "ALTER TABLE `customer_loan`
         ADD COLUMN `Panelty_period` VARCHAR(45) NOT NULL
         DEFAULT 'Daily'"
        );
    }

    $query = DB::table($table);

    // If a specific table alias/name is given for branch scoping and it has branch_id
    if ($useBranchIdFromTable && Schema::hasColumn($useBranchIdFromTable, 'branch_id')) {
        return $query->where("$useBranchIdFromTable.branch_id", session('branch_id'));
    }

    // Apply status filter for loan_category if present
    if ($table === 'loan_category') {
        $query->where('status', 1);
    }

    // Only scope by branch if this table actually has a branch_id column
    if (Schema::hasColumn($table, 'branch_id')) {
        return $query->where('branch_id', session('branch_id'));
    }

    // Fallback: no branch column, return unscoped query
    return $query;
}


function insertWithBranch($table, $data)
{
    // Add branch_id if the table has that column
    if (Schema::hasColumn($table, 'branch_id')) {
        $data['branch_id'] = session('branch_id');
    }

    return DB::table($table)->insertGetId($data);
}

function updateWithBranch($table, $idField, $idValue, $data)
{
    $builder = DB::table($table)->where($idField, $idValue);

    // Scope by branch only if applicable
    if (Schema::hasColumn($table, 'branch_id')) {
        $builder->where('branch_id', session('branch_id'));
    }

    return $builder->update($data);
}


function deleteWithBranch($table, $idField, $idValue)
{
    $builder = DB::table($table)->where($idField, $idValue);

    if (Schema::hasColumn($table, 'branch_id')) {
        $builder->where('branch_id', session('branch_id'));
    }

    return $builder->delete();
}


function updateOrInsertWithBranch($table, $conditions, $data)
{
    // Ensure branch_id is included in the conditions if column exists
    if (Schema::hasColumn($table, 'branch_id')) {
        $conditions['branch_id'] = session('branch_id');
    }

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
        $firstName = trim((string)$firstName);
        $lastName = trim((string)$lastName);

        if (empty($firstName)) {
            return $lastName;
        }

        $firstNameParts = explode(' ', $firstName);
        $initials = [];
        foreach ($firstNameParts as $part) {
            if (!empty($part)) {
                $initials[] = strtoupper(substr($part, 0, 1)) . '.';
            }
        }

        return trim(implode('', $initials) . ' ' . $lastName);
    }
}

// Format member display name according to app setting
if (!function_exists('format_member_name')) {
    /**
     * @param string|null $first First name(s)
     * @param string|null $last  Last name(s)
     * @param string $mode one of: full_name | with_initial | only_first_name | only_last_name
     * @return string
     */
    function format_member_name(?string $first, ?string $last, string $mode = 'with_initial'): string
    {
        $first = trim((string)($first ?? ''));
        $last  = trim((string)($last ?? ''));

        switch ($mode) {
            case 'full_name':
                return trim($first . ' ' . $last);
            case 'only_first_name':
                return $first;
            case 'only_last_name':
                return $last;
            case 'with_initial':
            default:
                return formatName($first, $last);
        }
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


//function customer_number($cus_id){
//    // Load customer & company
//    $customer = tableWithBranch('customer')->where('idCustomer', $cus_id)->first();
//    if (!$customer) { return; }
//
//    $old_cus_number = $customer->cus_number ?? null;
//
//    $company = tableWithBranch('company')->first();
//    if (!$company) { return; }
//
//    $type = $company->customer_num_type;
//
//    if ($type === "Format") {
//
//        $Branch_No = (string) ($company->branch ?? 'B00');
//
//        // ---- Customer Registration log (may be null) ----
//        $customer_log = tableWithBranch('customer_log')
//            ->where('customer_id', $cus_id)
//            ->where('type', 'Customer Registration')
//            ->first();
//
//        // Defaults (used if no log / any failure)
//        $Auto_Id       = str_pad((int)($company->customer_num_start_from ?? 0), 3, '0', STR_PAD_LEFT);
//        $Day           = date('d');
//        $Month         = date('m');
//        $Year          = date('Y');
//        $MonthlyCount  = '00';
//        $RootlyCount   = '00';
//
//        if ($customer_log) {
//            try {
//                // Global seq per branch (starting offset from company setting)
//                $last_logs_count = DB::table(DB::raw("
//                    (
//                        SELECT id, branch_id,
//                               ROW_NUMBER() OVER (ORDER BY `date`, `time`, `id`) AS seq
//                        FROM customer_log
//                        WHERE type = 'Customer Registration'
//                          AND branch_id = ".(int)session('branch_id')."
//                    ) AS t
//                "))
//                    ->where('id', $customer_log->id)
//                    ->where('branch_id', session('branch_id'))
//                    ->value('seq');
//
//                $customer_num_start_from = (int)($company->customer_num_start_from ?? 0);
//                $next_customer_id        = $customer_num_start_from + (int)($last_logs_count ?? 0);
//                $Auto_Id                 = str_pad($next_customer_id, 3, '0', STR_PAD_LEFT);
//
//                // Date parts from registration log date
//                $date  = $customer_log->date;
//                $Day   = date('d', strtotime($date));
//                $Month = date('m', strtotime($date));
//                $Year  = date('Y', strtotime($date));
//
//                // Monthly sequence (per branch)
//                $logDateYear  = (int)date('Y', strtotime($customer_log->date));
//                $logDateMonth = (int)date('m', strtotime($customer_log->date));
//
//                $MonthlyCount = DB::table(DB::raw("
//                    (
//                        SELECT id, branch_id,
//                               ROW_NUMBER() OVER (ORDER BY `date`, `time`, `id`) AS seq
//                        FROM customer_log
//                        WHERE type = 'Customer Registration'
//                          AND YEAR(`date`) = {$logDateYear}
//                          AND MONTH(`date`) = {$logDateMonth}
//                          AND branch_id = ".(int)session('branch_id')."
//                    ) AS t
//                "))
//                    ->where('id', $customer_log->id)
//                    ->value('seq');
//
//                $MonthlyCount = str_pad((string)($MonthlyCount ?? 0), 2, '0', STR_PAD_LEFT);
//
//                // Route-wise sequence (per branch)
//                $RootlyCount = DB::table(DB::raw("
//                    (
//                        SELECT cl.id, cl.branch_id, r.id_route, c.idCustomer,
//                               ROW_NUMBER() OVER (ORDER BY cl.date, cl.time, cl.id) AS seq
//                        FROM customer_log cl
//                        INNER JOIN customer c ON c.idCustomer = cl.customer_id
//                        INNER JOIN route r    ON r.id_route   = c.route_id
//                        WHERE cl.type = 'Customer Registration'
//                          AND r.id_route = ".(int)$customer->route_id."
//                          AND cl.branch_id = ".(int)session('branch_id')."
//                    ) AS t
//                "))
//                    ->where('id', $customer_log->id)
//                    ->value('seq');
//
//                $RootlyCount = str_pad((string)($RootlyCount ?? 0), 2, '0', STR_PAD_LEFT);
//
//            } catch (\Throwable $e) {
//                \Log::warning('customer_number: seq calculations failed', [
//                    'cus_id' => $cus_id,
//                    'error'  => $e->getMessage(),
//                ]);
//                // keep defaults
//            }
//        }
//
//        // Route code
//        $root_code = tableWithBranch('route')
//            ->where('id_route', $customer->route_id)
//            ->value('root_code') ?? 'R00';
//
//        // ---- Group/Center (guard nulls) ----
//        $Group_No               = 'G00';
//        $Center_No              = 'C00';
//        $center_customer_count  = '00';
//
//        $customer_group = tableWithBranch('group_has_customer')
//            ->where('cus_id', $customer->idCustomer)
//            ->first();
//
//        if ($customer_group) {
//            $Group = tableWithBranch('customer_group')
//                ->where('idCustomer_Group', $customer_group->group_id)
//                ->first();
//
//            if ($Group) {
//                $Group_No = $Group->Group_No ?? 'G00';
//
//                $center = tableWithBranch('center')
//                    ->where('idCenter', $Group->center_id)
//                    ->first();
//
//                $Center_No = $center->No ?? 'C00';
//
//                $centerId = $Group->center_id;
//
//                try {
//                    $branch_id=session('branch_id');
//                    $center_customer_count = DB::table(DB::raw(" ( SELECT cl.id, cl.branch_id, c.idCustomer, ROW_NUMBER() OVER ( PARTITION BY cg.center_id ORDER BY cl.date, cl.time, cl.id ) AS seq FROM customer_log cl INNER JOIN customer c ON c.idCustomer = cl.customer_id INNER JOIN group_has_customer ghc ON ghc.cus_id = c.idCustomer INNER JOIN customer_group cg ON cg.idCustomer_Group = ghc.group_id WHERE cl.type = 'Assign To A Group' AND cg.center_id = {$centerId} AND cl.branch_id = {$branch_id} ) AS t ")) ->where('idCustomer', $cus_id) ->value('seq') ?? '0'; $center_customer_count = str_pad($center_customer_count, 2, '0', STR_PAD_LEFT);
//                } catch (\Throwable $e) {
//                    \Log::warning('center_customer_count failed', [
//                        'center_id' => $centerId,
//                        'branch_id' => session('branch_id'),
//                        'cus_id'    => $cus_id,
//                        'error'     => $e->getMessage(),
//                    ]);
//                    // default already set
//                }
//            }
//        }
//
//        // ---- Build number from placeholders ----
//        $placeholders = [
//            '@Branch_No@'        => $Branch_No,
//            '@Root@'             => $root_code,
//            '@Center_No@'        => $Center_No,
//            '@Group_No@'         => $Group_No,
//            '@Auto_Id@'          => $Auto_Id,
//            '@Day@'              => $Day,
//            '@Month@'            => $Month,
//            '@Year@'             => $Year,
//            '@CountMonthly@'     => $MonthlyCount,
//            '@RootlyCount@'      => $RootlyCount,
//            '@Center_Cus_Count@' => $center_customer_count,
//        ];
//
//        $customer_number_txt = $company->customer_format;
//        foreach ($placeholders as $placeholder => $value) {
//            $customer_number_txt = str_replace($placeholder, (string)$value, $customer_number_txt);
//        }
//
//        // Update customer
//        updateWithBranch('customer', 'idCustomer', $cus_id, ['cus_number' => $customer_number_txt]);
//        $new_cus_number = $customer_number_txt;
//
//        // Log the change
//        try {
//            $CustomerLogController = new CustomerLogController();
//            $request = new Request([
//                'customer_id'    => $cus_id,
//                'description'    => "Customer Number Changed From ".$old_cus_number." To ".$new_cus_number,
//                'description_id' => $cus_id,
//                'comment'        => 'Change Customer Number',
//                'type'           => 'Customer Update',
//            ]);
//            $CustomerLogController->store($request);
//        } catch (\Throwable $e) {
//            \Log::warning('customer_number: log store failed', [
//                'cus_id' => $cus_id,
//                'error'  => $e->getMessage(),
//            ]);
//        }
//    }
//}

function customer_number($cus_id)
{
    // Load the customer (unscoped or explicitly scoped)—must include branch_id
    $customer = tableWithBranch('customer')->where('idCustomer', $cus_id)->first();
    if (!$customer) { return; }

    $old_cus_number = $customer->cus_number ?? null;

    // Always derive the target branch from the CUSTOMER, not the session
    $branchId = (int)($customer->branch_id ?? session('branch_id'));

    // Fetch the company row for the customer's branch
    // (Avoid tableWithBranch() here if it forces the session branch)
    $company = DB::table('company')->where('branch_id', $branchId)->first();
    if (!$company) { return; }

    $type = $company->customer_num_type;

    if ($type === "Format") {

        $Branch_No = (string)($company->branch ?? 'B00');

        // Get the specific "Customer Registration" log for this customer in its own branch
        $customer_log = DB::table('customer_log')
            ->where('branch_id', $branchId)
            ->where('customer_id', $cus_id)
            ->where('type', 'Customer Registration')
            ->first();

        // Defaults
        $Auto_Id       = str_pad((int)($company->customer_num_start_from ?? 0), 3, '0', STR_PAD_LEFT);
        $Day           = date('d');
        $Month         = date('m');
        $Year          = date('Y');
        $MonthlyCount  = '00';
        $RootlyCount   = '00';

        if ($customer_log) {
            try {
                // Global sequential index per BRANCH
                $last_logs_count = DB::table(DB::raw("
                    (
                      SELECT id, branch_id,
                             ROW_NUMBER() OVER (ORDER BY `date`, `time`, `id`) AS seq
                      FROM customer_log
                      WHERE type = 'Customer Registration'
                        AND branch_id = {$branchId}
                    ) AS t
                "))
                    ->where('id', $customer_log->id)
                    ->where('branch_id', $branchId)
                    ->value('seq');

                $startFrom           = (int)($company->customer_num_start_from ?? 0);
                $next_customer_id    = $startFrom + (int)($last_logs_count ?? 0);
                $Auto_Id             = str_pad($next_customer_id, 3, '0', STR_PAD_LEFT);

                // Dates from the registration log
                $date  = $customer_log->date;
                $Day   = date('d', strtotime($date));
                $Month = date('m', strtotime($date));
                $Year  = date('Y', strtotime($date));

                // Monthly sequence (per BRANCH)
                $logDateYear  = (int)date('Y', strtotime($customer_log->date));
                $logDateMonth = (int)date('m', strtotime($customer_log->date));

                $MonthlyCount = DB::table(DB::raw("
                    (
                      SELECT id, branch_id,
                             ROW_NUMBER() OVER (ORDER BY `date`, `time`, `id`) AS seq
                      FROM customer_log
                      WHERE type = 'Customer Registration'
                        AND YEAR(`date`) = {$logDateYear}
                        AND MONTH(`date`) = {$logDateMonth}
                        AND branch_id = {$branchId}
                    ) AS t
                "))
                    ->where('id', $customer_log->id)
                    ->value('seq');

                $MonthlyCount = str_pad((string)($MonthlyCount ?? 0), 2, '0', STR_PAD_LEFT);

                // Route-wise sequence (per BRANCH + this route)
                $routeId = (int)($customer->route_id ?? 0);

                $RootlyCount = DB::table(DB::raw("
                    (
                      SELECT cl.id, cl.branch_id, r.id_route, c.idCustomer,
                             ROW_NUMBER() OVER (ORDER BY cl.date, cl.time, cl.id) AS seq
                      FROM customer_log cl
                      INNER JOIN customer c ON c.idCustomer = cl.customer_id
                      INNER JOIN route r    ON r.id_route   = c.route_id
                      WHERE cl.type = 'Customer Registration'
                        AND r.id_route = {$routeId}
                        AND cl.branch_id = {$branchId}
                    ) AS t
                "))
                    ->where('id', $customer_log->id)
                    ->value('seq');

                $RootlyCount = str_pad((string)($RootlyCount ?? 0), 2, '0', STR_PAD_LEFT);

            } catch (\Throwable $e) {
                \Log::warning('customer_number: seq calculations failed', [
                    'cus_id'   => $cus_id,
                    'branchId' => $branchId,
                    'error'    => $e->getMessage(),
                ]);
                // keep defaults
            }
        }

        // Route code (from the customer's route, within the customer's branch)
        $root_code = DB::table('route')
            ->where('branch_id', $branchId)
            ->where('id_route', $customer->route_id)
            ->value('root_code') ?? 'R00';

        // ---- Group/Center (branch-safe) ----
        $Group_No               = 'G00';
        $Center_No              = 'C00';
        $center_customer_count  = '00';

        $customer_group = tableWithBranch('group_has_customer')
            ->where('cus_id', $customer->idCustomer)
            ->first();

        if ($customer_group) {
            $Group = DB::table('customer_group')
                ->where('branch_id', $branchId)
                ->where('idCustomer_Group', $customer_group->group_id)
                ->first();

            if ($Group) {
                $Group_No = $Group->Group_No ?? 'G00';

                $center = DB::table('center')
                    ->where('branch_id', $branchId)
                    ->where('idCenter', $Group->center_id)
                    ->first();

                $Center_No = $center->No ?? 'C00';
                $centerId = (int)($Group->center_id ?? 0);

                try {
                    // ---------- BASE: all members of this center + earliest assign log timestamp (if any) ----------
                    $base = DB::table('group_has_customer as ghc')
                        ->join('customer as c', 'c.idCustomer', '=', 'ghc.cus_id')
                        ->join('customer_group as cg', 'cg.idCustomer_Group', '=', 'ghc.group_id')
                        ->leftJoin('customer_log as cl', function ($j) use ($branchId) {
                            $j->on('cl.customer_id', '=', 'c.idCustomer')
                                ->where('cl.type', 'Assign To A Group')
                                ->where('cl.branch_id', $branchId); // keep LEFT-join semantics
                        })
                        ->where('cg.center_id', $centerId)
                        ->where('c.branch_id', $branchId) // safe if customers are branch-scoped
                        ->groupBy('c.idCustomer', 'cg.center_id')
                        ->selectRaw("
            c.idCustomer,
            cg.center_id,
            MIN(TIMESTAMP(cl.date, cl.time)) AS first_assign_at
        ");

                    // ---------- RANK: compute row_number over the FULL center set ----------
                    $ranked = DB::query()
                        ->fromSub($base, 'base')
                        ->selectRaw("
            base.idCustomer,
            base.center_id,
            ROW_NUMBER() OVER (
                PARTITION BY base.center_id
                ORDER BY COALESCE(base.first_assign_at, FROM_UNIXTIME(0)), base.idCustomer
            ) AS seq
        ");

                    // ---------- PICK: now filter to this customer ----------
                    $centerSeq = DB::query()
                        ->fromSub($ranked, 'r')
                        ->where('r.idCustomer', $cus_id)
                        ->value('seq'); // null if the customer isn't in that center

                    $center_customer_count = str_pad((string)($centerSeq ?? 0), 2, '0', STR_PAD_LEFT);

                } catch (\Throwable $e) {
                    \Log::warning('center_customer_count failed', [
                        'center_id' => $centerId,
                        'branch_id' => $branchId,
                        'cus_id'    => $cus_id,
                        'error'     => $e->getMessage(),
                    ]);
                    // keep "00"
                }


            }
        }

        // ---- Build the final number ----
        $placeholders = [
            '@Branch_No@'        => $Branch_No,
            '@Root@'             => $root_code,
            '@Center_No@'        => $Center_No,
            '@Group_No@'         => $Group_No,
            '@Auto_Id@'          => $Auto_Id,
            '@Day@'              => $Day,
            '@Month@'            => $Month,
            '@Year@'             => $Year,
            '@CountMonthly@'     => $MonthlyCount,
            '@RootlyCount@'      => $RootlyCount,
            '@Center_Cus_Count@' => $center_customer_count,
        ];

        $customer_number_txt = $company->customer_format;
        foreach ($placeholders as $placeholder => $value) {
            $customer_number_txt = str_replace($placeholder, (string)$value, $customer_number_txt);
        }

        // Update using the customer's branch (not the session)
        updateWithBranch('customer', 'idCustomer', $cus_id, ['cus_number' => $customer_number_txt]);
        $new_cus_number = $customer_number_txt;

        // Log the change (safe to keep as-is)
        try {
            $CustomerLogController = new CustomerLogController();
            $request = new Request([
                'customer_id'    => $cus_id,
                'description'    => "Customer Number Changed From ".$old_cus_number." To ".$new_cus_number,
                'description_id' => $cus_id,
                'comment'        => 'Change Customer Number',
                'type'           => 'Customer Update',
            ]);
            $CustomerLogController->store($request);
        } catch (\Throwable $e) {
            \Log::warning('customer_number: log store failed', [
                'cus_id' => $cus_id,
                'error'  => $e->getMessage(),
            ]);
        }
    }
}










