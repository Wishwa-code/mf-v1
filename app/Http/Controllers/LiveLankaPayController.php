<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class LiveLankaPayController extends Controller
{
    private string $xlsxUrl = 'https://lankapay.net/upload/documents/Bank%20Branch%20Directory.xlsx';

    public function banks(Request $req)
    {
        [$banks, $branchesByBank] = $this->getDirectoryOrFail();
        $q = strtolower(trim((string)$req->query('q','')));

        $list = array_map(fn($code, $name) => ['code'=>$code, 'name'=>$name], array_keys($banks), $banks);

        if ($q !== '') {
            $list = array_values(array_filter($list, fn($b) =>
                str_contains(strtolower($b['name']), $q) || str_contains(strtolower($b['code']), $q)
            ));
        }

        $results = array_map(fn($b)=>[
            'id'   => $b['code'],
            'text' => ($b['name'] ? $b['name'].' ' : '').'('.$b['code'].')',
            'name' => $b['name'],
            'code' => $b['code'],
        ], $list);

        return response()->json(['results'=>$results]);
    }

    public function branches(string $bankCode, Request $req)
    {
        [$banks, $branchesByBank] = $this->getDirectoryOrFail();
        $q = strtolower(trim((string)$req->query('q','')));
        $rows = $branchesByBank[$bankCode] ?? [];

        if ($q !== '') {
            $rows = array_values(array_filter($rows, fn($br) =>
                str_contains(strtolower($br['branch_name']), $q) || str_contains(strtolower($br['branch_code']), $q)
            ));
        }

        $results = array_map(fn($br)=>[
            'id'          => $br['branch_code'],
            'text'        => ($br['branch_name'] ? $br['branch_name'].' ' : '').'('.$br['branch_code'].')',
            'branch_name' => $br['branch_name'],
            'branch_code' => $br['branch_code'],
        ], $rows);

        return response()->json(['results'=>$results]);
    }

    public function health()
    {
        [$banks, $branchesByBank] = $this->getDirectoryOrFail();
        $branchCount = array_sum(array_map('count', $branchesByBank));
        return response()->json(['banks'=>count($banks), 'branches'=>$branchCount]);
    }

    // ================= Internals =================

    private function getDirectoryOrFail(): array
    {
        try {
            // bump key version to refresh cache
            return Cache::remember('lk_lankapay_dir_v4', now()->addHours(6), function () {
                $bin = Http::withHeaders([
                    'User-Agent' => 'ASIPBOOK/1.0',
                    'Accept' => '*/*',
                ])
                    ->retry(3, 800)
                    ->timeout(60)
                    ->get($this->xlsxUrl)
                    ->throw()
                    ->body();

                $tmp = tmpfile();
                $meta = stream_get_meta_data($tmp);
                file_put_contents($meta['uri'], $bin);

                $reader = IOFactory::createReaderForFile($meta['uri']);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($meta['uri']);

                $banks = [];            // bank_code => bank_name
                $branchesByBank = [];   // bank_code => [ ['branch_code','branch_name'], ... ]
                $banksFromBankSheet = [];

                foreach ($spreadsheet->getAllSheets() as $sheet) {
                    $title = strtolower(trim($sheet->getTitle()));

                    // 1) Sheets with Bank Code + Branch Code (e.g., "Branch NEW")
                    [$b, $bb] = $this->parseSheetWithBranches($sheet);
                    foreach ($b as $code => $name) { $banks[$code] = $banks[$code] ?? $name; }
                    foreach ($bb as $code => $list) {
                        $branchesByBank[$code] = array_merge($branchesByBank[$code] ?? [], $list);
                    }

                    // 2) Sheets with ONLY Bank Code + Bank Name (e.g., "Bank")
                    $banksOnly = $this->parseBankNameSheet($sheet);
                    if (!empty($banksOnly)) {
                        foreach ($banksOnly as $code => $name) {
                            $banksFromBankSheet[$code] = $name;
                        }
                    }
                }

                // Merge in names from the "Bank" sheet (authoritative for names)
                foreach ($banks as $code => $name) {
                    if (($name ?? '') === '' && isset($banksFromBankSheet[$code])) {
                        $banks[$code] = $banksFromBankSheet[$code];
                    }
                }
                // Also add banks that only appear on the Bank sheet (no branches)
                foreach ($banksFromBankSheet as $code => $name) {
                    if (!isset($banks[$code])) $banks[$code] = $name;
                }

                // Normalize / de-dup branches and sort
                ksort($banks);
                foreach ($branchesByBank as $k => $list) {
                    $seen = [];
                    $clean = [];
                    foreach ($list as $x) {
                        $key = $x['branch_code'].'|'.$x['branch_name'];
                        if (!isset($seen[$key])) { $seen[$key]=1; $clean[]=$x; }
                    }
                    usort($clean, fn($a,$b)=>strcmp($a['branch_name'],$b['branch_name']));
                    $branchesByBank[$k] = $clean;
                }

                if (!count($banks)) {
                    throw new \RuntimeException('Parsed 0 banks from directory');
                }
                return [$banks, $branchesByBank];
            });
        } catch (\Throwable $e) {
            Log::error('[LankaPay] Parse failed: '.$e->getMessage());
            abort(502, 'LankaPay directory parse failed: '.$e->getMessage());
        }
    }

    /**
     * Parse sheets that contain Bank Code + Branch Code (+ optional names).
     */
    private function parseSheetWithBranches(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (!$rows || count($rows) < 2) return [[],[]];

        $hdrIdx = null;
        $colBankCode = $colBranchCode = $colBankName = $colBranchName = null;

        $max = min(30, count($rows));
        for ($i=1; $i<=$max; $i++) {
            $norm = [];
            foreach ($rows[$i] ?? [] as $col => $val) {
                $t = strtolower(trim((string)$val));
                $t = preg_replace('/\s+/', ' ', $t);
                $norm[$col] = $t;
            }
            $bCode = $this->findCol($norm, ['bank code','code of bank','bankcode']);
            $brCode = $this->findCol($norm, ['branch code','code of branch','branchcode']);
            if ($bCode && $brCode) {
                $hdrIdx = $i;
                $colBankCode   = $bCode;
                $colBranchCode = $brCode;
                $colBankName   = $this->findCol($norm, ['bank name','name of bank']);
                $colBranchName = $this->findCol($norm, ['branch name','name of branch']);
                break;
            }
        }
        if (!$hdrIdx) return [[],[]];

        $banks = [];
        $branchesByBank = [];

        for ($r=$hdrIdx+1; $r<=count($rows); $r++) {
            $row = $rows[$r] ?? [];
            $bankCode   = trim((string)($row[$colBankCode]   ?? ''));
            $branchCode = trim((string)($row[$colBranchCode] ?? ''));
            if ($bankCode==='' || $branchCode==='') continue;

            // ignore if header repeats
            if (strtolower($bankCode)==='bank code' || strtolower($branchCode)==='branch code') continue;

            $bankName   = $colBankName   ? trim((string)($row[$colBankName]   ?? '')) : '';
            $branchName = $colBranchName ? trim((string)($row[$colBranchName] ?? '')) : '';

            if (!isset($banks[$bankCode])) $banks[$bankCode] = $bankName;
            $branchesByBank[$bankCode][] = [
                'branch_code' => $branchCode,
                'branch_name' => $branchName ?: $branchCode,
            ];
        }

        return [$banks, $branchesByBank];
    }

    /**
     * Parse sheets that only have Bank Code + Bank Name (e.g., "Bank" sheet).
     * Returns [bank_code => bank_name].
     */
    private function parseBankNameSheet(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (!$rows || count($rows) < 2) return [];

        // find a header row that contains "Bank Code" and "Bank Name" (no need for Branch Code)
        $hdrIdx = null;
        $colBankCode = $colBankName = null;

        $max = min(30, count($rows));
        for ($i=1; $i<=$max; $i++) {
            $norm = [];
            foreach ($rows[$i] ?? [] as $col => $val) {
                $t = strtolower(trim((string)$val));
                $t = preg_replace('/\s+/', ' ', $t);
                $norm[$col] = $t;
            }
            $bCode = $this->findCol($norm, ['bank code','code of bank','bankcode']);
            $bName = $this->findCol($norm, ['bank name','name of bank']);
            if ($bCode && $bName) {
                $hdrIdx = $i;
                $colBankCode = $bCode;
                $colBankName = $bName;
                break;
            }
        }
        if (!$hdrIdx) return [];

        $banks = [];
        for ($r=$hdrIdx+1; $r<=count($rows); $r++) {
            $row = $rows[$r] ?? [];
            $bankCode = trim((string)($row[$colBankCode] ?? ''));
            $bankName = trim((string)($row[$colBankName] ?? ''));
            if ($bankCode === '') continue;
            if (strtolower($bankCode)==='bank code') continue;
            if ($bankName === '') continue; // ignore nameless lines
            $banks[$bankCode] = $bankName;
        }
        return $banks;
    }

    private function findCol(array $normRow, array $needles): ?string
    {
        foreach ($needles as $needle) {
            foreach ($normRow as $col => $text) {
                if (strpos($text, $needle) !== false) return $col;
            }
        }
        return null;
    }


    public function getLoanIds(Request $request)
    {
        try {
            $q = DB::table('installments')
                ->select('Customer_Loan_idCustomer_Loan')
                ->groupBy('Customer_Loan_idCustomer_Loan')
                ->havingRaw('COUNT(*) >= 2');

            // Optional branch scoping (safer on installments.branch_id)
            if (session('branch_id') && session('branch_id') != -1) {
                $q->where('installments.branch_id', session('branch_id'));
            }

            $loanIds = $q->pluck('Customer_Loan_idCustomer_Loan')->toArray();

            return response()->json([
                'ok'       => true,
                'loan_ids' => $loanIds,
                'count'    => count($loanIds),
            ]);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * For a given loan:
     * - Take last 2 installments by idInstallments DESC
     * - Delete them
     */
    public function processLoan($loanId)
    {
        try {
            // Optional branch permission check (based on installments)
            if (session('branch_id') && session('branch_id') != -1) {
                $sameBranch = DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loanId)
                    ->where('branch_id', session('branch_id'))
                    ->exists();

                if (!$sameBranch) {
                    return response()->json([
                        'ok' => false,
                        'loan_id' => (int)$loanId,
                        'skipped' => true,
                        'reason' => 'Forbidden: different branch'
                    ], 403);
                }
            }

            // Must have at least 2 rows to delete
            $count = DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->count();

            if ($count < 2) {
                return response()->json([
                    'ok' => true,
                    'loan_id' => (int)$loanId,
                    'skipped' => true,
                    'reason' => 'Less than 2 installments'
                ]);
            }

            // Get last 2 by idInstallments DESC (exact requirement)
            $lastTwoRows = DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loanId)
                ->orderByDesc('idInstallments')
                ->limit(2)
                ->get(); // <-- Full rows (not just IDs)


            DB::transaction(function () use ($lastTwoRows, $loanId) {

                // 1. Delete the rows
                $ids = $lastTwoRows->pluck('idInstallments')->all();
                DB::table('installments')->whereIn('idInstallments', $ids)->delete();

                // 2. Insert new rows again
                foreach ($lastTwoRows as $row) {

                    // EXAMPLE: set your new amounts here
                    // 🔥 CHANGE THESE VALUES AS YOU NEED
                    $newInstallmentAmount = $row->Installment_Amount; // same
                    $newCapitalAmount     = $row->Capital_Amount;     // same
                    $newInterestAmount    = $row->Interest_Amount;    // same

                    DB::table('installments')->insert([
                        'Customer_Loan_idCustomer_Loan' => $loanId,

                        // keep original No or increment - your choice
                        'No'                 => $row->No,
                        'Installment_Date'   => $row->Installment_Date,

                        // Your updated values:
                        'Installment_Amount' => $newInstallmentAmount,
                        'Capital_Amount'     => $newCapitalAmount,
                        'Interest_Amount'    => $newInterestAmount,

                        // Always reset payment status if needed:
                        'Paid_Amount'        => 0,
                        'Status'             => 0, // unpaid

                        // keep loan branch + metadata
                        'branch_id'          => $row->branch_id,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            });


            return response()->json([
                'ok'            => true,
                'loan_id'       => (int)$loanId,
                'deleted'       => $lastTwo,
                'deleted_count' => count($lastTwo),
                'skipped'       => false
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'loan_id' => (int)$loanId,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
