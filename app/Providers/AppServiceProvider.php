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
        // Only try if table exists (avoids errors during first migrate)
        if (Schema::hasTable('app_settings')) {
            // Cache for 5 minutes (adjust if you want)
            $settings = Cache::remember('app_settings', 300, function () {
                return DB::table('app_settings')->pluck('value', 'key')->toArray();
            });

            // Now available everywhere
            config(['app.settings' => $settings]);   // use: config('app.settings.key')
            view()->share('appSettings', $settings); // Blade: $appSettings['key']
            // Optional: session if you want it
            // session(['app_settings' => $settings]);
        }
    }
}
