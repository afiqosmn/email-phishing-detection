<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\DetectionResultController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\AdminController;
use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', fn () => view('welcome'))->name('welcome');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/service', fn () => view('service'))->name('service');
Route::get('/contact', fn () => view('contact'))->name('contact');

// Get started page - for end-users
Route::get('/login', fn () => view('/login'))->name('login-page');

// Login route - redirects to Google OAuth
Route::get('/Oauth', fn () => redirect()->route('auth.google'))->name('login');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

/* Protected GET routes */

Route::middleware(['auth', 'ensure.google'])->group(function () {

    Route::get('/dashboard', fn () => view('/end-user/dashboard'))->name('dashboard');
    Route::get('/userprofile', fn () => view('/end-user/userprofile'))->name('userprofile');
    Route::get('/discussion', fn () => view('/end-user/discussion'))->name('discussion');
    Route::get('/help', fn () => view('/end-user/help'))->name('help');

    // Email status (fetched + scanned)
    Route::get('/status', function () {
        $emails = Email::where('user_id', auth()->id())
            ->orderByDesc('date')
            ->paginate(150);

        return view('/end-user/status', compact('emails'));
    })->name('status');

    // Detection results
    Route::get('/result', [DetectionResultController::class, 'index'])
        ->name('result');

    Route::get('/detection/{id}', [DetectionResultController::class, 'show'])
        ->name('detection.result');

    Route::get('/detection/{id}/download-pdf', [DetectionResultController::class, 'downloadPdf'])
        ->name('detection.download-pdf');

    Route::post('/detection/delete/{id}', [DetectionResultController::class, 'destroy'])
        ->name('detection.delete');

    // User Reports
    Route::get('/reports', [UserReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/create/{email}', [UserReportController::class, 'create'])
        ->name('reports.create');

    Route::get('/reports/{id}', [UserReportController::class, 'show'])
        ->name('reports.show');
});

/* Admin routes (protected with IsAdmin middleware) */

Route::post('/admin-login', fn () => view('/admin/login'))->name('admin.login');

    // Logout route - only requires auth, not ensure.google
    // (token may be expired when user wants to logout)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('welcome');
})->name('logout');

/* Protected POST routes (ensure.google) */

Route::middleware(['auth', 'ensure.google'])->group(function () {

    Route::post('/reports', [UserReportController::class, 'store'])
        ->name('reports.store');

    Route::post('/reports/{id}/delete', [UserReportController::class, 'destroy'])
        ->name('reports.delete');

});