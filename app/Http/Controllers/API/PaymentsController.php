<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TodayPaymentController; // <-- adjust if different
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentsController extends Controller
{
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
                    'payment_date'   => $date,
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
}
