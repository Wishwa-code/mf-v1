<?php

namespace App\Http\Controllers;

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
    public function index($loan_id,$check=0)
    {
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->first();
        if ($loan) {
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->get();

            $interestBalanceSum = round($installment->sum('Interest_Balance'),2);
            $capitalBalanceSum = round($installment->sum('capital_balance'),2);
            $totalBalance = round($installment->sum('Total_Balance'),2);

            $status=0;
            if ($totalBalance<1){
                $status=1;
                $interestBalanceSum=0;
                $capitalBalanceSum=0;
                $totalBalance=0;


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
        $loans = tableWithBranch('customer_loan')->where('idCustomer_Loan','=',$loan_id)->where('Status','!=','1')->first();
        if ($loans) {
            $ins_count=$loans->Installment_Count;
            $installment_count=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->count();
            if ($ins_count==$installment_count){
                $product=tableWithBranch('loan_category')->where('idLoan_Category','=',$loans->Loan_Category_idLoan_Category)->where('Interest_method','=','Flat Rate')->first();
                if ($product){
                    $loan_id=$loans->idCustomer_Loan;
                    $capital_amount=$loans->Amount;
                    $interest_amount=$loans->Interest_Amount;

                    $ins_capital=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->sum('capital_amount');
                    $ins_interest=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->sum('interest_amount');
                    $capital_additional_amount=0;
                    $interest_additional_amount=0;

                    if ($capital_amount!=$ins_capital){
                        $capital_additional_amount=$capital_amount-$ins_capital;
                    }

                    if ($interest_amount!=$ins_interest){
                        $interest_additional_amount=$interest_amount-$ins_interest;
                    }

                    if ($capital_additional_amount!=0 || $interest_additional_amount!=0){
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
        $loans=tableWithBranch('customer_loan')->where('idCustomer_Loan',$loan_id)->get();
        foreach ($loans as $loan){
            $loan_id=$loan->idCustomer_Loan;
            $loan_date = date('Y-m-d', strtotime($loan->Date_Time));
            $loan_logs=tableWithBranch('Loan_Log')->where('Loan_ID',$loan_id)->get();

            foreach ($loan_logs as $logs){
                $type=$logs->Type;


                if ($type=='Payment Undo'){
                    $type_id=$logs->Type_ID;
                    if ($loan_id!=$type_id){
                        $payment_log=tableWithBranch('Loan_Log')->where('Type_ID',$type_id)->where('Loan_ID','=',$loan_id)->where('Type','=','Customer Payment')->first();
                        if ($payment_log){
                            $interest_amount=$payment_log->Interest_Payment;
                            $capital_amount=$payment_log->Capital_Payment;

                            DB::table('Loan_Log')
                                ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                                ->update([
                                    'Interest_Payment' =>$interest_amount,
                                    'Capital_Payment' =>$capital_amount,
                                ]);

                        }
                    }
                }
            }




            $count=0;
            foreach ($loan_logs as $logs){
                $type=$logs->Type;

                $log_time = date('H:i:s', strtotime($logs->Date_Time));
                $new_datetime = $loan_date . ' ' . $log_time;

                if ($type=='Issue Loan'){
                    DB::table('Loan_Log')
                        ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                        ->update([
                            'Date_Time' =>$new_datetime
                        ]);
                }else if($type=='Customer Payment'){
                    $customer_payment = tableWithBranch('customer_payments')
                        ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                        ->orderBy('idCustomer_Payments', 'asc')
                        ->get();

                    $Payment = $customer_payment->get($count); // index starts from 0, so 2 means 3rd item

                    if ($Payment) {
                        $payment_date=$Payment->Date;
                        $new_datetime=$payment_date . ' ' . $log_time;



                        // Get previous log before current one
                        $previous_log = tableWithBranch('Loan_Log')
                            ->where('Loan_ID', $loan_id)
                            ->where('Loan_Log_ID', '<', $logs->Loan_Log_ID)
                            ->orderBy('Loan_Log_ID', 'desc')
                            ->first();

                        if ($previous_log) {
                            $Interest_Balance_previous=$previous_log->Interest_Balance;
                            $Capital_Balance_previous=$previous_log->Capital_Balance;
                            $Total_Pending_Balance_previous=$previous_log->Total_Pending_Balance;

                            $Interest_Payment=$logs->Interest_Payment;
                            $Capital_Payment=$logs->Capital_Payment;

                            DB::table('Loan_Log')
                                ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                                ->update([
                                    'Type_ID' =>$Payment->idCustomer_Payments,
                                    'Date_Time' =>$new_datetime,
                                    'Interest_Balance' =>$Interest_Balance_previous-$Interest_Payment,
                                    'Capital_Balance' =>$Capital_Balance_previous-$Capital_Payment,
                                    'Total_Pending_Balance' =>$Total_Pending_Balance_previous-$Interest_Payment-$Capital_Payment,
                                ]);
                            DB::table('customer_loan')
                                ->where('idCustomer_Loan', $loan_id)
                                ->update([
                                    'installment_balance' =>$Interest_Balance_previous-$Interest_Payment,
                                    'capital_balance' =>$Capital_Balance_previous-$Capital_Payment,
                                    'Balance_Amount' =>$Total_Pending_Balance_previous-$Interest_Payment-$Capital_Payment,
                                ]);

                            $count++;
                        }
                    }
                }else{
                    // Get previous log before current one
                    $previous_log = tableWithBranch('Loan_Log')
                        ->where('Loan_ID', $loan_id)
                        ->where('Loan_Log_ID', '<', $logs->Loan_Log_ID)
                        ->orderBy('Loan_Log_ID', 'desc')
                        ->first();

                    if ($previous_log) {
                        $Interest_Balance_previous=$previous_log->Interest_Balance;
                        $Capital_Balance_previous=$previous_log->Capital_Balance;
                        $Total_Pending_Balance_previous=$previous_log->Total_Pending_Balance;

                        $Interest_Payment=$logs->Interest_Payment;
                        $Capital_Payment=$logs->Capital_Payment;

                        DB::table('Loan_Log')
                            ->where('Loan_Log_ID', $logs->Loan_Log_ID)
                            ->update([
                                'Interest_Balance' =>$Interest_Balance_previous+$Interest_Payment,
                                'Capital_Balance' =>$Capital_Balance_previous+$Capital_Payment,
                                'Total_Pending_Balance' =>$Total_Pending_Balance_previous+$Interest_Payment+$Capital_Payment,
                            ]);


                        DB::table('customer_loan')
                            ->where('idCustomer_Loan', $loan_id)
                            ->update([
                                'installment_balance' =>$Interest_Balance_previous+$Interest_Payment,
                                'capital_balance' =>$Capital_Balance_previous+$Capital_Payment,
                                'Balance_Amount' =>$Total_Pending_Balance_previous+$Interest_Payment+$Capital_Payment,
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
        $keys = ['payment_member_name','loan_disbursement_policy','payment_backdate','loan_order','max_allowed_loans'];

        $rows = DB::table($this->table)
            ->whereIn('key', $keys)
            ->pluck('value', 'key');

        // return as { items: { payment_member_name: "full_name" } }
        return response()->json(['items' => $rows], 200);
    }

    // POST /settings/upsert
    public function upsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'   => ['required', 'in:payment_member_name,loan_disbursement_policy,payment_backdate,loan_order,max_allowed_loans'],
            'value' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->key === 'payment_member_name' &&
                        !in_array($value, ['full_name', 'with_initial', 'only_first_name', 'only_last_name'])) {
                        $fail('Invalid value for payment_member_name.');
                    }

                    if ($request->key === 'loan_disbursement_policy' &&
                        !in_array($value, ['strict', 'flexible'])) {
                        $fail('Invalid value for loan_disbursement_policy.');
                    }

                    if ($request->key === 'payment_backdate' &&
                        !in_array($value, ['enabled', 'disabled'])) {
                        $fail('Invalid value for payment_backdate.');
                    }

                    if ($request->key === 'loan_order' &&
                        !in_array($value, ['create_date', 'loan_number', 'issue_date'])) {
                        $fail('Invalid value for loan_order.');
                    }

                    if ($request->key === 'max_allowed_loans' &&
                        (!is_numeric($value) || $value < 1 || $value > 50)) {
                        $fail('Max allowed loans must be between 1 and 50.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $key = $request->input('key');
        $value = $request->input('value');
        $uid = auth()->id();

        $existing = DB::table($this->table)->where('key', $key)->first();

        if ($existing) {
            DB::table($this->table)
                ->where('key', $key)
                ->update([
                    'value'      => $value,
                    'updated_by' => $uid,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table($this->table)->insert([
                'key'        => $key,
                'value'      => $value,
                'created_by' => $uid,
                'updated_by' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('app_settings');
        return response()->json(['success' => true], 200);
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



}
