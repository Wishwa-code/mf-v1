<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Expenses;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LoanImportController extends Controller
{

    protected $customerLogController;
    protected $LoanLogController;

    protected $bankLogController;
    protected $loanLogController;
    protected $capitalBalanceController;
    public function __construct(CustomerLogController $customerLogController,LoanLogController $LoanLogController,BankLogController $bankLogController,LoanLogController $loanLogController,CapitalBalanceController $capitalBalanceController)
    {
        $this->customerLogController = $customerLogController;
        $this->LoanLogController = $LoanLogController;
        $this->bankLogController = $bankLogController;
        $this->loanLogController = $loanLogController;
        $this->capitalBalanceController = $capitalBalanceController;
    }



    /* =========================
     * Backend helpers (PHP)
     * ========================= */

    /** Excel serial (e.g. 45124) -> Y-m-d; else try flexible parsing. */
    private function toYmdFromExcelOrString($value): string
    {
        if ($value === null || $value === '') return '';
        if (is_numeric($value)) {
            // Excel epoch (1899-12-30)
            $unix = ((float)$value - 25569) * 86400;
            return gmdate('Y-m-d', (int)$unix);
        }
        // already Y-m-d?
        $d = DateTime::createFromFormat('Y-m-d', (string)$value);
        if ($d && $d->format('Y-m-d') === (string)$value) return $d->format('Y-m-d');

        // try m/d/Y, d/m/Y, fallback
        foreach (['n/j/Y', 'd/m/Y', 'm/d/Y', 'j/n/Y'] as $fmt) {
            $d = DateTime::createFromFormat($fmt, (string)$value);
            if ($d) return $d->format('Y-m-d');
        }
        try {
            $d = new DateTime((string)$value);
            return $d->format('Y-m-d');
        } catch (Exception $e) {
            return '';
        }
    }

    private function addMonthsKeepDom(DateTime $d, int $i): DateTime
    {
        $copy = clone $d;
        $copy->modify("+{$i} month");
        return $copy;
    }

    private function weekdayIndex(string $name): ?int
    {
        static $map = ['sunday'=>0,'monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6];
        $k = strtolower(trim($name));
        return array_key_exists($k, $map) ? $map[$k] : null;
    }

    private function nthWordToNum(string $word): int
    {
        static $m = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
        $k = strtolower(trim($word));
        return $m[$k] ?? 1;
    }

    private function getNthWeekday(int $y, int $m0, int $wd, int $nth): DateTime
    {
        // $m0 is 0-based
        $first = new DateTime(sprintf('%04d-%02d-01', $y, $m0 + 1));
        $shift = ($wd - (int)$first->format('w') + 7) % 7;
        $day   = 1 + $shift + ($nth - 1) * 7;
        return new DateTime(sprintf('%04d-%02d-%02d', $y, $m0 + 1, $day));
    }

    private function nthWeekOnOrBefore(DateTime $due, string $nthWord, string $wdName): DateTime
    {
        $nth = $this->nthWordToNum($nthWord);
        $wd  = $this->weekdayIndex($wdName);
        $y   = (int)$due->format('Y');
        $m0  = (int)$due->format('n') - 1;

        $cand = $this->getNthWeekday($y, $m0, $wd, $nth);
        if ($cand > $due) {
            $pm0 = ($m0 + 11) % 12;
            $py  = $m0 === 0 ? $y - 1 : $y;
            $cand = $this->getNthWeekday($py, $pm0, $wd, $nth);
        }
        return $cand;
    }

    private function weekdayOnOrBefore(DateTime $due, string $wdName): DateTime
    {
        $wd = $this->weekdayIndex($wdName);
        $cur = (int)$due->format('w');
        $diff = ($cur - $wd + 7) % 7;
        $d = clone $due;
        $d->modify("-{$diff} day");
        return $d;
    }

    /**
     * Compute "Collection Date" from a route rule text.
     * Examples:
     *  - "Monday" (simple weekday)
     *  - "Second Week Tuesday" (nth-week weekday)
     * Behavior:
     *  1) Start from (Due + $offsetDays)
     *  2) If simple weekday => the weekday on/before that date
     *  3) If "Nth Week X"   => that Nth weekday on/before that date
     *  4) Fallback: return shifted date
     */
    private function collectionDateForRoute(DateTime $due, ?string $ruleText, int $offsetDays = 5): DateTime
    {
        $shifted = clone $due;
        if ($offsetDays !== 0) $shifted->modify("+{$offsetDays} day");

        $txt = trim((string)$ruleText);
        if ($txt === '') return $shifted;

        // Plain weekday?
        if (($wd = $this->weekdayIndex($txt)) !== null) {
            return $this->weekdayOnOrBefore($shifted, $txt);
        }

        // "First Week Monday" / "Second Week Tuesday" / etc.
        $parts = preg_split('/\s+/', $txt);
        if (count($parts) >= 3 && $this->weekdayIndex($parts[2]) !== null) {
            return $this->nthWeekOnOrBefore($shifted, $parts[0], $parts[2]);
        }

        // Fallback
        return $shifted;
    }

    /** Signed difference in days: (collection - due) */
    private function diffDaysSigned(DateTime $due, DateTime $collection): int
    {
        return (int)$due->diff($collection)->format('%r%a');
    }



    public function uploadExcelLoan(\Illuminate\Http\Request $request)
    {
        $row      = $request->row;
        $user_id  = (int) session('userid');
        $branchId = (int) session('branch_id');

        // ---------- BASIC CHECKS ----------
        if (!$row || count($row) < 17) {
            return response()->json(['error' => 'Invalid row data (need 17+ columns).'], 400);
        }
        if (trim((string)$row[1]) === '') {
            return response()->json(['message' => 'Skipped empty row.']);
        }

        // ---------- HELPERS ----------
        $ymd = fn(\DateTime $d) => $d->setTime(0,0,0)->format('Y-m-d');

        $toYmd = function ($value): string {
            if ($value === null || $value === '') return '';
            if (is_string($value)) {
                $s = trim($value);
                $d = \DateTime::createFromFormat('Y-m-d', $s);
                if ($d && $d->format('Y-m-d') === $s) return $s;
                foreach (['d/m/Y','m/d/Y','n/j/Y','j/n/Y','d-m-Y','m-d-Y'] as $fmt) {
                    $d = \DateTime::createFromFormat($fmt, $s);
                    if ($d) return $d->format('Y-m-d');
                }
                try { $d = new \DateTime($s); return $d->format('Y-m-d'); } catch (\Throwable $e) {}
            }
            if (is_numeric($value)) {
                $num = (float)$value;
                if ($num > 1e12 && $num < 1e13) { // unix ms
                    $d = (new \DateTime('@' . (int)round($num/1000)))->setTimezone(new \DateTimeZone('UTC'));
                    return $d->format('Y-m-d');
                }
                if ($num > 1e9 && $num < 2e10) { // unix s
                    $d = (new \DateTime('@' . (int)$num))->setTimezone(new \DateTimeZone('UTC'));
                    return $d->format('Y-m-d');
                }
                // Excel 1900
                $epoch = new \DateTime('1899-12-30 00:00:00', new \DateTimeZone('UTC'));
                $epoch->modify(((int)round($num*86400)) . ' seconds');
                return $epoch->format('Y-m-d');
            }
            return '';
        };

        $addMonthsKeepDOM = function(\DateTime $base, int $months): \DateTime {
            $dom = (int)$base->format('j');
            $y   = (int)$base->format('Y');
            $m   = (int)$base->format('n') + $months;
            $y  += intdiv($m-1, 12);
            $m   = (($m-1)%12)+1;
            $last = cal_days_in_month(CAL_GREGORIAN, $m, $y);
            return (new \DateTime(sprintf('%04d-%02d-%02d', $y, $m, min($dom,$last))))->setTime(0,0,0);
        };

        // ====== EXACT BACKEND PORT OF YOUR JS ======
        // Anchor = due + 5; "Monday" => weekday on/before; "First Week Monday" => nth weekday on/before.
        $collectionDateForRoute = function (\DateTime $due, string $ruleText, int $offsetDays = 5): \DateTime {
            $txt = trim($ruleText);

            // Only the full weekday names per your JS (no abbreviations)
            $weekdayIndex = function (string $name): ?int {
                static $map = ['sunday'=>0,'monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6];
                $k = strtolower(trim($name));
                return $map[$k] ?? null;
            };
            $nthWordToNum = function (string $word): int {
                static $map = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
                $k = strtolower(trim($word));
                return $map[$k] ?? 1;
            };
            $getNthWeekday = function (int $y, int $m0, int $wd, int $nth): \DateTime {
                $first = new \DateTime(sprintf('%04d-%02d-01', $y, $m0 + 1));
                $shift = ($wd - (int)$first->format('w') + 7) % 7;
                $day   = 1 + $shift + ($nth - 1) * 7;
                return new \DateTime(sprintf('%04d-%02d-%02d', $y, $m0 + 1, $day));
            };
            $weekdayOnOrBefore = function (\DateTime $anchor, string $wdName) use ($weekdayIndex): \DateTime {
                $wd = $weekdayIndex($wdName);
                $cur  = (int)$anchor->format('w');
                $diff = ($cur - $wd + 7) % 7;
                $d = clone $anchor; $d->modify("-{$diff} days")->setTime(0,0,0);
                return $d;
            };
            $nthWeekOnOrBefore = function (\DateTime $anchor, string $nthWord, string $wdName)
            use ($nthWordToNum, $weekdayIndex, $getNthWeekday): \DateTime {
                $nth = $nthWordToNum($nthWord);
                $wd  = $weekdayIndex($wdName);
                $y = (int)$anchor->format('Y'); $m0 = (int)$anchor->format('n')-1;
                $cand = $getNthWeekday($y,$m0,$wd,$nth);
                if ($cand > $anchor) {
                    $pm0 = ($m0 + 11) % 12; $py = $m0===0 ? $y-1 : $y;
                    $cand = $getNthWeekday($py,$pm0,$wd,$nth);
                }
                return $cand->setTime(0,0,0);
            };

            // base = due + offset (e.g., +5)
            $anchor = clone $due; $anchor->modify("+{$offsetDays} days")->setTime(0,0,0);

            if ($txt === '') return $anchor; // (you don't do this on FE; but we'll never call with '')

            // Case 1: simple weekday, exactly as your JS object keys
            if (($weekdayIndex($txt) ?? null) !== null) {
                return $weekdayOnOrBefore($anchor, $txt);
            }

            // Case 2: "First Week Monday" (we allow extra spaces / any case)
            $clean = preg_replace('/\s+/', ' ', trim($txt));
            $parts = explode(' ', $clean);
            if (count($parts) >= 3 && strtolower($parts[1]) === 'week' && ($weekdayIndex($parts[2]) ?? null) !== null) {
                return $nthWeekOnOrBefore($anchor, $parts[0], $parts[2]);
            }

            // Fallback (should not happen if rule provided correctly): just anchor
            return $anchor;
        };

        $diffDaysUI = function (\DateTime $due, \DateTime $collection): int {
            // JS: diffDays(due, coll) = (due - coll) in whole days
            $dueMid = (clone $due)->setTime(0,0,0);
            $colMid = (clone $collection)->setTime(0,0,0);
            $seconds = $dueMid->getTimestamp() - $colMid->getTimestamp();
            return (int) round($seconds / 86400);
        };

        // ---------- MAP FIELDS ----------
        $loan_no         = (string)$row[1];
        $product_name    = (string)$row[2];
        $member_no       = (string)$row[3];

        $issue_date_str       = $toYmd($row[4]);
        $loan_amount          = (float)$row[5];
        $interest_rate_str    = (string)$row[6]; // "54%" or "54"
        $installment_cnt      = (int)$row[8];
        $interest_amount      = (float)$row[10];
        $Total_Loan_Amount      = (float)$row[12];
        $Installment_Amount      = (float)$row[13];

        $other_charge         = (float)$row[11];
        $installment_amt_xls  = (float)$row[13]; // optional fixed EMI input
        $collection_type_raw  = (string)$row[14];
        $first_ins_date_str   = $toYmd($row[15]); // not used for monthly; we use issue+1mo
        $route_rule_excel_raw = isset($row[16]) ? (string)$row[16] : '';

        if ($issue_date_str === '') {
            return response()->json(['error' => 'Invalid Issue Date.'], 400);
        }
        $issueDT = (new \DateTime($issue_date_str))->setTime(0,0,0);

        $annual_rate = (float)str_replace('%','', trim($interest_rate_str)); // e.g., 54

        // Normalize collection type (default monthly)
        $collection_type = match (strtoupper(trim($collection_type_raw))) {
            'WEEKLY'   => 'Weekly',
            'MONTHLY'  => 'Per Month',
            'B/WEEKLY', 'BI-WEEKLY', 'BIWEEKLY', 'TWICE A MONTH' => 'Twice A Month',
            'DAILY'    => 'Daily',
            default    => 'Per Month',
        };

        // First due (like flat): issue + 1 month (monthly)
        if ($collection_type === 'Per Month') {
            $firstDT = $addMonthsKeepDOM($issueDT, 1);
        } elseif ($collection_type === 'Weekly') {
            $firstDT = (clone $issueDT)->modify('+1 week')->setTime(0,0,0);
        } elseif ($collection_type === 'Twice A Month') {
            $firstDT = (clone $issueDT)->modify('+14 days')->setTime(0,0,0);
        } else {
            $firstDT = (clone $issueDT)->modify('+1 day')->setTime(0,0,0);
        }

        // ---------- LOOKUPS ----------
        $product = tableWithBranch('loan_category')->where('Name','=',$product_name)->first();
        if (!$product) return response()->json(['error'=>"Product not found: {$product_name}"], 400);

        $customer = tableWithBranch('customer')->where('cus_number','=',$member_no)->first();
        if (!$customer) return response()->json(['error'=>"Customer not found: {$member_no}"], 400);

        $panelty_rate      = (float)($product->Panelty_pecentage ?? 0);
        $panelty_start_day = (int)($product->Panelty_date ?? 0);

        // ----- ROUTE COLLECTION DATE (auto via customer.route_id) -----
        $routeRule = '';
        if (!empty($customer->route_id)) {
            $routeRow = \DB::table('route')->where('id_route', $customer->route_id)->first();
            if ($routeRow && !empty($routeRow->collection_date)) {
                $routeRule = trim(preg_replace('/\s+/', ' ', $routeRow->collection_date));
            }
        }

        if ($routeRule === '') {
            // fallback if route missing or empty
            if (trim($route_rule_excel_raw) !== '') {
                $routeRule = trim(preg_replace('/\s+/', ' ', $route_rule_excel_raw));
            } elseif ($request->has('route_collection_date')) {
                $routeRule = trim(preg_replace('/\s+/', ' ', (string)$request->input('route_collection_date')));
            } else {
                return response()->json(['error' => 'No valid collection_date found in route table for this customer.'], 400);
            }
        }


        $offsetDays = 5;

        \DB::beginTransaction();
        try {
            // ---------- LOAN HEADER ----------
            $loan = new \App\Models\Loan();
            $loan->Loan_No                       = $loan_no;
            $loan->Loan_Category_idLoan_Category = $product->idLoan_Category;
            $loan->Customer_idCustomer           = $customer->idCustomer;
            $loan->Leasing_type                  = "Cash";
            $loan->Vehicle_No                    = null;
            $loan->Date_Time                     = $ymd($issueDT);
            $loan->Amount                        = $loan_amount;
            $loan->Interest_Rate                 = $annual_rate;
            $loan->Panalty_Rate                  = $panelty_rate;
            $loan->Installment_Count             = $installment_cnt;
            $loan->Interest_Amount               = $interest_amount;
            $loan->Total_Other_Amount            = $other_charge;
            $loan->Other_Amount_Balance          = '0';
            $loan->Total_Loan_Amount             = $Total_Loan_Amount;
            $loan->Installment_Amount            = $Installment_Amount;
            $loan->Collection_Type               = $collection_type;
            $loan->Collection_Date               = $ymd($firstDT);
            $loan->Panalty_Date                  = $panelty_start_day;
            $loan->Status                        = "0";
            $loan->User_idUser                   = $user_id;
            $loan->capital_balance               = $loan_amount;
            $loan->installment_balance           = $interest_amount;
            $loan->type                          = "Reducing Balance";
            $loan->Interest_period               = $collection_type;
            $loan->lending_officer_id            = $user_id;
            $loan->collector_id                  = $user_id;
            $loan->repayment_duration            = 'Days';
            $loan->cus_bank_account              = null;
            $loan->branch_id                     = $branchId;
            $loan->save();

            $loanId = (int)($loan->idCustomer_Loan ?? 0);
            if ($loanId <= 0) $loanId = (int)($loan->getKey() ?? 0);
            if ($loanId <= 0) $loanId = (int)\DB::getPdo()->lastInsertId();
            if ($loanId <= 0) throw new \RuntimeException('Failed to retrieve new loan PK.');

            // ---------- SAVINGS (optional) ----------
            if (($product->enable_saving_process ?? 'No') === "Yes") {
                $savingId = insertWithBranch('Customer_Saving_Accounts', [
                    'Customer_Id'  => $customer->idCustomer,
                    'Loan_Id'      => $loanId,
                    'Loan_No'      => $loan_no,
                    'Created_Date' => date('Y-m-d H:i:s'),
                    'Account_No'   => $customer->idCustomer,
                    'Account_Type' => "Saving",
                    'Balance'      => "0.00",
                    'Status'       => "1",
                ]);
                insertWithBranch('Savings_Account_Log', [
                    'Saving_Acount_Id' => $savingId,
                    'Date_Time'        => date('Y-m-d H:i:s'),
                    'Type'             => "Saving Account",
                    'Description'      => "Account Creation",
                    'Credit'           => 0.00,
                    'Debit'            => 0.00,
                    'Balance'          => 0.00,
                    'User'             => $user_id,
                ]);
            }
            $savingVal = 0.00;
            if (($product->enable_saving_process ?? "No") === "Yes" && ($product->saving_payment ?? "1") !== "1") {
                $savingVal = (float)($product->saving_amount ?? 0);
            }

            // ---------- REDUCING BALANCE ----------
            $P = (float)$loan_amount;
            $n = max(1,(int)$installment_cnt);

            // Periods per year & due advancer
            if ($collection_type === 'Per Month') {
                $ppy = 12;
                $advanceDueDate = fn(int $i) => $addMonthsKeepDOM($firstDT, $i);
            } elseif ($collection_type === 'Weekly') {
                $ppy = 52;
                $advanceDueDate = function (int $i) use ($firstDT) { $d = clone $firstDT; $d->modify("+{$i} weeks")->setTime(0,0,0); return $d; };
            } elseif ($collection_type === 'Twice A Month') {
                $ppy = 24;
                $advanceDueDate = function (int $i) use ($firstDT) { $d = clone $firstDT; $d->modify('+' . ($i*14) . ' days')->setTime(0,0,0); return $d; };
            } else {
                $ppy = 365;
                $advanceDueDate = function (int $i) use ($firstDT) { $d = clone $firstDT; $d->modify("+{$i} days")->setTime(0,0,0); return $d; };
            }

            // Rate & EMI
            $r = ($annual_rate/100.0) / $ppy;
            $EMI = $r > 0 ? $P * $r * pow(1+$r, $n) / (pow(1+$r, $n) - 1) : $P / $n;
            $EMI = round($EMI, 2);

            // If Excel gave a fixed EMI (e.g., 7700.04), lock to it by solving r
            if ($installment_amt_xls > 0) {
                $target = round($installment_amt_xls, 2);
                $lo=0.0; $hi=2.0; // 0..200% per period
                for ($i=0; $i<70; $i++) {
                    $mid = ($lo+$hi)/2.0;
                    $emiMid = $mid > 0 ? $P * $mid * pow(1+$mid, $n) / (pow(1+$mid, $n)-1) : $P / $n;
                    if ($emiMid > $target) $hi = $mid; else $lo = $mid;
                }
                $r = ($lo+$hi)/2.0;
                $EMI = $target;
            }

            $balance = $P;
            $hasCollectionDate = \Schema::hasColumn('installments','Collection_Date');
            $hasCollectionDiff = \Schema::hasColumn('installments','Collection_Diff');

            $totalInterestAcc = 0.00;

            for ($i = 0; $i < $n; $i++) {
                $dueDT = $advanceDueDate($i);

                $interestThis = round($balance * $r, 2);
                $capitalThis  = round($EMI - $interestThis, 2);

                if ($i === $n - 1) {
                    // settle rounding
                    $capitalThis = round($balance, 2);
                    $EMI         = round($capitalThis + $interestThis, 2);
                }
                $balance = round($balance - $capitalThis, 2);
                $totalInterestAcc += $interestThis;

                // penalty date
                $penaltyDT = clone $dueDT;
                if ($panelty_start_day > 0) $penaltyDT->modify("+{$panelty_start_day} day")->setTime(0,0,0);

                // ==== EXACT JS BEHAVIOR ====
                $collectionDT = $collectionDateForRoute($dueDT, $routeRule, $offsetDays);
                $diff         = $diffDaysUI($dueDT, $collectionDT);   // positive if collection is earlier

                $totalAmt = round($EMI + $savingVal, 2);

                \DB::table('installments')->insert(array_filter([
                    'Customer_Loan_idCustomer_Loan' => $loanId,
                    'No'                 => $i + 1,
                    'Installment_Date'   => $ymd($dueDT),
                    'Installment_Amount' => number_format($EMI, 2, '.', ''),
                    'capital_amount'     => number_format($capitalThis, 2, '.', ''),
                    'interest_amount'    => number_format($interestThis, 2, '.', ''),
                    'Panalty_Amount'     => number_format(0, 2, '.', ''),
                    'Saving_amount'      => number_format($savingVal, 2, '.', ''),
                    'Total_Amount'       => number_format($totalAmt, 2, '.', ''),
                    'Paid_Amount'        => number_format(0, 2, '.', ''),
                    'Panalty_Balance'    => number_format(0, 2, '.', ''),
                    'Interest_Balance'   => number_format($interestThis, 2, '.', ''),
                    'capital_balance'    => number_format($capitalThis, 2, '.', ''),
                    'Total_Balance'      => number_format($totalAmt, 2, '.', ''),
                    'Status'             => '0',
                    'Panelty_date'       => $ymd($penaltyDT),
                    'Panelty_status'     => '0',
                    'Saving_balance'     => number_format($savingVal, 2, '.', ''),
                    'branch_id'          => $branchId,
                    'Collection_Date'    => $hasCollectionDate ? $ymd($collectionDT) : null,
                    'Collection_Diff'    => $hasCollectionDiff ? $diff : null,   // due - collection (days)
                ], fn($v)=>$v!==null));
            }

            // Header totals
            \DB::table('customer_loan')->where('idCustomer_Loan', $loanId)->update([
                'Interest_Amount'     => round($totalInterestAcc, 2),
                'Total_Loan_Amount'   => round($P + $totalInterestAcc, 2),
                'Installment_Amount'  => $EMI,
                'installment_balance' => round($totalInterestAcc, 2),
                'Balance_Amount'      => round($P + $totalInterestAcc, 2),
                'type'                => 'Reducing Balance',
            ]);

            // ---------- (existing bank logs / income etc. unchanged) ----------
            $company_bank = tableWithBranch('company_bank_accounts')->where('Account_No', 'Cash')->value('Idbank');
            $customer_loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loanId)->first();
            $bank = tableWithBranch('company_bank_accounts')->where('Idbank', $company_bank)->first();

            if (!is_null($bank)) {
                \DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loanId)
                    ->where('branch_id', $branchId)
                    ->update([
                        'Status'               => '0',
                        'cus_bank_account'     => $request->bank_acc ?? null,
                        'company_bank_account' => $company_bank
                    ]);

                $bank_log_comment = "Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";

                $bank_id = tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type', 'System_default_1')
                    ->first();

                $this->bankLogController->index(
                    $company_bank, "Issue Loan", $bank_log_comment, "-", "credit",
                    $customer_loan->Amount, $bank_id->Idbank
                );
                $this->bankLogController->index(
                    $bank_id->Idbank, "Issue Loan", $bank_log_comment, "-", "debit",
                    $customer_loan->Amount, $company_bank
                );


//                $Interest_Receivable_Suspense_AC = tableWithBranch('company_bank_accounts')
//                    ->where('Bank_Type', 'System_default_4')
//                    ->first();
//
//                $Deferred_Income_AC = tableWithBranch('company_bank_accounts')
//                    ->where('Bank_Type', 'System_default_14')
//                    ->first();

                $Interest_Receivable_Suspense_AC=9;
                $Deferred_Income_AC=320;

                $this->bankLogController->index(
                    $Interest_Receivable_Suspense_AC, "Issue Loan", $bank_log_comment, "-", "debit",
                    $interest_amount, $Deferred_Income_AC
                );
                $this->bankLogController->index(
                    $Deferred_Income_AC, "Issue Loan", $bank_log_comment, "-", "credit",
                    $interest_amount, $Interest_Receivable_Suspense_AC
                );

                insertWithBranch('loan_other_charges', [
                    'Description' => 'Document / Insuarance Charge',
                    'Amount'        => $other_charge,
                    'Type'             => "Amount",
                    'Customer_Loan_idCustomer_Loan'      => $loanId,
                ]);



                $sumAmount = \DB::table('loan_other_charges')
                    ->where('Customer_Loan_idCustomer_Loan', $loanId)
                    ->where('branch_id', $branchId)
                    ->sum('Amount');

                if ($sumAmount > 0) {
                    $bank_log_doc_comment = "Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";
                    $doc_bank = tableWithBranch('company_bank_accounts')->where('Bank_Type', 'System_default_9')->first();

                    $this->bankLogController->index($company_bank, "Loan Document Chargers", $bank_log_doc_comment, "-", "debit", $sumAmount, $doc_bank->Idbank);
                    $this->bankLogController->index($doc_bank->Idbank, "Loan Document Chargers", $bank_log_doc_comment, "-", "credit", $sumAmount, $company_bank);

                    $cate = tableWithBranch('income_category')->where('description', 'Other')->first();
                    if (!$cate) {
                        $cateId = \DB::table('income_category')->insertGetId([
                            'description' => "Other",
                            'branch_id'   => $branchId
                        ]);
                        $cate = (object) ['id' => $cateId];
                    }

                    $expenses = new \App\Models\Expenses();
                    $customerFull = tableWithBranch('customer')->where('idCustomer', $customer_loan->Customer_idCustomer)->first();
                    $customerName = $customerFull ? ($customerFull->First_Name . ' ' . $customerFull->Last_Name) : '';
                    $expenses->type        = "Income";
                    $expenses->reason      = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customerName})";
                    $expenses->date        = date('Y-m-d');
                    $expenses->amount      = $sumAmount;
                    $expenses->category_id = $cate->id;
                    $expenses->bank_id     = 1;
                    $expenses->user_id     = $user_id;
                    $expenses->branch_id   = $branchId;
                    $expenses->save();
                }
            }

            // Logs
            $requestLog = new \Illuminate\Http\Request([
                'customer_id'    => $customer_loan->Customer_idCustomer,
                'description'    => "Approve Loan ({$customer_loan->Loan_No})\nLoan Amount : ({$customer_loan->Amount})",
                'description_id' => $loanId,
                'comment'        => ' ',
                'type'           => 'Approve Loan',
            ]);
            $this->customerLogController->store($requestLog);

            $panelty_balance = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->sum('Panalty_Balance');

            $this->LoanLogController->index(
                $loanId,
                'Issue Loan',
                $loanId,
                'Loan Issue',
                $customer_loan->Amount,
                '0','0','0','0',
                $panelty_balance,
                round($totalInterestAcc, 2),
                $customer_loan->capital_balance,
                $customer_loan->Balance_Amount + $panelty_balance,
                '0'
            );

            \DB::commit();
            return response()->json([
                'message' => 'Reducing Balance schedule created (route-aligned collection dates & differences match JS).',
                'loan_id' => $loanId,
            ], 200);

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Excel loan upload failed', ['error'=>$e->getMessage(),'trace'=>$e->getTraceAsString()]);
            return response()->json([
                'error'  => 'Create loan failed. Transaction rolled back.',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }









//    public function uploadExcelLoan(\Illuminate\Http\Request $request)
//    {
//        $row      = $request->row;
//        $user_id  = (int) session('userid');
//        $branchId = (int) session('branch_id');
//
//        // ---------- BASIC VALIDATION ----------
//        if (!$row || count($row) < 17) {
//            return response()->json(['error' => 'Invalid row data. Expected at least 17 columns.'], 400);
//        }
//        if (trim((string)$row[1]) === '') {
//            return response()->json(['message' => 'Skipped empty row.']);
//        }
//
//        // ---------- INLINE HELPERS ----------
//        $ymd = fn(\DateTime $d) => $d->setTime(0, 0, 0)->format('Y-m-d');
//
//        $toYmdFromExcelOrString = function ($value): string {
//            if ($value === null || $value === '') return '';
//
//            if (is_string($value)) {
//                $s = trim($value);
//                $d = \DateTime::createFromFormat('Y-m-d', $s);
//                if ($d && $d->format('Y-m-d') === $s) return $s;
//
//                foreach (['d/m/Y','m/d/Y','n/j/Y','j/n/Y','d-m-Y','m-d-Y'] as $fmt) {
//                    $d = \DateTime::createFromFormat($fmt, $s);
//                    if ($d) return $d->format('Y-m-d');
//                }
//                try { $d = new \DateTime($s); return $d->format('Y-m-d'); } catch (\Exception $e) {}
//            }
//
//            if (is_numeric($value)) {
//                $num = (float) $value;
//
//                // Unix ms
//                if ($num > 1e12 && $num < 1e13) {
//                    $d = (new \DateTime('@' . (int) round($num / 1000)))->setTimezone(new \DateTimeZone('UTC'));
//                    return $d->format('Y-m-d');
//                }
//                // Unix s
//                if ($num > 1e9 && $num < 2e10) {
//                    $d = (new \DateTime('@' . (int) $num))->setTimezone(new \DateTimeZone('UTC'));
//                    return $d->format('Y-m-d');
//                }
//
//                // Excel serial (1900 & 1904)
//                $toDate = function (float $daysSinceEpoch, string $epoch): \DateTime {
//                    $epochDT = new \DateTime($epoch, new \DateTimeZone('UTC'));
//                    $secs = (int) round($daysSinceEpoch * 86400);
//                    $epochDT->modify(($secs >= 0 ? '+' : '-') . abs($secs) . ' seconds');
//                    return $epochDT->setTime(0, 0, 0);
//                };
//                $d1900 = $toDate($num, '1899-12-30 00:00:00');
//                $d1904 = $toDate($num, '1904-01-01 00:00:00');
//                $now = new \DateTime('now', new \DateTimeZone('UTC'));
//                $pick = function (array $cands) use ($now) {
//                    $best = null; $bestDiff = PHP_INT_MAX;
//                    foreach ($cands as $dt) {
//                        $y = (int) $dt->format('Y');
//                        if ($y < 1970 || $y > 2100) continue;
//                        $diff = abs($now->getTimestamp() - $dt->getTimestamp());
//                        if ($diff < $bestDiff) { $best = $dt; $bestDiff = $diff; }
//                    }
//                    return $best;
//                };
//                $chosen = $pick([$d1900, $d1904]) ?? $d1900;
//                return $chosen->format('Y-m-d');
//            }
//
//            return '';
//        };
//
//        $addMonthsNoOverflow = function (\DateTime $base, int $months): \DateTime {
//            $y = (int) $base->format('Y');
//            $m = (int) $base->format('n');
//            $d = (int) $base->format('j');
//
//            $m += $months;
//            $y += intdiv($m - 1, 12);
//            $m  = (($m - 1) % 12) + 1;
//
//            $daysInTarget = cal_days_in_month(CAL_GREGORIAN, $m, $y);
//            $day          = min($d, $daysInTarget);
//
//            return (new \DateTime(sprintf('%04d-%02d-%02d', $y, $m, $day)))->setTime(0, 0, 0);
//        };
//
//        $weekdayIndex = function (string $name): ?int {
//            $k = strtolower(trim($name));
//            $map = [
//                'sun'=>0, 'sunday'=>0,
//                'mon'=>1, 'monday'=>1,
//                'tue'=>2, 'tuesday'=>2, 'tues'=>2,
//                'wed'=>3, 'wednesday'=>3,
//                'thu'=>4, 'thur'=>4, 'thurs'=>4, 'thursday'=>4,
//                'fri'=>5, 'friday'=>5,
//                'sat'=>6, 'saturday'=>6,
//            ];
//            return $map[$k] ?? null;
//        };
//        $nthWordToNum = function (string $word): int {
//            $k = strtolower(trim($word));
//            $map = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
//            if (isset($map[$k])) return $map[$k];
//            if (preg_match('/^(\d+)(st|nd|rd|th)$/i', $k, $m)) {
//                $n = (int) $m[1]; return max(1, min(4, $n));
//            }
//            return 1;
//        };
//        $getNthWeekday = function (int $y, int $m0, int $wd, int $nth): \DateTime {
//            $first = new \DateTime(sprintf('%04d-%02d-01', $y, $m0 + 1));
//            $shift = ($wd - (int) $first->format('w') + 7) % 7;
//            $day   = 1 + $shift + ($nth - 1) * 7;
//            return new \DateTime(sprintf('%04d-%02d-%02d', $y, $m0 + 1, $day));
//        };
//        $weekdayOnOrBefore = function (\DateTime $due, string $wdName) use ($weekdayIndex): \DateTime {
//            $wd  = $weekdayIndex($wdName);
//            $cur = (int) $due->format('w');
//            $diff = ($cur - $wd + 7) % 7;
//            $d = clone $due; $d->modify("-{$diff} day")->setTime(0, 0, 0);
//            return $d;
//        };
//        $nthWeekOnOrBefore = function (\DateTime $due, string $nthWord, string $wdName)
//        use ($nthWordToNum, $weekdayIndex, $getNthWeekday): \DateTime {
//            $nth = $nthWordToNum($nthWord);
//            $wd  = $weekdayIndex($wdName);
//            $y   = (int) $due->format('Y'); $m0 = (int) $due->format('n') - 1;
//            $cand = $getNthWeekday($y, $m0, $wd, $nth);
//            if ($cand > $due) {
//                $pm0 = ($m0 + 11) % 12; $py = $m0 === 0 ? $y - 1 : $y;
//                $cand = $getNthWeekday($py, $pm0, $wd, $nth);
//            }
//            return $cand->setTime(0, 0, 0);
//        };
//        $collectionDateForRoute = function (\DateTime $due, ?string $ruleText, int $offsetDays = 5)
//        use ($weekdayIndex, $weekdayOnOrBefore, $nthWeekOnOrBefore): \DateTime {
//            $shifted = (clone $due)->modify("+{$offsetDays} day")->setTime(0, 0, 0);
//            $txt = trim((string) $ruleText);
//            if ($txt === '') return $shifted;
//
//            if (($weekdayIndex($txt) ?? null) !== null) {
//                return $weekdayOnOrBefore($shifted, $txt);
//            }
//            $parts = preg_split('/\s+/', preg_replace('/\s+/', ' ', $txt));
//            if (count($parts) >= 2) {
//                $nthWord = $parts[0];
//                $wdName  = $parts[count($parts) - 1];
//                if (($weekdayIndex($wdName) ?? null) !== null) {
//                    return $nthWeekOnOrBefore($shifted, $nthWord, $wdName);
//                }
//            }
//            return $shifted;
//        };
//        $diffDaysSigned = fn(\DateTime $due, \DateTime $collection)
//        => (int) $due->diff($collection)->format('%r%a');
//
//        // ---------- MAP FIELDS FROM EXCEL ----------
//        $loan_no         = (string) $row[1];
//        $product_name    = (string) $row[2];
//        $member_no       = (string) $row[3];
//        $issue_date_str  = $toYmdFromExcelOrString($row[4]);
//        $loan_amount     = (float) $row[5];
//        $rate_in_raw     = (string) $row[6]; // "54%" or "54"
//        $installment_cnt = (int) $row[8];
//        $interest_amount_xls  = (float) $row[10]; // legacy info (not used for flat)
//        $other_charge    = (float) $row[11];
//        $tot_loan_amount_xls  = (float) $row[12];
//        $installment_amt_xls  = (float) $row[13];
//        $collection_type_raw  = (string) $row[14];
//        $first_ins_date_str   = $toYmdFromExcelOrString($row[15]);
//        $route_rule_excel_raw = isset($row[16]) ? (string) $row[16] : '';
//
//        if ($issue_date_str === '') {
//            return response()->json(['error' => 'Invalid Issue Date.'], 400);
//        }
//        $issueDT = (new \DateTime($issue_date_str))->setTime(0, 0, 0);
//
//        $interest_rate = (float) str_replace('%', '', trim($rate_in_raw)); // 54 -> annual 54%
//
//        $collection_type = match (strtoupper(trim($collection_type_raw))) {
//            'WEEKLY'   => 'Weekly',
//            'MONTHLY'  => 'Per Month',
//            'B/WEEKLY', 'BI-WEEKLY', 'BIWEEKLY', 'TWICE A MONTH' => 'Twice A Month',
//            'DAILY'    => 'Daily',
//            default    => 'Per Month',
//        };
//
//        // Force first due = issue + 1 month for monthly, regardless of Excel value
//        if ($collection_type === 'Per Month') {
//            $firstDT = $addMonthsNoOverflow($issueDT, 1); // e.g. 2025-07-09 -> 2025-08-09
//        } elseif ($collection_type === 'Weekly') {
//            $firstDT = (clone $issueDT)->modify('+1 week')->setTime(0, 0, 0);
//        } elseif ($collection_type === 'Twice A Month') {
//            $firstDT = (clone $issueDT)->modify('+14 days')->setTime(0, 0, 0);
//        } else {
//            $firstDT = (clone $issueDT)->modify('+1 day')->setTime(0, 0, 0);
//        }
//
//        // ---------- LOOKUPS ----------
//        $product = tableWithBranch('loan_category')->where('Name', '=', $product_name)->first();
//        if (!$product) {
//            \Log::info("Product not found: {$product_name} - {$loan_no}");
//            return response()->json(['error' => "Product not found: {$product_name}"], 400);
//        }
//        $customer = tableWithBranch('customer')->where('cus_number', '=', $member_no)->first();
//        if (!$customer) {
//            \Log::info("Customer not found by cus_number: {$member_no}");
//            return response()->json(['error' => "Customer not found: {$member_no}"], 400);
//        }
//
//        $panelty_rate      = (float) ($product->Panelty_pecentage ?? 0);
//        $panelty_start_day = (int)   ($product->Panelty_date ?? 0);
//
//        $routeRule = trim($route_rule_excel_raw) !== ''
//            ? trim($route_rule_excel_raw)
//            : \DB::table('route')->where('id_route', $customer->route_id ?? 0)->value('collection_date');
//        $offsetDays = 5;
//
//        \DB::beginTransaction();
//        try {
//            // ---------- CREATE LOAN (HEADER) ----------
//            $loan = new \App\Models\Loan();
//            $loan->Loan_No                       = $loan_no;
//            $loan->Loan_Category_idLoan_Category = $product->idLoan_Category;
//            $loan->Customer_idCustomer           = $customer->idCustomer;
//            $loan->Leasing_type                  = "Cash";
//            $loan->Vehicle_No                    = null;
//            $loan->Date_Time                     = $ymd($issueDT);
//            $loan->Amount                        = $loan_amount;
//            $loan->Interest_Rate                 = $interest_rate;     // store 54 (annual %)
//            $loan->Panalty_Rate                  = $panelty_rate;
//            $loan->Installment_Count             = $installment_cnt;
//
//            $loan->Interest_Amount               = 0;
//            $loan->Total_Other_Amount            = $other_charge;
//            $loan->Other_Amount_Balance          = '0';
//            $loan->Total_Loan_Amount             = 0;
//            $loan->Installment_Amount            = 0;
//
//            $loan->Collection_Type               = $collection_type;
//            $loan->Collection_Date               = $ymd($firstDT);
//            $loan->Panalty_Date                  = $panelty_start_day;
//            $loan->Status                        = "0";
//            $loan->User_idUser                   = $user_id;
//            $loan->capital_balance               = $loan_amount;
//            $loan->installment_balance           = 0;
//            $loan->type                          = "Flat Rate";
//            $loan->Interest_period               = $collection_type;
//            $loan->lending_officer_id            = $user_id;
//            $loan->collector_id                  = $user_id;
//            $loan->repayment_duration            = 'Days';
//            $loan->cus_bank_account              = null;
//            $loan->branch_id                     = $branchId;
//            $loan->save();
//
//            $loanId = (int) ($loan->idCustomer_Loan ?? 0);
//            if ($loanId <= 0) $loanId = (int) ($loan->getKey() ?? 0);
//            if ($loanId <= 0) $loanId = (int) \DB::getPdo()->lastInsertId();
//            if ($loanId <= 0) {
//                $loanId = (int) \DB::table('customer_loan')
//                    ->where('Loan_No', $loan_no)
//                    ->where('branch_id', $branchId)
//                    ->orderByDesc('idCustomer_Loan')
//                    ->value('idCustomer_Loan');
//            }
//            if ($loanId <= 0) {
//                throw new \RuntimeException('Failed to retrieve new loan primary key.');
//            }
//
//            // ---------- SAVINGS (optional) ----------
//            if (($product->enable_saving_process ?? 'No') === "Yes") {
//                $saving_number_txt = $customer->idCustomer;
//                $savingId = insertWithBranch('Customer_Saving_Accounts', [
//                    'Customer_Id'  => $customer->idCustomer,
//                    'Loan_Id'      => $loanId,
//                    'Loan_No'      => $loan_no,
//                    'Created_Date' => date('Y-m-d H:i:s'),
//                    'Account_No'   => $saving_number_txt,
//                    'Account_Type' => "Saving",
//                    'Balance'      => "0.00",
//                    'Status'       => "1",
//                ]);
//                insertWithBranch('Savings_Account_Log', [
//                    'Saving_Acount_Id' => $savingId,
//                    'Date_Time'        => date('Y-m-d H:i:s'),
//                    'Type'             => "Saving Account",
//                    'Description'      => "Account Creation",
//                    'Credit'           => 0.00,
//                    'Debit'            => 0.00,
//                    'Balance'          => 0.00,
//                    'User'             => $user_id,
//                ]);
//            }
//            $savingVal = 0.00;
//            if (($product->enable_saving_process ?? "No") === "Yes" && ($product->saving_payment ?? "1") !== "1") {
//                $savingVal = (float) ($product->saving_amount ?? 0);
//            }
//
//            // ---------- FLAT RATE SCHEDULE ----------
//            $P = (float) $loan_amount;
//            $n = max(1, (int) $installment_cnt);
//
//            // periods-per-year and due date advancer
//            if ($collection_type === 'Per Month') {
//                $ppy = 12;
//                $advanceDueDate = fn(int $i) => $addMonthsNoOverflow($firstDT, $i);
//            } elseif ($collection_type === 'Weekly') {
//                $ppy = 52;
//                $advanceDueDate = function (int $i) use ($firstDT) {
//                    $d = clone $firstDT; $d->modify("+{$i} weeks")->setTime(0, 0, 0); return $d;
//                };
//            } elseif ($collection_type === 'Twice A Month') {
//                $ppy = 24; // 14-day cadence
//                $advanceDueDate = function (int $i) use ($firstDT) {
//                    $d = clone $firstDT; $d->modify('+' . ($i * 14) . ' days')->setTime(0, 0, 0); return $d;
//                };
//            } else { // Daily
//                $ppy = 365;
//                $advanceDueDate = function (int $i) use ($firstDT) {
//                    $d = clone $firstDT; $d->modify("+{$i} days")->setTime(0, 0, 0); return $d;
//                };
//            }
//
//            // annual flat
//            $annual          = $interest_rate;           // 54% -> 0.54
//            $termYears       = $n / $ppy;                        // 12/12 = 1
//            $totalInterest   = round($P * $annual * $termYears, 2); // 32,400.00
//            $interestPerInst = round($totalInterest / $n, 2);       // 2,700.00
//            $capitalPerInst  = round($P / $n, 2);                   // 5,000.00
//            $EMI_nominal     = round($capitalPerInst + $interestPerInst, 2); // 7,700.00
//            $loanTotal       = round($P + $totalInterest, 2);
//
//            // Update header totals
//            \DB::table('customer_loan')
//                ->where('idCustomer_Loan', $loanId)
//                ->update([
//                    'Interest_Amount'     => $totalInterest,
//                    'Total_Loan_Amount'   => $loanTotal,
//                    'Installment_Amount'  => $EMI_nominal,
//                    'installment_balance' => $totalInterest,
//                    'Balance_Amount'      => $loanTotal,
//                    'type'                => 'Flat Rate',
//                ]);
//
//            $hasCollectionDate = \Schema::hasColumn('installments', 'Collection_Date');
//            $hasCollectionDiff = \Schema::hasColumn('installments', 'Collection_Diff');
//
//            $interestAccum = 0.00;
//            $capitalAccum  = 0.00;
//
//            for ($i = 0; $i < $n; $i++) {
//                $dueDT = $advanceDueDate($i);
//
//                $interestThis = ($i === $n - 1)
//                    ? round($totalInterest - $interestAccum, 2)
//                    : $interestPerInst;
//
//                $capitalThis = ($i === $n - 1)
//                    ? round($P - $capitalAccum, 2)
//                    : $capitalPerInst;
//
//                $interestAccum += $interestThis;
//                $capitalAccum  += $capitalThis;
//
//                $EMI = round($capitalThis + $interestThis, 2);
//
//                $penaltyDT = clone $dueDT;
//                if ($panelty_start_day > 0) $penaltyDT->modify("+{$panelty_start_day} day")->setTime(0, 0, 0);
//
//                $collectionDT = $collectionDateForRoute($dueDT, $routeRule, $offsetDays);
//                $diffSigned   = $diffDaysSigned($dueDT, $collectionDT);
//                $diff         = abs($diffSigned);
//
//                $totalAmt = round($EMI + $savingVal, 2);
//
//                \DB::table('installments')->insert(array_filter([
//                    'Customer_Loan_idCustomer_Loan' => $loanId,
//                    'No'                 => $i + 1,
//                    'Installment_Date'   => $ymd($dueDT),
//                    'Installment_Amount' => number_format($EMI, 2, '.', ''),
//                    'capital_amount'     => number_format($capitalThis, 2, '.', ''),
//                    'interest_amount'    => number_format($interestThis, 2, '.', ''),
//                    'Panalty_Amount'     => number_format(0, 2, '.', ''),
//                    'Saving_amount'      => number_format($savingVal, 2, '.', ''),
//                    'Total_Amount'       => number_format($totalAmt, 2, '.', ''),
//                    'Paid_Amount'        => number_format(0, 2, '.', ''),
//                    'Panalty_Balance'    => number_format(0, 2, '.', ''),
//                    'Interest_Balance'   => number_format($interestThis, 2, '.', ''),
//                    'capital_balance'    => number_format($capitalThis, 2, '.', ''),
//                    'Total_Balance'      => number_format($totalAmt, 2, '.', ''),
//                    'Status'             => '0',
//                    'Panelty_date'       => $ymd($penaltyDT),
//                    'Panelty_status'     => '0',
//                    'Saving_balance'     => number_format($savingVal, 2, '.', ''),
//                    'branch_id'          => $branchId,
//                    'Collection_Date'    => $hasCollectionDate ? $ymd($collectionDT) : null,
//                    'Collection_Diff'    => $hasCollectionDiff ? $diff : null,
//                ], fn($v) => $v !== null));
//            }
//
//            // ---------- BANK LOGS / DOCUMENT CHARGES / INCOME ----------
//            $company_bank = tableWithBranch('company_bank_accounts')->where('Account_No', 'Cash')->value('Idbank');
//            $customer_loan = tableWithBranch('customer_loan')
//                ->where('idCustomer_Loan', $loanId)
//                ->first();
//            $bank = tableWithBranch('company_bank_accounts')->where('Idbank', $company_bank)->first();
//
//            if (!is_null($bank)) {
//                \DB::table('customer_loan')
//                    ->where('idCustomer_Loan', $loanId)
//                    ->where('branch_id', $branchId)
//                    ->update([
//                        'Status'               => '0',
//                        'cus_bank_account'     => $request->bank_acc ?? null,
//                        'company_bank_account' => $company_bank
//                    ]);
//
//                $bank_log_comment = "Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";
//
//                $bank_id = tableWithBranch('company_bank_accounts')
//                    ->where('Bank_Type', 'System_default_1')
//                    ->first();
//
//                // Company bank (selected) CREDIT, System_default_1 DEBIT
//                $this->bankLogController->index(
//                    $company_bank, "Issue Loan", $bank_log_comment, "-", "credit",
//                    $customer_loan->Amount, $bank_id->Idbank
//                );
//                $this->bankLogController->index(
//                    $bank_id->Idbank, "Issue Loan", $bank_log_comment, "-", "debit",
//                    $customer_loan->Amount, $company_bank
//                );
//
//                // Document charges movement (if any)
//                $sumAmount = \DB::table('loan_other_charges')
//                    ->where('Customer_Loan_idCustomer_Loan', $loanId)
//                    ->where('branch_id', $branchId)
//                    ->sum('Amount');
//
//                if ($sumAmount > 0) {
//                    $bank_log_doc_comment = "Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";
//
//                    $doc_bank = tableWithBranch('company_bank_accounts')
//                        ->where('Bank_Type', 'System_default_9')->first();
//
//                    // Company bank DEBIT, Doc bank CREDIT
//                    $this->bankLogController->index(
//                        $company_bank, "Loan Document Chargers", $bank_log_doc_comment, "-",
//                        "debit", $sumAmount, $doc_bank->Idbank
//                    );
//                    $this->bankLogController->index(
//                        $doc_bank->Idbank, "Loan Document Chargers", $bank_log_doc_comment, "-",
//                        "credit", $sumAmount, $company_bank
//                    );
//
//                    // Income: "Other"
//                    $cate = tableWithBranch('income_category')->where('description', 'Other')->first();
//                    if (!$cate) {
//                        $cateId = \DB::table('income_category')->insertGetId([
//                            'description' => "Other",
//                            'branch_id'   => $branchId
//                        ]);
//                        $cate = (object) ['id' => $cateId];
//                    }
//
//                    $expenses = new \App\Models\Expenses();
//                    $customerFull = tableWithBranch('customer')->where('idCustomer', $customer_loan->Customer_idCustomer)->first();
//                    $customerName = $customerFull ? ($customerFull->First_Name . ' ' . $customerFull->Last_Name) : '';
//                    $expenses->type        = "Income";
//                    $expenses->reason      = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customerName})";
//                    $expenses->date        = date('Y-m-d');
//                    $expenses->amount      = $sumAmount;
//                    $expenses->category_id = $cate->id;
//                    $expenses->bank_id     = 1;
//                    $expenses->user_id     = $user_id;
//                    $expenses->branch_id   = $branchId;
//                    $expenses->save();
//                }
//            }
//
//            // ---------- CUSTOMER & LOAN LOGS ----------
//            $requestLog = new \Illuminate\Http\Request([
//                'customer_id'    => $customer_loan->Customer_idCustomer,
//                'description'    => "Approve Loan ({$customer_loan->Loan_No})\nLoan Amount : ({$customer_loan->Amount})",
//                'description_id' => $loanId,
//                'comment'        => ' ',
//                'type'           => 'Approve Loan',
//            ]);
//            $this->customerLogController->store($requestLog);
//
//            $panelty_balance = tableWithBranch('installments')
//                ->where('Customer_Loan_idCustomer_Loan', $loanId)
//                ->sum('Panalty_Balance');
//
//            $this->LoanLogController->index(
//                $loanId,
//                'Issue Loan',
//                $loanId,
//                'Loan Issue',
//                $customer_loan->Amount,
//                '0', '0', '0', '0',
//                $panelty_balance,
//                $totalInterest,                // flat-rate interest total
//                $customer_loan->capital_balance,
//                $customer_loan->Balance_Amount + $panelty_balance,
//                '0'
//            );
//
//            \DB::commit();
//            return response()->json([
//                'message' => 'Row processed with Flat Rate schedule.',
//                'loan_id' => $loanId
//            ], 200);
//
//        } catch (\Throwable $e) {
//            \DB::rollBack();
//            \Log::error('Excel loan upload failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//            return response()->json([
//                'error'  => 'Create loan failed. Transaction rolled back.',
//                'detail' => $e->getMessage(),
//            ], 500);
//        }
//    }




}
