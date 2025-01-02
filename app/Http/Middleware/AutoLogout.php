<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');
            $now = now();

            // If the last activity time is not set, initialize it
            if (!$lastActivity) {
                session(['last_activity_time' => $now]);
            } elseif ($now->diffInMinutes($lastActivity) >= 60) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('message', 'You have been logged out due to inactivity.');
            }

            // Update the last activity time
            session(['last_activity_time' => $now]);
        }

        return $next($request);
    }
}
