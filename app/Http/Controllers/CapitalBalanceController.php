<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAppSettingsRequest;
use App\Models\AppSettings;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class CapitalBalanceController extends Controller
{

    private string $table = 'app_settings';

    public function __construct()
    {
        $this->ensureSchema();
    }

    /**
     * Display a listing of the resource.
     */
    public function index($loan_id, $check = 0)
    {
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->first();
        if ($loan) {
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->get();

            $interestBalanceSum = round($installment->sum('Interest_Balance'), 2);
            $capitalBalanceSum = round($installment->sum('capital_balance'), 2);
            $totalBalance = round($installment->sum('Total_Balance'), 2);

            $status = 0;
            if ($totalBalance < 1) {
                $status = 1;
                $interestBalanceSum = 0;
                $capitalBalanceSum = 0;
                $totalBalance = 0;


                DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                    ->update([
                        'Interest_Balance' => 0.00,
                        'Panalty_Balance' => 0.00,
                        'capital_balance' => 0.00,
                        'Total_Balance' => 0.00,
                        'Status' => '1',
                        'Panelty_status' => '2',
                    ]);
            }

            updateWithBranch('customer_loan', 'idCustomer_Loan', $loan_id, [
                'capital_balance' => $capitalBalanceSum,
                'installment_balance' => $interestBalanceSum,
                'Balance_Amount' => $totalBalance,
                'Status' => $status,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($loan_id)
    {
        $loans = tableWithBranch('customer_loan')->where('idCustomer_Loan', '=', $loan_id)->where('Status', '!=', '1')->first();
        if ($loans) {
            $ins_count = $loans->Installment_Count;
            $installment_count = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)->count();
            if ($ins_count == $installment_count) {
                $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $loans->Loan_Category_idLoan_Category)->where('Interest_method', '=', 'Flat Rate')->first();
                if ($product) {
                    $loan_id = $loans->idCustomer_Loan;
                    $capital_amount = $loans->Amount;
                    $interest_amount = $loans->Interest_Amount;

                    $ins_capital = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)->sum('capital_amount');
                    $ins_interest = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)->sum('interest_amount');
                    $capital_additional_amount = 0;
                    $interest_additional_amount = 0;

                    if ($capital_amount != $ins_capital) {
                        $capital_additional_amount = $capital_amount - $ins_capital;
                    }

                    if ($interest_amount != $ins_interest) {
                        $interest_additional_amount = $interest_amount - $ins_interest;
                    }

                    if ($capital_additional_amount != 0 || $interest_additional_amount != 0) {
                        $lastInstallment = DB::table('installments')
                            ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                            ->orderByDesc('idInstallments')
                            ->first();

                        if ($lastInstallment) {

                            $capital_additional_amount = (float) $capital_additional_amount;
                            $interest_additional_amount = (float) $interest_additional_amount;


                            DB::table('installments')
                                ->where('idInstallments', $lastInstallment->idInstallments)
                                ->update([
                                    'Installment_Amount' => DB::raw("Installment_Amount + $capital_additional_amount + $interest_additional_amount"),
                                    'capital_amount'     => DB::raw("capital_amount + $capital_additional_amount"),
                                    'interest_amount'    => DB::raw("interest_amount + $interest_additional_amount"),
                                    'Total_Amount'       => DB::raw("Total_Amount + $capital_additional_amount + $interest_additional_amount"),
                                    'capital_balance'    => DB::raw("capital_balance + $capital_additional_amount"),
                                    'Interest_Balance'   => DB::raw("Interest_Balance + $interest_additional_amount"),
                                    'Total_Balance'      => DB::raw("Total_Balance + $capital_additional_amount + $interest_additional_amount"),
                                ]);
                        }
                    }
                }
            }
        }
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
    public function show($loan_id)
    {
        $loans = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->get();
        foreach ($loans as $loan) {
            $loan_id = $loan->idCustomer_Loan;
            $loan_date = date('Y-m-d', strtotime($loan->Date_Time));
            $loan_logs = tableWithBranch('Loan_Log')->where('Loan_ID', $loan_id)->get();

            foreach ($loan_logs as $logs) {
                $type = $logs->Type;


                if ($type == 'Payment Undo') {
                    $type_id = $logs->Type_ID;
                    if ($loan_id != $type_id) {
                        $payment_log = tableWithBranch('Loan_Log')->where('Type_ID', $type_id)->where('Loan_ID', '=', $loan_id)->where('Type', '=', 'Customer Payment')->first();
                        if ($payment_log) {
                            $interest_amount = $payment_log->Interest_Payment;
                            $capital_amount = $payment_log->Capital_Payment;

                            DB::table('Loan_Log')
                                ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                                ->update([
                                    'Interest_Payment' => $interest_amount,
                                    'Capital_Payment' => $capital_amount,
                                ]);
                        }
                    }
                }
            }




            $count = 0;
            foreach ($loan_logs as $logs) {
                $type = $logs->Type;

                $log_time = date('H:i:s', strtotime($logs->Date_Time));
                $new_datetime = $loan_date . ' ' . $log_time;

                if ($type == 'Issue Loan') {
                    DB::table('Loan_Log')
                        ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                        ->update([
                            'Date_Time' => $new_datetime
                        ]);
                } else if ($type == 'Customer Payment') {
                    $customer_payment = tableWithBranch('customer_payments')
                        ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                        ->orderBy('idCustomer_Payments', 'asc')
                        ->get();

                    $Payment = $customer_payment->get($count); // index starts from 0, so 2 means 3rd item

                    if ($Payment) {
                        $payment_date = $Payment->Date;
                        $new_datetime = $payment_date . ' ' . $log_time;



                        // Get previous log before current one
                        $previous_log = tableWithBranch('Loan_Log')
                            ->where('Loan_ID', $loan_id)
                            ->where('Loan_Log_ID', '<', $logs->Loan_Log_ID)
                            ->orderBy('Loan_Log_ID', 'desc')
                            ->first();

                        if ($previous_log) {
                            $Interest_Balance_previous = $previous_log->Interest_Balance;
                            $Capital_Balance_previous = $previous_log->Capital_Balance;
                            $Total_Pending_Balance_previous = $previous_log->Total_Pending_Balance;

                            $Interest_Payment = $logs->Interest_Payment;
                            $Capital_Payment = $logs->Capital_Payment;

                            DB::table('Loan_Log')
                                ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                                ->update([
                                    'Type_ID' => $Payment->idCustomer_Payments,
                                    'Date_Time' => $new_datetime,
                                    'Interest_Balance' => $Interest_Balance_previous - $Interest_Payment,
                                    'Capital_Balance' => $Capital_Balance_previous - $Capital_Payment,
                                    'Total_Pending_Balance' => $Total_Pending_Balance_previous - $Interest_Payment - $Capital_Payment,
                                ]);
                            DB::table('customer_loan')
                                ->where('idCustomer_Loan', $loan_id)
                                ->update([
                                    'installment_balance' => $Interest_Balance_previous - $Interest_Payment,
                                    'capital_balance' => $Capital_Balance_previous - $Capital_Payment,
                                    'Balance_Amount' => $Total_Pending_Balance_previous - $Interest_Payment - $Capital_Payment,
                                ]);

                            $count++;
                        }
                    }
                } else {
                    // Get previous log before current one
                    $previous_log = tableWithBranch('Loan_Log')
                        ->where('Loan_ID', $loan_id)
                        ->where('Loan_Log_ID', '<', $logs->Loan_Log_ID)
                        ->orderBy('Loan_Log_ID', 'desc')
                        ->first();

                    if ($previous_log) {
                        $Interest_Balance_previous = $previous_log->Interest_Balance;
                        $Capital_Balance_previous = $previous_log->Capital_Balance;
                        $Total_Pending_Balance_previous = $previous_log->Total_Pending_Balance;

                        $Interest_Payment = $logs->Interest_Payment;
                        $Capital_Payment = $logs->Capital_Payment;

                        DB::table('Loan_Log')
                            ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                            ->update([
                                'Interest_Balance' => $Interest_Balance_previous + $Interest_Payment,
                                'Capital_Balance' => $Capital_Balance_previous + $Capital_Payment,
                                'Total_Pending_Balance' => $Total_Pending_Balance_previous + $Interest_Payment + $Capital_Payment,
                            ]);


                        DB::table('customer_loan')
                            ->where('idCustomer_Loan', $loan_id)
                            ->update([
                                'installment_balance' => $Interest_Balance_previous + $Interest_Payment,
                                'capital_balance' => $Capital_Balance_previous + $Capital_Payment,
                                'Balance_Amount' => $Total_Pending_Balance_previous + $Interest_Payment + $Capital_Payment,
                            ]);
                    }
                }
            }
        }
        return response()->json(['message' => 'Loan log update completed']);
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

    // GET /settings/all
    public function all()
    {
        try {
            //code...
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


            //Dynamic headoffice approval keys

            $approvalSettings = AppSettings::query()
                ->where('key', 'like', 'headoffice_approval_%')
                ->pluck('value', 'key');


            //Merge & return

            $allSettings = $fixedSettings->merge($approvalSettings);

            return response()->json([
                'items' => $allSettings
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error fetching app settings: ' . $th->getMessage());
            return response()->json(['message' => 'Failed to fetch app settings'], 500);
        }
    }


    // POST /settings/upsert
    public function upsert(UpdateAppSettingsRequest $request)
    {
        try {
            //code...
            DB::beginTransaction();

            $data = $request->validated();

            $key = $data['key'];
            $value = $data['value'];
            $uid = auth()->id();

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
            dd($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }




    /**
     * Create table if not exists (per your requirement to do it in controller).
     * Columns: id, key (unique), value, created_by, updated_by, timestamps
     */
    private function ensureSchema(): void
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('key')->unique();
                $table->string('value');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        } else {
            // Optional: ensure columns exist if table already there
            $this->ensureColumn('value', fn(Blueprint $t) => $t->string('value')->nullable(false));
            $this->ensureColumn('created_by', fn(Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable());
            $this->ensureColumn('updated_by', fn(Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable());
            // timestamps usually exist; add if missing
            if (!Schema::hasColumn($this->table, 'created_at') || !Schema::hasColumn($this->table, 'updated_at')) {
                Schema::table($this->table, function (Blueprint $t) {
                    $t->timestamps();
                });
            }
        }
    }

    // Helper to add a column if it doesn't exist
    private function ensureColumn(string $name, \Closure $definition): void
    {
        if (!Schema::hasColumn($this->table, $name)) {
            Schema::table($this->table, function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }



    public function panelty_remove()
    {

        //        $rows = DB::table('installments as i')
        //            ->join('customer_loan as c', 'c.idCustomer_Loan', '=', 'i.Customer_Loan_idCustomer_Loan')
        //            ->where('i.Status', 0)
        //            ->where('i.Paid_Amount', '>', 0)
        //            ->where('i.Panalty_Amount', '!=', 0)
        //            ->where('i.Total_Balance', '>', 1)
        //            ->where('c.Collection_Type', 'Weekly')
        //            ->get();
        //
        //        DB::table('Loan_Log')->where('Type','=','Adjustment- Asipiya')->delete();
        //        foreach ($rows as $item){
        //            $ins_amount=$item->Installment_Amount;
        //
        //            $panelty=$ins_amount/100*3;
        //
        //
        //            DB::table('installments')
        //                ->where('idInstallments', $item->idInstallments)
        //                ->update([
        //                    'Panalty_Amount'=>$panelty,
        //                    'Panelty_count'    => '1',
        //                ]);
        //        }
        //
        //        $loans=DB::table('customer_loan')->where('Status','=','0')->get();
        //        foreach ($loans as $loan){
        //
        //            $installment=DB::table('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->get();
        //            foreach ($installment as $ins){
        //
        //                $installment_amount=$ins->Installment_Amount;
        //                $Panalty_Amount=$ins->Panalty_Amount;
        //                $Paid_Amount=$ins->Paid_Amount;
        //                $Paid_Amount_2=$ins->Paid_Amount;
        //                $capital_amount=$ins->capital_amount;
        //                $interest_amount=$ins->interest_amount;
        //
        //
        //
        //                $interest_balance=$interest_amount;
        //                $capital_balance=$capital_amount;
        //
        //                if ($Paid_Amount<=$Panalty_Amount){
        //                    $panelty_balance=$Panalty_Amount-$Paid_Amount;
        //                }else{
        //                    $panelty_balance=0;
        //                    $Paid_Amount=$Paid_Amount-$Panalty_Amount;
        //                    if ($Paid_Amount<=$interest_amount){
        //                        $interest_balance=$interest_amount-$Paid_Amount;
        //                    }else{
        //                        $interest_balance=0;
        //                        $Paid_Amount=$Paid_Amount-$interest_amount;
        //
        //                        if ($Paid_Amount<=$capital_amount){
        //                            $capital_balance=$capital_amount-$Paid_Amount;
        //                        }else{
        //                            $capital_balance=0;
        //                            $Paid_Amount_2=$capital_amount+$interest_amount+$Panalty_Amount;
        //                        }
        //                    }
        //                }
        //
        //
        //                $installmentAmount = (float) $installment_amount;  // cap+interest for this installment
        //                $penaltyAmount     = (float) $Panalty_Amount;       // newly computed penalty
        //                $penaltyBalance    = (float) $panelty_balance;
        //                $interestBalance   = (float) $interest_balance;
        //                $capitalBalance    = (float) $capital_balance;
        //
        //                $totalAmount  = round($installmentAmount + $penaltyAmount, 2);
        //                $totalBalance = round($capitalBalance + $interestBalance + $penaltyBalance, 2);
        //
        //                DB::table('installments')
        //                    ->where('idInstallments', $ins->idInstallments)
        //                    ->update([
        //                        'Total_Amount'     => $totalAmount,
        //                        'Panalty_Balance'  => round($penaltyBalance, 2),
        //                        'Interest_Balance' => round($interestBalance, 2),
        //                        'capital_balance'  => round($capitalBalance, 2),
        //                        'Paid_Amount'  => round($Paid_Amount_2, 2),
        //                        'Total_Balance'    => $totalBalance,
        //
        //                    ]);
        //            }
        //
        //            $Interest_Balance_sum=DB::table('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->sum('Interest_Balance');
        //            $capital_balance_sum=DB::table('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->sum('capital_balance');
        //            $Total_Balance_sum=DB::table('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->sum('Total_Balance');
        //            $Panalty_Balance_sum=DB::table('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->sum('Panalty_Balance');
        //
        //            DB::table('customer_loan')
        //                ->where('idCustomer_Loan', $loan->idCustomer_Loan)
        //                ->update([
        //                    'Balance_Amount'=>$Total_Balance_sum,
        //                    'capital_balance'=>$capital_balance_sum,
        //                    'installment_balance'=>$Interest_Balance_sum,
        //                ]);
        //
        //
        //            $user_id = (int)session('userid');
        //
        //
        //
        //            DB::table('Loan_Log')->insert([
        //                'Loan_ID' => $loan->idCustomer_Loan,
        //                'Date_Time' => date('Y-m-d H:i:s'),
        //                'Type' => 'Adjustment- Asipiya',
        //                'Type_ID' => '0',
        //                'Description' => 'Adjustment- Asipiya',
        //                'Amount' => '0.00',
        //                'Panelty_Payment' => '0.00',
        //                'Interest_Payment' => '0.00',
        //                'Capital_Payment' => '0.00',
        //                'Savings_Payment' => '0.00',
        //                'Panelty_Balance' => $Panalty_Balance_sum,
        //                'Interest_Balance' => $Interest_Balance_sum,
        //                'Capital_Balance' => $capital_balance_sum,
        //                'Total_Pending_Balance' => $Total_Balance_sum,
        //                'Saving_Account_Balance' => '0.00',
        //                'User_idUser' => $user_id,
        //                'branch_id' => session('branch_id')
        //            ]);
        //
        //
        //
        //        }
    }



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

        $uid = auth()->id();

        $id = DB::table('commission_people')->insertGetId([
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

        return response()->json([
            'success' => true,
            'person' => DB::table('commission_people')->where('id', $id)->first()
        ]);
    }



    public function commission_save_rates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|min:1',
            'rates' => 'required|array',
            'rates.*.person_id' => 'required|integer',
            'rates.*.product_id' => 'required|integer',
            'rates.*.rate' => 'nullable|numeric|min:0|max:999999',
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

        if ($selectedBranch <= 0) {
            return response()->json(['message' => 'Invalid branch'], 422);
        }

        $uid = auth()->id();

        DB::beginTransaction();
        try {
            foreach ($request->rates as $r) {
                DB::table('commission_rates')->updateOrInsert(
                    [
                        'branch_id'             => $selectedBranch,
                        'commission_person_id'  => (int)$r['person_id'],
                        'product_id'            => (int)$r['product_id'],
                    ],
                    [
                        'rate' => (float)($r['rate'] ?? 0),
                        'updated_by' => $uid,
                        'updated_at' => now(),
                        'created_by' => $uid,
                        'created_at' => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function commission_all(Request $request)
    {
        $branch_access = session('branch_access');      // 1 = HO can select
        $sessionBranch = (int) session('branch_id');    // branch user branch id
        $isHO = ($sessionBranch === -1);

        // selected branch from UI (HO only). Branch users force session branch.
        $selectedBranch = ($branch_access == 1 && $isHO)
            ? (int) ($request->branch_id ?? 0)
            : $sessionBranch;

        if ($selectedBranch <= 0) {
            return response()->json(['message' => 'Branch is required'], 422);
        }

        // ✅ Products (your "products" = loan_category)
        $products = tableWithBranch('loan_category')
            ->select('idLoan_Category as id', 'Name as name')
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        // ✅ Collectors list (adjust condition if needed)
        $collectors = tableWithBranch('user')
            ->select('id as user_id', 'Full_Name as full_name')
            ->where('Status', 1)
            ->where('collector', '1') // adjust
            ->orderBy('Full_Name')
            ->get();

        // ✅ Ensure commission_people rows exist (branch-wise)
        $uid = auth()->id();
        foreach ($collectors as $c) {
            DB::table('commission_people')->updateOrInsert(
                [
                    'branch_id' => $selectedBranch,
                    'user_id'   => $c->user_id,
                ],
                [
                    'type'       => 'Collector',
                    'full_name'  => $c->full_name,
                    'status'     => 1,
                    'updated_by' => $uid,
                    'updated_at' => now(),
                    'created_by' => $uid,
                    'created_at' => now(),
                ]
            );
        }

        // ✅ People for this branch
        $people = DB::table('commission_people')
            ->where('branch_id', $selectedBranch)
            ->where('status', 1)
            ->orderByRaw("FIELD(type,'Collector','Other')")
            ->orderBy('full_name')
            ->get();

        // ✅ Rates for this branch
        $rates = DB::table('commission_rates')
            ->where('branch_id', $selectedBranch)
            ->select('commission_person_id', 'product_id', 'rate')
            ->get();

        return response()->json([
            'branch_id' => $selectedBranch,
            'products'  => $products,
            'people'    => $people,
            'rates'     => $rates,
        ]);
    }


    public function branches_all()
    {
        $branches = DB::table('branch')
            ->select('branch_id', 'Name')
            ->where('status', 1)
            ->orderBy('Name')
            ->get();

        return response()->json(['items' => $branches]);
    }


}
