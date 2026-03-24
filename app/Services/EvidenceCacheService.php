<?php

namespace App\Services;

use App\Models\DetectionResult;
use App\Models\UrlEvidence;
use App\Models\AuthenticationEvidence;
use App\Models\KeywordEvidence;
use App\Models\HtmlAnomalyEvidence;
use Illuminate\Support\Facades\Cache;

class EvidenceCacheService
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'evidence:';
    
    /**
     * Cache duration in minutes (1 hour)
     */
    private const CACHE_TTL = 60;

    /**
     * Get all evidence for a detection result with caching
     */
    public function getEvidenceForDetectionResult(int $detectionResultId): array
    {
        $cacheKey = $this->getCacheKey('full', $detectionResultId);

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($detectionResultId) {
            return [
                'url_evidences' => UrlEvidence::where('detection_result_id', $detectionResultId)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->toArray(),
                'authentication_evidences' => AuthenticationEvidence::where('detection_result_id', $detectionResultId)
                    ->orderBy('check_type')
                    ->get()
                    ->toArray(),
                'keyword_evidences' => KeywordEvidence::where('detection_result_id', $detectionResultId)
                    ->orderBy('category')
                    ->get()
                    ->toArray(),
                'html_anomaly_evidences' => HtmlAnomalyEvidence::where('detection_result_id', $detectionResultId)
                    ->orderBy('severity', 'desc')
                    ->get()
                    ->toArray(),
            ];
        });
    }

    /**
     * Get evidence by classification with pagination and caching
     */
    public function getEvidenceByClassification(int $detectionResultId, string $classification, int $perPage = 20): array
    {
        $cacheKey = $this->getCacheKey('classification', $detectionResultId, $classification);

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($detectionResultId, $classification, $perPage) {
            return [
                'url_evidences' => UrlEvidence::where('detection_result_id', $detectionResultId)
                    ->where('classification', $classification)
                    ->paginate($perPage)
                    ->toArray(),
                'authentication_evidences' => AuthenticationEvidence::where('detection_result_id', $detectionResultId)
                    ->where('classification', $classification)
                    ->paginate($perPage)
                    ->toArray(),
                'keyword_evidences' => KeywordEvidence::where('detection_result_id', $detectionResultId)
                    ->where('classification', $classification)
                    ->paginate($perPage)
                    ->toArray(),
                'html_anomaly_evidences' => HtmlAnomalyEvidence::where('detection_result_id', $detectionResultId)
                    ->where('classification', $classification)
                    ->paginate($perPage)
                    ->toArray(),
            ];
        });
    }

    /**
     * Get evidence summary counts
     */
    public function getEvidenceSummary(int $detectionResultId): array
    {
        $cacheKey = $this->getCacheKey('summary', $detectionResultId);

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($detectionResultId) {
            return [
                'phishing' => [
                    'url_count' => UrlEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'phishing')
                        ->count(),
                    'auth_count' => AuthenticationEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'phishing')
                        ->count(),
                    'keyword_count' => KeywordEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'phishing')
                        ->count(),
                    'html_count' => HtmlAnomalyEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'phishing')
                        ->count(),
                ],
                'legitimate' => [
                    'url_count' => UrlEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'legitimate')
                        ->count(),
                    'auth_count' => AuthenticationEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'legitimate')
                        ->count(),
                    'keyword_count' => KeywordEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'legitimate')
                        ->count(),
                    'html_count' => HtmlAnomalyEvidence::where('detection_result_id', $detectionResultId)
                        ->where('classification', 'legitimate')
                        ->count(),
                ],
            ];
        });
    }

    /**
     * Clear cache for a detection result (call after updating evidence)
     */
    public function clearCache(int $detectionResultId): void
    {
        Cache::forget($this->getCacheKey('full', $detectionResultId));
        Cache::forget($this->getCacheKey('classification', $detectionResultId, 'phishing'));
        Cache::forget($this->getCacheKey('classification', $detectionResultId, 'legitimate'));
        Cache::forget($this->getCacheKey('summary', $detectionResultId));
    }

    /**
     * Generate cache key
     */
    private function getCacheKey(string $type, int $detectionResultId, ?string $classification = null): string
    {
        $key = self::CACHE_PREFIX . $type . ':' . $detectionResultId;
        
        if ($classification) {
            $key .= ':' . $classification;
        }
        
        return $key;
    }
}
