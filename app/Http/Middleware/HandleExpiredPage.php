<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleExpiredPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() == 419) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        return $response;
    }
}
