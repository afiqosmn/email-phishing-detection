<?php

namespace App\Http\Controllers;

use App\Jobs\InitialGmailSyncJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Oauth2;
use App\Models\User;
use App\Services\GmailService;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $client->addScope(Google_Service_Gmail::GMAIL_READONLY);
        $client->addScope('email');
        $client->addScope('profile');

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect()->away($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request, GmailService $gmailService) 
    {
        $client = new Google_Client();

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $client->addScope(Google_Service_Gmail::GMAIL_READONLY);
        $client->addScope('email');
        $client->addScope('profile');

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        if (isset($token['error'])) {
            return redirect('/')
                ->withErrors(['msg' => 'Google authentication failed']);
        }

        $client->setAccessToken($token);

        $oauth = new Google_Service_Oauth2($client);
        $googleUser = $oauth->userinfo->get();

        // Create / update user WITHOUT token logic
        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name'       => $googleUser->name,
                'email'      => $googleUser->email,
                'avatar_url' => $googleUser->picture,
            ]
        );

        // Delegate token storage to service
        $gmailService->storeTokens($user, $token);

        Auth::login($user);

        // Dispatch initial sync later
        InitialGmailSyncJob::dispatch($user->id)->onQueue('gmail');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
