<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MLDetectionService
{
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.ml.endpoint', 'http://127.0.0.1:5001/detect');
    }

    /**
     * Call external ML API (Flask)
     */
    public function predict(array $emailData): array
    {
        try {
            $emailRaw =
                $emailData['raw']
                ?? $emailData['body']
                ?? '';

            if (empty($emailRaw)) {
                return $this->fallback();
            }

            $response = Http::timeout(5)->post($this->endpoint, [
                'email_raw' => $emailRaw,
            ]);

            if (! $response->successful()) {
                Log::warning('ML API failed', [
                    'status' => $response->status(),
                ]);
                return $this->fallback();
            }

            $data = $response->json();

            return [
                'ml_result'     => $data['result'] ?? 'unknown',
                'ml_confidence' => $data['confidence'] ?? 0.0,
                'risk_score'    => $data['risk_score'] ?? null,
                'model_used'    => $data['model_used'] ?? null,
                'method'        => $data['method'] ?? 'ml_model',
            ];
        } catch (\Throwable $e) {
            Log::error('MLDetectionService error', [
                'error' => $e->getMessage(),
            ]);
            return $this->fallback();
        }
    }

    private function fallback(): array
    {
        return [
            'ml_result'     => 'unavailable',
            'ml_confidence' => 0.0,
            'method'        => 'fallback',
        ];
    }
}
