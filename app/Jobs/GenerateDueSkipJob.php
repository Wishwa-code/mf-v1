<?php

namespace App\Jobs;

use App\Http\Controllers\HolidayController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateDueSkipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $skipFor;
    public ?int   $targetId;
    public string $skipType;
    public int    $runId;

    public function __construct(string $skipFor, ?int $targetId, string $skipType, int $runId)
    {
        $this->skipFor  = $skipFor;
        $this->targetId = $targetId;
        $this->skipType = $skipType;
        $this->runId    = $runId;
    }

    public function handle(HolidayController $holidayController): void
    {
        // Update status → running
        DB::table('due_skip_runs')
            ->where('id', $this->runId)
            ->update([
                'status'     => 'running',
                'updated_at' => now(),
            ]);

        try {
            $holidayController->processDueSkipWithRun(
                $this->skipFor,
                $this->targetId,
                $this->skipType,
                $this->runId
            );

            DB::table('due_skip_runs')
                ->where('id', $this->runId)
                ->update([
                    'status'     => 'completed',
                    'updated_at' => now(),
                ]);

        } catch (\Throwable $e) {

            DB::table('due_skip_runs')
                ->where('id', $this->runId)
                ->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at'    => now(),
                ]);

            throw $e;
        }
    }
}
