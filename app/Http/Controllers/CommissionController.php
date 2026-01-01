<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CommissionController extends Controller
{
    /**
     * POST: Create commission person + create bank account + opening log
     */
    public function commission_store_person(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|min:1',
            'full_name' => 'required|string|max:150',
            'contact_number' => 'nullable|string|max:30',
            'nic_number' => 'nullable|string|max:30',
            'brief_description' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:120',
            'bank_branch' => 'nullable|string|max:120',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $branch_access = session('branch_access');
        $sessionBranch = (int) session('branch_id');
        $isHO = ($sessionBranch === -1);

        $selectedBranch = ($branch_access == 1 && $isHO)
            ? (int) $request->branch_id
            : $sessionBranch;

        $uid = session('user_data')["idUser"];

        try {
            DB::beginTransaction();

            // 1) Create commission person
            $personId = DB::table('commission_people')->insertGetId([
                'branch_id' => $selectedBranch,
                'type' => 'commission',
                'user_id' => null,

                'full_name' => $request->full_name,
                'contact_number' => $request->contact_number,
                'nic_number' => $request->nic_number,
                'brief_description' => $request->brief_description,

                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name' => $request->bank_account_name,

                'status' => 1,
                'created_by' => $uid,
                'updated_by' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2) Create bank account
            $accountCode = 'COM-' . $personId;

            $bankAccountId = DB::table('company_bank_accounts')->insertGetId([
                'Bank_Type'        => 'Commission',
                'code'             => 'COM-' . $personId,
                'Bank_Name'        => 'Commission',
                'Account_Name'     => $request->full_name,
                'Account_No'       => $accountCode,
                'Bank_Branch'      => '-',
                'Account_Balance'  => '0.00',
                'type'             => 'Cash and Bank',
                'cashflow'         => 'Non Applicable',
                'acc_type_group'   => 'Assets',
                'User'             => session('user_id') ?? $uid,
                'branch_id'        => $selectedBranch,
            ]);

            // 3) Opening/creation log (only if table exists)
            if (Schema::hasTable('company_bank_has_log')) {
                DB::table('company_bank_has_log')->insert([
                    'Bank_Account_Id' => $bankAccountId, // adjust if your FK is different
                    'Date_Time'       => now(),
                    'Type'            => 'Credit',
                    'Note'            => '-',
                    'Description'     => 'Commission account created (Opening Balance)',
                    'Credit'          => '0.00',
                    'Debit'           => '0.00',
                    'Balance'         => '0.00',
                    'User'            => session('user_id') ?? $uid,
                    'branch_id'       => $selectedBranch,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'person'  => DB::table('commission_people')->where('id', $personId)->first(),
                'bank_account_id' => $bankAccountId,
                'account_no' => $accountCode
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save commission person & bank account.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST: Save rates (bulk)
     */
    public function commission_save_rates(Request $request)
    {
        $branch_id = (int) $request->branch_id;
        $rates     = $request->rates ?? [];

        $uid = session('user_data')["idUser"]
            ?? session('user_id')
            ?? session('id')
            ?? session('idUser')
            ?? 1;

        if ($branch_id <= 0) {
            return response()->json(['message' => 'Branch is required'], 422);
        }

        if (!is_array($rates) || empty($rates)) {
            return response()->json(['message' => 'Rates are required'], 422);
        }

        $invalid = [];
        $saved = 0;

        DB::beginTransaction();
        try {
            foreach ($rates as $r) {
                $incomingPerson = (int)($r['person_id'] ?? 0);
                $product_id     = (int)($r['product_id'] ?? 0);

                // allow 0 too
                $rateValRaw     = $r['rate'] ?? null;
                if (!is_numeric($rateValRaw)) {
                    $invalid[] = ['row' => $r, 'reason' => 'Invalid rate'];
                    continue;
                }
                $rateVal = (float)$rateValRaw;

                if ($incomingPerson <= 0 || $product_id <= 0) {
                    $invalid[] = ['row' => $r, 'reason' => 'Missing person_id/product_id'];
                    continue;
                }

                // resolve commission_people.id
                $person_id = DB::table('commission_people')
                    ->where('branch_id', $branch_id)
                    ->where('status', 1)
                    ->where(function ($q) use ($incomingPerson) {
                        $q->where('id', $incomingPerson)
                            ->orWhere('user_id', $incomingPerson);
                    })
                    ->value('id');

                if (!$person_id) {
                    $invalid[] = ['row' => $r, 'reason' => 'Person not found'];
                    continue;
                }

                // ✅ Update if exists, otherwise insert
                $existing = DB::table('commission_rates')
                    ->where('branch_id', $branch_id)
                    ->where('commission_person_id', $person_id)
                    ->where('product_id', $product_id)
                    ->first();

                if ($existing) {
                    DB::table('commission_rates')->where('id', $existing->id)->update([
                        'rate' => $rateVal,
                        'updated_by' => $uid,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('commission_rates')->insert([
                        'branch_id' => $branch_id,
                        'commission_person_id' => $person_id,
                        'product_id' => $product_id,
                        'rate' => $rateVal,
                        'created_by' => $uid,
                        'created_at' => now(),
                        'updated_by' => $uid,
                        'updated_at' => now(),
                    ]);
                }

                $saved++;
            }

            DB::commit();

            return response()->json([
                'message' => empty($invalid) ? 'Saved' : 'Saved with some invalid rows',
                'saved_count' => $saved,
                'invalid' => $invalid,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'save failed', 'error' => $e->getMessage()], 500);
        }
    }




    /**
     * GET: Load people + products + rates and AUTO-sync collectors
     * query: ?branch_id=#
     */
    public function commission_all(Request $request)
    {
        $branch_access = session('branch_access');
        $sessionBranch = (int) session('branch_id');
        $isHO = ($sessionBranch === -1);

        $selectedBranch = ($branch_access == 1 && $isHO)
            ? (int) ($request->branch_id ?? 0)
            : $sessionBranch;

        if ($selectedBranch <= 0) {
            return response()->json(['message' => 'Branch is required'], 422);
        }

        DB::beginTransaction();
        try {
            $sync = $this->ensureCollectorCommissionAccounts($selectedBranch);

            // ✅ create 0% rows for every person × product
            $defaults = $this->ensureDefaultCommissionRates($selectedBranch);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to sync collector commission accounts',
                'error' => $e->getMessage()
            ], 500);
        }


        // ✅ products - LOAD from selectedBranch
        $products = DB::table('loan_category')
            ->select('idLoan_Category as id', 'Name as name')
            ->where('branch_id', $selectedBranch)
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        // people
        $people = DB::table('commission_people')
            ->where('branch_id', $selectedBranch)
            ->where('status', 1)
            ->orderByRaw("FIELD(type,'Collector','commission') DESC")
            ->orderBy('full_name')
            ->get();

        // rates
        $rates = DB::table('commission_rates')
            ->where('branch_id', $selectedBranch)
            ->select('commission_person_id', 'product_id', 'rate')
            ->get();

        return response()->json([
            'branch_id' => $selectedBranch,
            'sync'      => $sync,
            'products'  => $products,
            'people'    => $people,
            'rates'     => $rates,
        ]);
    }


    /**
     * GET: branches list
     */
    public function branches_all()
    {
        $branches = DB::table('branch')
            ->select('branch_id', 'Name')
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        return response()->json(['items' => $branches]);
    }

    /**
     * AUTO: Ensure all collectors have commission_people + bank accounts + opening log
     */
    private function ensureCollectorCommissionAccounts(int $branchId): array
    {
        $uid = session('user_data')["idUser"] ?? session('user_id') ?? session('id') ?? session('idUser') ?? 1; // fallback 1 if needed


        $collectors = DB::table('user')
            ->where('collector', 1)
            ->where('Status', 1)
            ->where('branch_id', $branchId)
            ->get();

        $debug = [
            'branch_id' => $branchId,
            'collectors_found' => $collectors->count(),
            'people_created' => 0,
            'bank_created' => 0,
            'bank_skipped_existing' => 0,
        ];

        foreach ($collectors as $c) {

            // ================= COMMISSION PERSON =================
            $person = DB::table('commission_people')
                ->where('branch_id', $branchId)
                ->where('user_id', $c->id)
                ->where('type', 'Collector')
                ->first();

            if (!$person) {

                $personId = DB::table('commission_people')->insertGetId([
                    'branch_id' => $branchId,
                    'type' => 'Collector',
                    'user_id' => $c->id,
                    'full_name' => $c->Full_Name,
                    'status' => 1,
                    'created_by' => $uid,
                    'updated_by' => $uid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $debug['people_created']++;
            } else {
                $personId = $person->id;
            }

            // ================= BANK ACCOUNT =================
            $accNo = 'COL-' . $personId;

            $exists = DB::table('company_bank_accounts')
                ->where('Account_No', $accNo)
                ->where('branch_id', $branchId)
                ->exists();

            if (!$exists) {
                DB::table('company_bank_accounts')->insert([
                    'Bank_Type' => 'Commission',
                    'code' => $accNo,
                    'Bank_Name' => 'Commission',
                    'Account_Name' => $c->Full_Name,
                    'Account_No' => $accNo,
                    'Bank_Branch' => '-',
                    'Account_Balance' => 0,
                    'type' => 'Cash and Bank',
                    'cashflow' => 'Non Applicable',
                    'acc_type_group' => 'Assets',
                    'User' => $uid,
                    'branch_id' => $branchId,
                ]);
                $debug['bank_created']++;
            } else {
                $debug['bank_skipped_existing']++;
            }
        }

        return $debug;
    }


    private function ensureDefaultCommissionRates(int $branchId): array
    {
        $uid = session('user_data')["idUser"] ?? session('user_id') ?? session('id') ?? session('idUser') ?? 1;

        // active products
        $products = DB::table('loan_category')
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->pluck('idLoan_Category');

        // active people
        $people = DB::table('commission_people')
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->pluck('id');

        $created = 0;

        foreach ($people as $personId) {
            foreach ($products as $productId) {

                $exists = DB::table('commission_rates')
                    ->where('branch_id', $branchId)
                    ->where('commission_person_id', $personId)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$exists) {
                    DB::table('commission_rates')->insert([
                        'branch_id' => $branchId,
                        'commission_person_id' => $personId,
                        'product_id' => $productId,
                        'rate' => 0,
                        'created_by' => $uid,
                        'created_at' => now(),
                        'updated_by' => $uid,
                        'updated_at' => now(),
                    ]);
                    $created++;
                }
            }
        }

        return [
            'branch_id' => $branchId,
            'default_rates_created' => $created
        ];
    }
}
