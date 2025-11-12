<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Jobs\RunRecoverySweepJob;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register your command here
        Commands\SendSmsBirthday::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->call(function () {
            // Here we manually dispatch the job
            $branchId = 1; // or get from your main branch setup
            $userId   = 1; // system user ID (for logs/audit)

            Log::info('Auto Recovery Scheduler Triggered', [
                'time' => now()->toDateTimeString(),
                'branch_id' => $branchId,
                'user_id' => $userId,
            ]);

            RunRecoverySweepJob::dispatch($branchId, $userId);
        })
            ->dailyAt('02:00')          // run at 2:00 AM
            ->timezone('Asia/Colombo')  // Sri Lankan time zone
            ->name('auto_recovery_sweep')
            ->withoutOverlapping();     // ensures it won't overlap if still running
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
