<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureGoogleAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('auth.google')
                ->withErrors(['msg' => 'Please login first.']);
        }

        if (! optional($user)->access_token || ! optional($user)->refresh_token) {
            Auth::logout();
            return redirect()->route('auth.google')
                ->withErrors(['msg' => 'Google authentication required.']);
        }

        if ($this->isTokenExpired($user)) {
            Auth::logout();
            return redirect()->route('auth.google')
                ->withErrors(['msg' => 'Session expired, please login again.']);
        }

        return $next($request);
    }

    /**
     * Check if the access token is expired
     */
    private function isTokenExpired($user): bool
    {
        if (! $user->expires_at) {
            return true;
        }

        return now()->greaterThan($user->expires_at);
    }
}
