<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected $levels = [];
    protected $dontReport = [];
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        // Handling 419 CSRF token expired
        $this->renderable(function (TokenMismatchException $e, $request) {
            return redirect()->route('welcome')
                ->with('error', 'Session expired. Please login again.');
        });

        // Handling unauthenticated access
        $this->renderable(function (AuthenticationException $e, $request) {
            return redirect()->route('auth.google')
                ->with('error', 'Please login to continue.');
        });
    }
}
