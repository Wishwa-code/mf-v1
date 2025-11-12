<?php

namespace App\Jobs;

use App\Services\RecoveryAutoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunRecoverySweepJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $branchId;
    protected int $userId;

    public function __construct(int $branchId, int $userId)
    {
        $this->branchId = $branchId;
        $this->userId   = $userId;
        $this->onQueue('recovery');
    }

    public function handle(\App\Services\RecoveryAutoService $service): void
    {
        $service->runDailyRecoverySweep($this->branchId, $this->userId);
    }
}

