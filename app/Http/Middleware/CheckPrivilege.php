<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $privilege
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $privilege)
    {
        $userPrivileges = collect(user_data('privileges') ?? [])
            ->pluck('Description')
            ->map(function ($d) {
                return strtoupper($d);
            })
            ->toArray();

        if (!in_array(strtoupper($privilege), $userPrivileges)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
