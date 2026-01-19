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
        if (!has_privilege($privilege)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
