<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next)
    {
        // Read tenant DB name from session
        $tenantDb = $request->session()->get('tenant_db');

        if ($tenantDb) {

            // 1) Point the tenant connection to the correct database
            Config::set('database.connections.tenant.database', $tenantDb);

            // 2) Purge old tenant connection (if any)
            DB::purge('tenant');

            // 3) Set tenant as DEFAULT connection for this request
            DB::setDefaultConnection('tenant');

            // 4) Reconnect tenant with new DB name
            DB::reconnect('tenant');

            // 5) Make sure Auth still uses web guard
            Auth::shouldUse('web');
        }

        return $next($request);
    }
}
