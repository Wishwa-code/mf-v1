<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // If it’s not an AJAX/JSON request, send to login
        if (! $request->expectsJson()) {
            return route('login'); // you already have a named route('login')
        }
    }
}
