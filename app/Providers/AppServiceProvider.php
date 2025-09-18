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
        //
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

            // Auto create created_at column if not exists (leave existing rows NULL)
            if (Schema::hasTable('customer_loan') && !Schema::hasColumn('customer_loan', 'created_at')) {
                try {
                    DB::statement("ALTER TABLE customer_loan ADD COLUMN created_at DATETIME NULL AFTER Date_Time");
                } catch (\Exception $e) {
                    logger()->warning('Could not add customer_loan.created_at: '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log and skip during deploy if DB is not ready
            logger()->warning("Skipping settings load: " . $e->getMessage());
        }
    }
}
