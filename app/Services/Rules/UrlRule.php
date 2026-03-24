<?php

namespace App\Services\Rules;

use App\Models\UrlCache;
use Illuminate\Support\Facades\Http;

class UrlRule
{
    protected $apiKey;
    protected $weight = 0.3;

    public function __construct()
    {
        //dd('UrlRule constructor called');
        $this->apiKey = config('services.google_safe_browsing.key');
    }

    /**
     * MAIN METHOD: Evaluate URLs and return standardized result
     * This will be called by RuleManager
     */
    public function evaluate(array $email): array
    {
        //dd('UrlRule evaluate called', $email);
        $body = $email['body'] ?? '';
        $urls = $this->extractUrls($body);
        //dd('Extracted URLs', $urls);
        
        //No URLs found
        if (empty($urls)) {
            return [
                'score' => 0.0,
                'details' => [],
                'weight' => $this->weight,
            ];
        }
        
        $maliciousCount = 0;
        $details = [];
        $loopCount = 0;

        // Loop for each URL and check
        foreach ($urls as $url) {
            $checkResult = $this->checkUrl($url);
            $details[] = [
                'url' => $url,
                'status' => $checkResult['status'],
                'threatTypes' => $checkResult['threatTypes'],
                'explanation' => $checkResult['explanation'],
                'source' => $checkResult['source']
            ];
            $loopCount++;
            
            if ($checkResult['status'] === 'malicious') {
                $maliciousCount++;
            }
        }
        //dd($details, 'URL check details after looping through URLs', $maliciousCount, $loopCount);
        
        
        $score = $this->calculateScore($urls, $maliciousCount);
        //dd('URL evaluation complete', $score, $details);
        
        return [
            'score' => $score,
            'details' => $details,
            'weight' => $this->weight,
        ];
    }
    
    /**
     * Calculate score based on URL analysis
     */
    private function calculateScore(array $urls, int $maliciousCount): float
    {
        if ($maliciousCount > 0) {
            return 1.0;  // High confidence phishing if any malicious URL
        }
        
        if (count($urls) >= 3) {
            return 0.3;  // Moderate suspicion for many URLs
        }
        
        if (count($urls) === 2) {
            return 0.1;  // Low suspicion for 2 URLs
        }
        
        return 0.0;  // Single safe URL is normal
    }
    
    /**
     * Function 3: Check single URL via Google Safe Browsing API
     */
    public function checkUrl($url)
    {
        // 1️⃣ Check cached result first using url_hash
        $urlHash = hash('sha256', $url);
        $cached = UrlCache::where('url_hash', $urlHash)->first();
        if ($cached) {
            return [
                'status' => $cached->status,
                'threatTypes' => $cached->threat_types ?? [],
                'explanation' => $cached->explanation ?? 'From cache',
                'source' => 'cache'
            ];
        }
        
        // 2️⃣ Call Google Safe Browsing API
        $response = Http::post(
            "https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$this->apiKey}",
            [
                "client" => [
                    "clientId" => "your-app",
                    "clientVersion" => "1.0"
                ],
                "threatInfo" => [
                    "threatTypes" => ["MALWARE", "SOCIAL_ENGINEERING","UNWANTED_SOFTWARE","POTENTIALLY_HARMFUL_APPLICATION"],
                    "platformTypes" => ["ANY_PLATFORM"],
                    "threatEntryTypes" => ["URL"],
                    "threatEntries" => [
                        ["url" => $url]
                    ]
                ]
            ]
        );

        if (!$response->successful()) {
            return [
                'status' => 'unknown',
                'threatTypes' => [],
                'explanation' => 'Failed to contact Safe Browsing API.',
                'source' => 'Google Safe Browsing API'
            ];
        }

        $data = $response->json();

        // Parse response
        if (!empty($data['matches'])) {
            $threatTypes = array_values(array_unique(
                array_column($data['matches'], 'threatType')
            ));
            $status = 'malicious';
            
            // More detailed explanation
            $threatMap = [
                'MALWARE' => 'malware distribution',
                'SOCIAL_ENGINEERING' => 'phishing/social engineering',
                'UNWANTED_SOFTWARE' => 'unwanted software',
                'POTENTIALLY_HARMFUL_APPLICATION' => 'potentially harmful application'
            ];
            
            $humanReadableThreats = array_map(function($type) use ($threatMap) {
                return $threatMap[$type] ?? strtolower($type);
            }, $threatTypes);
            
            $explanation = '❌ URL flagged by Google Safe Browsing for: ' . 
                        implode(', ', $humanReadableThreats);
        } else {
            $threatTypes = [];
            $status = 'safe';
            $explanation = '✅ URL verified safe by Google Safe Browsing API.';
        }


        //dd('URL check result', $url, $status, $threatTypes, $explanation);

        // 4️⃣ Cache result using url_hash
        UrlCache::updateOrCreate(
            ['url_hash' => hash('sha256', $url)],
            [
                'url' => $url,
                'status' => $status,
                'threat_types' => $threatTypes,
                'explanation' => $explanation,
                'last_checked' => now(),
            ]
        );
        
        return [
            'status' => $status,
            'threatTypes' => $threatTypes,
            'explanation' => $explanation,
            'source' => 'Google Safe Browsing API'
        ];
    }
    
    /**
     * Function 4: Extract URLs from HTML/plain text
     */
    private function extractUrls(string $content): array
    {
        $urls = [];
        
        // Method 1: Extract from HTML <a> tags
        if (preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            $urls = array_merge($urls, $matches[1]);
        }
        
        // Method 2: Extract plain URLs (common in plain text emails)
        $urlPattern = '/(https?:\/\/[^\s<>"\'\)]+)/i';
        if (preg_match_all($urlPattern, $content, $plainMatches)) {
            $urls = array_merge($urls, $plainMatches[1]);
        }
        
        // Clean and deduplicate
        $urls = array_map(function($url) {
            return trim(html_entity_decode($url));
        }, $urls);
        
        return array_unique($urls);
    }
    
    /**
     * Getter for weight (optional)
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
    
    /**
     * Setter for weight (optional)
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
}