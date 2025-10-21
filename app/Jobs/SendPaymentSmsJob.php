<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendPaymentSmsJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 5;

    public function backoff(): array { return [5, 15, 45, 90, 180]; }

    public function uniqueId(): string
    {
        return 'payment-sms-' . $this->paymentId;
    }

    public function middleware(): array
    {
        return [ new RateLimited('sms') ];
    }

    public function __construct(
        public int $paymentId,
        public int $loanId,
        public int $customerId,
        public string $message
    ) {
        $this->onQueue('sms');
    }

    public function handle(SmsService $sms): void
    {
        // Note: sessions are usually not available in queued jobs. Keep robust.
        $branchId = (int) (session('branch_id') ?? 0);

        try {
            $company  = DB::table('company')->where('branch_id', $branchId)->first();
            $customer = DB::table('customer')->where('idCustomer', $this->customerId)->first();

            if (!$company || !$customer) {
                Log::info('[SMS][Job] Skip: missing company/customer', [
                    'payment_id' => $this->paymentId,
                    'branch_id'  => $branchId,
                    'has_company'=> (bool) $company,
                    'has_customer'=>(bool) $customer,
                ]);
                return; // ALWAYS continue without failing the job
            }

            $provider = $company->provider ?? config('sms.provider');
            $mask     = $company->mask ?? config('sms.mask');
            $contact  = (string) ($customer->Contact_No ?? '');

            // Insert an SMS row (pending / neutral)
            $smsId = DB::table('sms')->insertGetId([
                'cus_id'     => $this->customerId,
                'cus_name'   => trim(($customer->First_Name ?? '') . ' ' . ($customer->Last_Name ?? '')),
                'contact_no' => $contact,
                'message'    => $this->message,
                'type'       => 'Customer Loan Payment',
                'date'       => now()->toDateString(),
                'time'       => now()->toTimeString(),
                'branch_id'  => $branchId,
            ]);

            Log::info('[SMS][Job] Prepared record & sending', [
                'sms_id'     => $smsId,
                'payment_id' => $this->paymentId,
                'loan_id'    => $this->loanId,
                'customer_id'=> $this->customerId,
                'provider'   => $provider,
                'mask'       => $mask,
                'contact'    => $contact,
            ]);

            $ok        = false;
            $respArray = null;

            // Wrap the whole provider call so any exception from SmsService is swallowed here.
            try {
                $resp = $sms->send($provider, $mask, $contact, $this->message);

                // Normalize response into array for logging/storing
                if (is_array($resp)) {
                    $respArray = $resp;
                } elseif (is_object($resp)) {
                    $respArray = json_decode(json_encode($resp), true);
                } elseif (is_string($resp)) {
                    $respArray = ['raw' => $resp];
                } else {
                    $respArray = ['unknown' => $resp];
                }

                // Provider-agnostic success check (keep your original logic)
                $ok = ($provider === 'Dialog')
                    ? (($respArray['status'] ?? null) === 'success')
                    : (($respArray['serverRef'] ?? null) !== null);

                Log::info('[SMS][Job] Provider response parsed', [
                    'sms_id'   => $smsId,
                    'ok'       => $ok,
                    'response' => $respArray,
                ]);
            } catch (\Throwable $e) {
                // IMPORTANT: swallow the exception – do NOT rethrow
                $respArray = ['error' => $e->getMessage(), 'class' => get_class($e)];
                $ok = false;
                Log::info('[SMS][Job] Provider call threw, swallowed for continuity', [
                    'sms_id'     => $smsId,
                    'payment_id' => $this->paymentId,
                    'err'        => $e->getMessage(),
                    'trace_at'   => $e->getFile() . ':' . $e->getLine(),
                ]);
            }

            // Persist final status; never throw
            DB::table('sms')->where('id', $smsId)->update([
                'status'            => $ok ? 'sent' : 'failed',
                'provider_response' => json_encode($respArray),
                'updated_at'        => now(),
            ]);

            Log::info('[SMS][Job] Finalized', [
                'sms_id'     => $smsId,
                'status'     => $ok ? 'sent' : 'failed',
                'payment_id' => $this->paymentId,
            ]);

            // Always exit normally (no exceptions)
            return;

        } catch (\Throwable $e) {
            // ABSOLUTE last-resort catch – still do not rethrow
            Log::info('[SMS][Job] Unhandled exception swallowed', [
                'payment_id' => $this->paymentId,
                'err'        => $e->getMessage(),
                'trace_at'   => $e->getFile() . ':' . $e->getLine(),
            ]);
            // No rethrow: job completes without breaking frontend/flow
        }
    }
}
