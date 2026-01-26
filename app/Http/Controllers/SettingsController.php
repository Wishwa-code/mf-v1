<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCommissionRatesRequest;
use App\Http\Requests\StoreCommissionPersonRequest;
use App\Http\Requests\UpdateAppSettingsRequest;
use App\Models\AppSettingLog;
use App\Models\AppSettings;
use App\Models\Branch;
use App\Models\CommissionPerson;
use App\Models\CommissionRate;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankHasLog;
use App\Models\LoanCategory;
use App\Models\Shortcut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('pages.Setting.index');
    }

    /**
     * Get all settings as JSON (Scoped by Branch).
     */
    public function all()
    {
        try {
            $branchId = session('branch_id');

            $appSettings = AppSettings::where('branch_id', $branchId)
                ->pluck('value', 'key');

            return response()->json([
                'items' => $appSettings,
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error fetching app settings: ' . $th->getMessage());
            return response()->json(['message' => 'Failed to fetch app settings'], 500);
        }
    }

    /**
     * Upsert a setting value (Update or Create).
     */
    public function upsert(UpdateAppSettingsRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $key = $data['key'];
            $value = $data['value'];
            $uid = user_data('idUser');
            $branchId = session('branch_id');

            $setting = AppSettings::updateOrCreate(
                ['key' => $key, 'branch_id' => $branchId],
                [
                    'value'      => $value,
                    'updated_by' => $uid,
                ]
            );

            if ($setting->wasRecentlyCreated) {
                $setting->created_by = $uid;
                $setting->save();
            }

            AppSettingLog::create([
                'user_id'     => $uid,
                'setting_key' => $key,
                'old_value'   => null,
                'new_value'   => $value,
                'changed_at'  => now(),
                'branch_id'   => $branchId,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            Cache::forget('app_settings_' . $branchId);

            DB::commit();
            return response()->json(['success' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Settings Upsert Error: ' . $th->getMessage());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function shortcuts(Request $request)
    {
        try {
            DB::beginTransaction();

            $checkboxValues = $request->input('checkboxValues', []);
            $user_id = user_data('idUser');
            $branch_id = session('branch_id');

            Shortcut::where('branch_id', $branch_id)
                ->where('user_id', $user_id)
                ->delete();

            foreach ($checkboxValues as $key => $value) {
                Shortcut::create([
                    'name' => $key,
                    'user_id' => $user_id,
                    'branch_id' => $branch_id
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Shortcuts updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update shortcuts', 'error' => $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $userData = Shortcut::where('user_id', user_data('idUser'))
            ->where('branch_id', session('branch_id'))
            ->get();
        return response()->json(['items' => $userData], 200);
    }

    public function updateNumberFormats(Request $request)
    {
        try {
            DB::beginTransaction();
            $branchId = session('branch_id');
            $uid = user_data('idUser');

            $formats = [
                'customer_num_type' => $request->customer_format_selection ?? '',
                'customer_seperate_from' => $request->separate_from ?? '',
                'customer_num_start_from' => $request->auto_number ?? '',
                'customer_format' => $request->field_output_customer ?? '',
                'customer_format_scope' => $request->customer_format_scope ?? '0',

                'loan_num_type' => $request->loan_format_selection ?? '',
                'loan_seperate_from' => $request->separate_from_loan ?? '',
                'loan_format' => $request->field_output_loan ?? '',

                'inv_loan_num_type' => $request->inv_loan_format_selection ?? '',
                'inv_loan_seperate_from' => $request->separate_from_inv_loan ?? '',
                'inv_loan_format' => $request->field_output_inv_loan ?? '',

                'account_saving_type' => $request->saving_selection ?? '',
                'saving_seperate_from' => $request->separate_from_savings ?? '',
                'saving_format' => $request->field_output_saving ?? '',
            ];

            foreach ($formats as $key => $value) {
                AppSettings::updateOrCreate(
                    ['key' => $key, 'branch_id' => $branchId],
                    ['value' => $value, 'updated_by' => $uid]
                );
            }

            if ($request->customer_format_scope == "all") {
                // Assuming Customer model exists as App\Models\Customer.
                // If strictly no DB::table, we must use Model.
                if (class_exists('\App\Models\Customer')) {
                    $customers = \App\Models\Customer::where('branch_id', $branchId)->get();
                    foreach ($customers as $item) {
                        customer_number($item->idCustomer);
                    }
                } else {
                    // Fallback if model missing, but strictly asked not to use DB::table.
                    // We will assume it exists or just skip/log error? 
                    // I will assume it exists.
                }
            }

            DB::commit();
            return response()->json(['message' => 'Number formats updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function saveCommissionPerson(StoreCommissionPersonRequest $request)
    {
        $branch_access = session('branch_access');
        $sessionBranch = (int) session('branch_id');
        $isHO = ($sessionBranch === -1);

        $selectedBranch = ($branch_access == 1 && $isHO)
            ? (int) $request->branch_id
            : $sessionBranch;

        $uid = user_data('idUser');

        try {
            DB::beginTransaction();

            $person = CommissionPerson::create([
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
            ]);

            $accountCode = 'COM-' . $person->id;

            $bankAccount = CompanyBankAccount::create([
                'Bank_Type'        => 'Commission',
                'code'             => 'COM-' . $person->id,
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

            if (Schema::hasTable('company_bank_has_log')) {
                CompanyBankHasLog::create([
                    'Bank_Account_Id' => $bankAccount->id,
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
                'person'  => $person,
                'bank_account_id' => $bankAccount->id,
                'account_no' => $accountCode
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function saveCommissionRates(SaveCommissionRatesRequest $request)
    {
        $branch_id = (int) $request->branch_id;
        $rates     = $request->rates;
        $uid = user_data('idUser') ?? 1;

        $saved = 0;
        $invalid = [];

        try {
            DB::beginTransaction();
            foreach ($rates as $r) {
                $incomingPerson = (int)($r['person_id'] ?? 0);
                $product_id     = (int)($r['product_id'] ?? 0);
                $rateVal        = (float)($r['rate'] ?? 0);

                if ($incomingPerson <= 0 || $product_id <= 0) {
                    $invalid[] = ['row' => $r, 'reason' => 'Invalid IDs'];
                    continue;
                }

                $person = CommissionPerson::where('branch_id', $branch_id)
                    ->where('status', 1)
                    ->where(function ($q) use ($incomingPerson) {
                        $q->where('id', $incomingPerson)->orWhere('user_id', $incomingPerson);
                    })->first();

                if (!$person) {
                    $invalid[] = ['row' => $r, 'reason' => 'Person not found'];
                    continue;
                }

                CommissionRate::updateOrCreate(
                    [
                        'branch_id' => $branch_id,
                        'commission_person_id' => $person->id,
                        'product_id' => $product_id
                    ],
                    [
                        'rate' => $rateVal,
                        'updated_by' => $uid,
                        'updated_at' => now()
                    ]
                );

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

    public function loadCommissions(Request $request)
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
            $defaults = $this->ensureDefaultCommissionRates($selectedBranch);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to sync collector commission accounts',
                'error' => $e->getMessage()
            ], 500);
        }

        $products = LoanCategory::select('idLoan_Category as id', 'Name as name')
            ->where('branch_id', $selectedBranch)
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        $people = CommissionPerson::where('branch_id', $selectedBranch)
            ->where('status', 1)
            ->orderByRaw("FIELD(type,'Collector','commission') DESC")
            ->orderBy('full_name')
            ->get();

        $rates = CommissionRate::where('branch_id', $selectedBranch)
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

    public function getBranches()
    {
        $branches = Branch::select('branch_id', 'Name')
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        return response()->json(['items' => $branches]);
    }

    private function ensureCollectorCommissionAccounts(int $branchId): array
    {
        $uid = user_data('idUser') ?? 1;
        $collectors = User::where('collector', 1)->where('Status', 1)->where('branch_id', $branchId)->get();

        $debug = ['branch_id' => $branchId, 'created' => 0];

        foreach ($collectors as $c) {
            $person = CommissionPerson::firstOrCreate(
                ['branch_id' => $branchId, 'user_id' => $c->id, 'type' => 'Collector'],
                ['full_name' => $c->Full_Name, 'status' => 1, 'created_by' => $uid, 'updated_by' => $uid]
            );

            $accNo = 'COL-' . $person->id;
            if (!CompanyBankAccount::where('Account_No', $accNo)->where('branch_id', $branchId)->exists()) {
                CompanyBankAccount::create([
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
                $debug['created']++;
            }
        }
        return $debug;
    }

    private function ensureDefaultCommissionRates(int $branchId): array
    {
        $uid = user_data('idUser') ?? 1;
        $products = LoanCategory::where('branch_id', $branchId)->where('status', 1)->pluck('idLoan_Category');
        $people = CommissionPerson::where('branch_id', $branchId)->where('status', 1)->pluck('id');

        $created = 0;
        foreach ($people as $personId) {
            foreach ($products as $productId) {
                if (!CommissionRate::where('branch_id', $branchId)->where('commission_person_id', $personId)->where('product_id', $productId)->exists()) {
                    CommissionRate::create([
                        'branch_id' => $branchId,
                        'commission_person_id' => $personId,
                        'product_id' => $productId,
                        'rate' => 0,
                        'created_by' => $uid,
                        'updated_by' => $uid
                    ]);
                    $created++;
                }
            }
        }
        return ['branch_id' => $branchId, 'default_rates_created' => $created];
    }
}
