<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApplyBranchFromUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // auth:sanctum user
        if ($user) {
            // Make branch available everywhere
            $request->attributes->set('branch_id', $user->branch_id);

            // Optional: if your old helpers depend on session()
            // this keeps them working even on token calls
            session(['branch_id' => $user->branch_id]);
        }

        return $next($request);
    }
}
