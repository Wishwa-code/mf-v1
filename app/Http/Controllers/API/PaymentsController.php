<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TodayPaymentController; // <-- adjust if different
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentsController extends Controller
{

    protected $smsLogController;

    public function __construct(SmsController $smsLogController)
    {
        $this->smsLogController = $smsLogController;

    }

    /**
     * POST /api/loans/{id}/payments
     * Body (JSON):
     * {
     *   "payment_amount": 1000.00,           // required
     *   "saving_amount": 0,                  // optional, default 0
     *   "payment_date": "2025-09-26",        // optional, default today (Asia/Colombo)
     *   "payment_type": "Cash",              // optional, default "Cash"  (Cash|Cheque|Transfer etc.)
     *   "bank_account_company": "1",         // optional
     *   "cheque_issue_bank": "1",            // optional
     *   "name_on_cheque": "",                // optional
     *   "chq_number": "",                    // optional
     *   "chq_date": "",                      // optional (YYYY-MM-DD)
     *   "chq_type": "Crossed"                // optional, default "Crossed"
     * }
     */
    public function storeLoanPayment(Request $request, int $id)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();

        // Validate incoming JSON
        $request->validate([
            'payment_amount'       => 'required|numeric|min:0.01',
            'saving_amount'        => 'nullable|numeric|min:0',
            'payment_date'         => 'nullable|date_format:Y-m-d',
            'payment_type'         => 'nullable|string|max:50',
            'bank_account_company' => 'nullable|string|max:50',
            'cheque_issue_bank'    => 'nullable|string|max:50',
            'name_on_cheque'       => 'nullable|string|max:150',
            'chq_number'           => 'nullable|string|max:100',
            'chq_date'             => 'nullable|date_format:Y-m-d',
            'chq_type'             => 'nullable|string|max:50',
        ]);

        // Ensure the loan exists and belongs to this branch
        $loan = DB::table('customer_loan')
            ->where('branch_id', $branchId)
            ->where('idCustomer_Loan', $id)
            ->first();

        if (!$loan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Loan not found in your branch',
            ], 404);
        }

        // Defaults
        $amount        = (float) $request->input('payment_amount');
        $saving_amount = (float) $request->input('saving_amount', 0);

        $payment_type  = $request->input('payment_type', 'Cash');

        // Build the exact structure your existing store() expects
        $paymentData = [
            'cus_id'               => $loan->Customer_idCustomer,
            'payment_amount'       => $amount,
            'saving_amount'        => $saving_amount,
            'file'                 => '-', // keep consistent with your current flow
            'loan_id'              => $loan->idCustomer_Loan,
            'payment_date'         => Carbon::today(config('app.timezone', 'Asia/Colombo'))->toDateString(),
            'payment_type'         => $payment_type,
            'bank_account_company' => (string) $request->input('bank_account_company', '1'),
            'cheque_issue_bank'    => (string) $request->input('cheque_issue_bank', '1'),
            'name_on_cheque'       => (string) $request->input('name_on_cheque', ''),
            'chq_number'           => (string) $request->input('chq_number', ''),
            'chq_date'             => (string) $request->input('chq_date', ''),
            'chq_type'             => (string) $request->input('chq_type', 'Crossed'),
        ];

        // Legacy compatibility: many of your controllers read session('userid')
        // Ensure it's set so TodayPaymentController::store() can use it.
        session([
            'userid'      => (int) $user->id,
            'branch_id'   => $branchId,              // already set by ApplyBranchFromUser, but safe
            'designation' => $user->Designation ?? null, // optional if used downstream
        ]);

        // Call your existing web controller logic
        try {
            /** @var \App\Http\Controllers\TodayPaymentController $paymentController */
            $paymentController = app(TodayPaymentController::class);

            // Create a synthetic Request carrying the expected payload
            $forward = new Request($paymentData);

            $result = $paymentController->store($forward);

            // If your store() already returns JSON, just pass it through
            if ($result instanceof \Illuminate\Http\JsonResponse) {
                return $result;
            }

            // Otherwise, standardize a success payload
            return response()->json([
                'status'  => 'success',
                'message' => 'Payment recorded',
                'data'    => [
                    'loan_id'        => $loan->idCustomer_Loan,
                    'cus_id'         => $loan->Customer_idCustomer,
                    'payment_amount' => round($amount, 2),
                    'saving_amount'  => round($saving_amount, 2),
                    'payment_date'   => date('Y-m-d'),
                    'payment_type'   => $payment_type,
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('API payment error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to record payment',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function paymentReceipt($id)
    {
        try {
            $status = '1'; // always 1

            $customer_payment = DB::table('customer_payments')
                ->where('idCustomer_Payments', $id)
                ->first();

            if (!$customer_payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $user = DB::table('user')->where('id', $customer_payment->User_idUser)->first();
            $loan = DB::table('customer_loan')
                ->where('idCustomer_Loan', $customer_payment->Customer_Loan_idCustomer_Loan)
                ->first();

            if (!$loan) {
                return response()->json(['message' => 'Loan not found'], 404);
            }

            $customer = DB::table('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();

            $cheque_details = null;
            if ($customer_payment->Payment_type === "Cheque") {
                $cheque_details = DB::table('cheque_details')
                    ->where('Payment_id', $id)
                    ->first();
            }

            $company = DB::table('company')->first();

            // ---------- build logo URL ----------
            $logo_url = null;
            if ($company && !empty($company->logo)) {
                // if the logo already contains 'http' return as-is
                if (filter_var($company->logo, FILTER_VALIDATE_URL)) {
                    $logo_url = $company->logo;
                } else {
                    // ensure we don't duplicate 'storage/' if it's already present in the column
                    $path = $company->logo;
                    if (strpos($path, 'storage/') === 0) {
                        // path already begins with storage/
                        $logo_url = asset($path);
                    } else {
                        // most common case: column holds 'Company/xxx.png' -> public URL is /storage/Company/xxx.png
                        $logo_url = asset('storage/' . ltrim($path, '/'));
                    }
                }
            }
            // ------------------------------------

            $points_message = "";
            $points_to_add = 0;

            if ($company && $company->points === "1") {
                $points_percentage = (float) $company->points_percentage;
                $points_to_add = ($customer_payment->Amount * $points_percentage) / 100;
                $current_points = (float) ($customer->points ?? 0);
                $points_message = "\n\nCongratulations!\nYou have earned " . number_format($points_to_add, 2) .
                    " points for this transaction. Your total loyalty points are now " . number_format($current_points + $points_to_add, 2) . ".";
            }

            $sms_template = DB::table('sms_template')
                ->where('type', 'loan_payment')
                ->where('status', '1')
                ->first();

            if ($sms_template) {
                $arrears = (float) DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $customer_payment->Customer_Loan_idCustomer_Loan)
                    ->where('Status', '0')
                    ->where('Installment_Date', '<', date('Y-m-d'))
                    ->sum('Total_Balance');

                $placeholders = [
                    '@Member_No@'       => $customer->cus_number ?? '',
                    '@Member_Name@'     => trim(($customer->First_Name ?? '') . ' ' . ($customer->Last_Name ?? '')),
                    '@Loan_No@'         => $loan->Loan_No ?? '',
                    '@Payment_Date@'    => $customer_payment->Date ?? '',
                    '@Paid_Amount@'     => number_format((float)$customer_payment->Amount, 2, '.', ','),
                    '@Loan_Balance@'    => number_format((float)$loan->Balance_Amount, 2, '.', ','),
                    '@Capital_Balance@' => number_format((float)$loan->capital_balance, 2, '.', ','),
                    '@Pending_Total@'   => number_format($arrears, 2, '.', ','),
                ];

                $loan_number_txt = str_replace(array_keys($placeholders), array_values($placeholders), $sms_template->template);
                $loan_number_txt .= $points_message;

                // status is always 1, so always log SMS
                $this->smsLogController->index($loan->Customer_idCustomer, $loan_number_txt, "Customer Loan Payment");
            }

            $panelty_balance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $customer_payment->Customer_Loan_idCustomer_Loan)
                ->sum('Panalty_Balance');

            $tot_balance = (float) DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $customer_payment->Customer_Loan_idCustomer_Loan)
                ->sum('Total_Balance');

            $loan_category = tableWithBranch('loan_category')
                ->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)
                ->first();

            $saving_amount = "0.00";
            $saving_on = 0;

            if ($loan_category && $loan_category->saving_payment == "1") {
                $saving_on = 1;
                $last_log = DB::table('Loan_Log')
                    ->where('Type_ID', $id)
                    ->orderBy('Loan_Log_ID', 'desc')
                    ->first();

                if ($last_log && isset($last_log->Savings_Payment)) {
                    $saving_amount = number_format((float)$last_log->Savings_Payment, 2, '.', ',');
                }
            }

            return response()->json([
                'saving_amount'   => $saving_amount,
                'saving_on'       => $saving_on,
                'tot_balance'     => $tot_balance,
                'panelty_balance' => $panelty_balance,
                'payment'         => $customer_payment,
                'cheque_details'  => $cheque_details,
                'loan'            => $loan,
                'customer'        => $customer,
                'user'            => $user,
                'points_to_add'   => $points_to_add,
                'point_check'     => $company->points ?? '0',
                'logo_url'        => $logo_url, // <-- added here
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }


}
