<?php

use App\Models\EmailCache;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\GmailController;
//use App\Http\Controllers\DetectionController; 

// Landing Page
Route::get('/', fn() => view('welcome'))->name('welcome');

// Shortcut login
Route::get('/login', fn() => redirect()->route('auth.google'))->name('login');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Protected GET routes - require login + Google token
Route::middleware(['auth', 'ensure.google',\App\Http\Middleware\ClearEmailCache::class])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/userprofile', fn() => view('userprofile'))->name('userprofile');
    //Route::get('/status', fn() => view('status'))->name('status');
    Route::get('/result', fn() => view('result'))->name('result');
    Route::get('/discussion', fn() => view('discussion'))->name('discussion');
    Route::get('/help', fn() => view('help'))->name('help');
    
    /* Emails
    Route::get('/emails/view/{messageId}', [GmailController::class, 'view'])->name('emails.view');
    Route::post('/emails/scan/{messageId}', [GmailController::class, 'scanEmail'])->name('emails.scan');
    */
    
    // Detection results
    Route::get('/detection/{id}', [DetectionController::class, 'showResult'])->name('detection.result');
    // Delete a detection result
    Route::post('/detection/delete/{id}', function($id) {
        $result = \App\Models\DetectionResult::find($id);
        if ($result) {
            $result->delete();
            return redirect()->route('result')->with('success', 'Detection result deleted.');
        }
        return redirect()->route('result')->with('error', 'Result not found.');
    })->name('detection.delete');

});

// Protected POST routes - require login + Google token
Route::middleware('ensure.google')->group(function () {
    Route::post('/detect', [DetectionController::class, 'detectAll'])->name('detect.all');

    Route::post('/logout', function () {
        $user = Auth::user();
        if ($user) {
            EmailCache::where('user_id', $user->id)->delete();
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }
        return redirect()->route('welcome');
    })->name('logout');
});
