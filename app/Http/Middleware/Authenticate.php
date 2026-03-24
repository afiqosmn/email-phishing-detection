<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Redirect users if not authenticated.
     */
    protected function redirectTo(Request $request)
    {
        if (! $request->expectsJson()) {
            return route('welcome'); // redirect default kalau tak login
        }

        return null; // wajib return null kalau expect JSON
    }
}
