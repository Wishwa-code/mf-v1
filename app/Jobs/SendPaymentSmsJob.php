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
        return 'payment-sms-'.$this->paymentId; // avoid duplicate sends per payment
    }

    public function middleware(): array
    {
        // additionally protect provider with a global rate-limit bucket named "sms"
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

    public function handle(SmsService $sms)
    {
        $company  = DB::table('company')->where('branch_id', session('branch_id'))->first();
        $customer = DB::table('customer')->where('idCustomer', $this->customerId)->first();

        if (!$company || !$customer) {
            Log::warning('SMS skip: missing company/customer', ['payment_id'=>$this->paymentId]);
            return;
        }

        $provider = $company->provider ?? config('sms.provider');
        $mask     = $company->mask ?? config('sms.mask');

        // Insert a row up-front with "pending"
        $smsId = DB::table('sms')->insertGetId([
            'cus_id'     => $this->customerId,
            'cus_name'   => ($customer->First_Name.' '.$customer->Last_Name),
            'contact_no' => $customer->Contact_No,
            'message'    => $this->message,
            'type'       => 'Customer Loan Payment',
            'date'       => now()->toDateString(),
            'time'       => now()->toTimeString(),
            'branch_id'  => session('branch_id'),
        ]);

        try {
            $resp = $sms->send($provider, $mask, $customer->Contact_No, $this->message);

            $ok = ($provider === 'Dialog')
                ? (($resp['status'] ?? null) === 'success')
                : (($resp['serverRef'] ?? null) !== null);



            if (!$ok) {
                // Throw to trigger retry
                throw new \RuntimeException('Provider response indicates failure');
            }
        } catch (\Throwable $e) {
            Log::warning('SMS send failed', ['payment_id'=>$this->paymentId, 'err'=>$e->getMessage()]);
            DB::table('sms')->where('id', $smsId)->update([
                'status' => 'failed',
                'provider_response' => json_encode(['error'=>$e->getMessage()]),
                'updated_at' => now(),
            ]);
            throw $e;
        }
    }
}
