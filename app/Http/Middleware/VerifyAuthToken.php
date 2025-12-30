<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user info is in session (set by AccountCenterAutoLoginController)
        if (session()->has('auth_user')) {
            $user = session('auth_user');
            // Store user in request globally
            $request->attributes->set('auth_user', $user);
        } else {
            return redirect('https://accountcenter.asipbook.com/')
                ->with('error', 'Unauthorized. Please login.');
        }

        return $next($request);
    }
}
