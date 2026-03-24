<?php

namespace App\Services;

use App\Models\User;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class GmailService
{
    /**
     * Build Gmail service instance for a specific user
     * Handles token decrypt + refresh automatically
     */
    public function forUser(User $user): Google_Service_Gmail
    {
        $client = new Google_Client();

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        try {
            // Decrypt stored tokens
            $accessToken = Crypt::decryptString($user->access_token);
            $refreshToken = $user->refresh_token
                ? Crypt::decryptString($user->refresh_token)
                : null;
        } catch (\Exception $e) {
            Log::error('Failed to decrypt Google tokens', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to decrypt Google tokens for user.');
        }

        $expiresIn = max(now()->diffInSeconds($user->expires_at), 0);
        $client->setAccessToken([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in'    => $expiresIn,
        ]);

        // Refresh token if expired
        if ($client->isAccessTokenExpired()) {
            if (! $refreshToken) {
                Log::warning('No refresh token available', ['user_id' => $user->id]);
                throw new \Exception('Google refresh token missing for user.');
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newToken['error'])) {
                Log::error('Google token refresh failed', [
                    'user_id' => $user->id,
                    'error'   => $newToken,
                ]);
                throw new \Exception('Google token refresh failed.');
            }

            // Update encrypted access token safely
            try {
                $user->update([
                    'access_token' => Crypt::encryptString($newToken['access_token']),
                    'expires_at'   => now()->addSeconds($newToken['expires_in']),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to save refreshed Google token', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                throw new \Exception('Failed to save refreshed Google token.');
            }

            $client->setAccessToken([
                'access_token'  => $newToken['access_token'],
                'refresh_token' => $refreshToken,
                'expires_in'    => $newToken['expires_in'],
            ]);
        }

        return new Google_Service_Gmail($client);
    }

    public function storeTokens(User $user, array $token): void
    {
        try {
            $data = [
                'access_token' => Crypt::encryptString($token['access_token']),
                'expires_at'   => now()->addSeconds($token['expires_in']),
            ];

            if (isset($token['refresh_token'])) {
                $data['refresh_token'] = Crypt::encryptString($token['refresh_token']);
            }

            $user->update($data);

        } catch (\Exception $e) {
            Log::error('Failed to store Google tokens', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            throw new \Exception('Failed to store Google authentication tokens.');
        }
    }

}
