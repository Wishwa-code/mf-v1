<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAppSettingsRequest;
use App\Models\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Get all settings as JSON.
     */
    public function all()
    {
        try {
            $keys = [
                'payment_member_name',
                'loan_disbursement_policy',
                'payment_backdate',
                'loan_order',
                'max_allowed_loans',
                'empty_row_count',
                'document_types',
                'image_types',
                'guardian_image_types',
                'guarantor_image_types',
                'agreement_image_types',
                'collector_txn_modes',
                'fund_request_columns',
                'disbursement_columns',
                'document_upload_restriction',
                'guarantees_restriction',
                'change_product_details',
                'first_installment_daily',
                'first_installment_weekly',
                'first_installment_monthly',
                'recovery_account_status',
                'collection_days',
                'due_skip_type',
            ];

            // Get fixed keys
            $fixedSettings = AppSettings::query()
                ->whereIn('key', $keys)
                ->pluck('value', 'key');

            // Dynamic headoffice approval keys
            $approvalSettings = AppSettings::query()
                ->where('key', 'like', 'headoffice_approval_%')
                ->pluck('value', 'key');

            // Merge & return
            $allSettings = $fixedSettings->merge($approvalSettings);

            // Fetch company data for Number Formats
            $company = DB::table('company')->where('branch_id', session('branch_id'))->first();

            return response()->json([
                'items' => $allSettings,
                'company' => $company
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error fetching app settings: ' . $th->getMessage());
            return response()->json(['message' => 'Failed to fetch app settings'], 500);
        }
    }

    /**
     * Upsert a setting value.
     */
    public function upsert(UpdateAppSettingsRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $key = $data['key'];
            $value = $data['value'];
            $uid = user_data('idUser');

            $setting = AppSettings::where('key', $key)->first();
            $oldValue = $setting?->value;

            if ($setting) {
                $setting->update([
                    'value'      => $value,
                    'updated_by' => $uid,
                ]);
            } else {
                AppSettings::create([
                    'key'        => $key,
                    'value'      => $value,
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */

            DB::table('app_setting_log')->insert([
                'user_id'     => $uid,
                'setting_key' => $key,
                'old_value'   => $oldValue,
                'new_value'   => $value,
                'changed_at'  => now(),
                'branch_id'   => session('branch_id'),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            Cache::forget('app_settings');

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
        $checkboxValues = $request->input('checkboxValues', []);
        $user_id = user_data('idUser');

        DB::table('shortcut')
            ->where('branch_id', session('branch_id'))
            ->delete();

        foreach ($checkboxValues as $key => $value) {
            $data = ['name' => $key, 'user_id' => $user_id];
            // Call the helper function
            insertWithBranch('shortcut', $data);
        }

        return response()->json(['message' => 'Shortcuts updated successfully']);
    }

    public function show()
    {
        $userData = tableWithBranch('shortcut')->where('user_id', user_data('idUser'))->get();
        return response()->json(['items' => $userData], 200);
    }
}
