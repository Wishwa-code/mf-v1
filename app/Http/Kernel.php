<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \App\Http\Middleware\HandleExpiredPage::class,
        // \App\Http\Middleware\AutoLogout::class,
    ];


    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // 👇 MUST be here after StartSession
            \App\Http\Middleware\SetTenantConnection::class,


            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \App\Http\Middleware\HandleExpiredPage::class,
        ],


        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];



    protected $routeMiddleware = [
        'auth'            => \App\Http\Middleware\Authenticate::class,
        'auth.basic'      => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers'   => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'             => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'          => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'        => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'        => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'branch.from.user' => \App\Http\Middleware\ApplyBranchFromUser::class,
        //        'tenant' => \App\Http\Middleware\SetTenantConnection::class,
        'auth.central' => \App\Http\Middleware\VerifyAuthToken::class
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->call(function () {
            \App\Jobs\RunRecoverySweepJob::dispatch(1, 1);
        })
            ->dailyAt('02:00')
            ->timezone('Asia/Colombo')
            ->name('auto_recovery_sweep')
            ->withoutOverlapping();
    }
}
