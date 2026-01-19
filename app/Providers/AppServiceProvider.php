<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Manually load helper if composer autoload didn't pick it up
        $helperPath = app_path('Helpers/SessionHelper.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('app_settings')) {
                $settings = DB::table('app_settings')->pluck('value', 'key')->toArray();
                config(['app.settings' => $settings]);
                view()->share('appSettings', $settings);
            }

            // Auto create created_at column if not exists and copy Date_Time values
            if (Schema::hasTable('customer_loan') && !Schema::hasColumn('customer_loan', 'created_at')) {
                try {
                    DB::statement("ALTER TABLE customer_loan ADD COLUMN created_at DATETIME NULL AFTER Date_Time");
                    DB::statement("UPDATE customer_loan SET created_at = Date_Time WHERE Date_Time IS NOT NULL");
                } catch (\Exception $e) {
                    logger()->warning('Could not add customer_loan.created_at: ' . $e->getMessage());
                }
            }

            // Register Blade Directive for Privileges
            \Illuminate\Support\Facades\Blade::if('hasPrivilege', function ($expression) {
                $privileges = collect(user_data('privileges') ?? [])
                    ->pluck('Description')
                    ->map(fn($d) => strtoupper($d))
                    ->toArray();
                return in_array(strtoupper($expression), $privileges);
            });

            // Share branches with navbar for Admin users (branch_access == 1)
            view()->composer('layout.navbar', function ($view) {
                if (session('branch_access') === 1 && !isset($view->branch)) {
                    $branches = DB::table('branch')->get();
                    $view->with('branch', $branches);
                }
            });

            // Activity Log: Snapshot User Name
            \Spatie\Activitylog\Models\Activity::saving(function (\Spatie\Activitylog\Models\Activity $activity) {
                $user = auth()->user();
                // If there's an authenticated user and no "causer_name" property yet
                if ($user) {
                    $activity->properties = $activity->properties->merge([
                        'causer_name' => $user->Full_Name ?? 'Unknown User',
                        'ip' => request()->ip()
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Log and skip during deploy if DB is not ready
            logger()->warning("Skipping settings load: " . $e->getMessage());
        }
    }
}
