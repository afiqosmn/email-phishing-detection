<?php

namespace App\Services;

use App\Models\DetectionResult;
use App\Models\UrlEvidence;
use App\Models\AuthenticationEvidence;
use App\Models\KeywordEvidence;
use App\Models\HtmlAnomalyEvidence;
use Illuminate\Support\Facades\Log;

class DetectionService
{
    public function __construct(
        protected RuleManager $ruleManager,
        protected MLDetectionService $mlService
    ) {}

    /**
     * Analyze email data using rules and ML
     */
    public function analyze(array $emailData, string $messageId): array
    {
        // Run all rules (including URL rules)
        $ruleResults = $this->ruleManager->run($emailData);

        // ML prediction
        $mlPrediction = $this->mlService->predict($emailData);

        // Calculate score based on rule results and any URL findings embedded in rules
        $score = $this->calculateScore($ruleResults);

        // Decide final classification
        $finalDecision = (
            $score >= 70 ||
            ($mlPrediction['ml_result'] === 'phishing' && $mlPrediction['ml_confidence'] >= 0.7)
        )
            ? 'phishing'
            : 'legitimate';

        $email = \App\Models\Email::where('message_id', $messageId)->firstOrFail();

        // Save detection result
        $result = DetectionResult::create([
            'email_id'       => $email->id,
            'message_id'     => $messageId,
            'rule_result'     => 'rule_engine',
            'rule_score'      => $score,
            'rule_details'    => $ruleResults,
            'ml_result'       => $mlPrediction['ml_result'],
            'ml_confidence'   => $mlPrediction['ml_confidence'],
            'final_decision'  => $finalDecision,
        ]);

        // Persist evidence from rule results (now stores for all emails with classification)
        $this->persistEvidence($result, $ruleResults, $finalDecision);

        $email->update(['processing_status' => 'scanned']);

        return $result->toArray();
    }

    /**
     * Calculate rule-based score
     */
    private function calculateScore(array $rules): int
    {
        $score = 0;

        foreach ($rules as $rule) {
            // Each rule can include URL findings internally
            $score += $rule['score'] ?? 0;
        }

        return min($score, 100);
    }

    /**
     * Persist evidence from rule results into dedicated tables
     * Now stores evidence for all emails (both phishing and legitimate) with classification
     */
    private function persistEvidence(DetectionResult $result, array $ruleResults, string $finalDecision): void
    {
        // Determine classification for this evidence
        // This allows us to track why legitimate emails were marked as safe
        $classification = match($finalDecision) {
            'phishing' => 'phishing',
            default => 'legitimate'
        };

        // URL Evidence
        if (isset($ruleResults['url']['details']) && !empty($ruleResults['url']['details'])) {
            foreach ($ruleResults['url']['details'] as $urlDetail) {
                UrlEvidence::create([
                    'detection_result_id' => $result->id,
                    'url' => $urlDetail['url'] ?? '',
                    'status' => $urlDetail['status'] ?? 'unknown',
                    'threat_types' => $urlDetail['threatTypes'] ?? [],
                    'explanation' => $urlDetail['explanation'] ?? '',
                    'source' => $urlDetail['source'] ?? 'unknown',
                    'classification' => $classification,
                ]);
            }
        }

        // Authentication Evidence (SPF, DKIM, DMARC)
        if (isset($ruleResults['authentication']['details'])) {
            $authDetails = $ruleResults['authentication']['details'];
            
            // SPF
            if (isset($authDetails['spf'])) {
                AuthenticationEvidence::create([
                    'detection_result_id' => $result->id,
                    'check_type' => 'spf',
                    'result' => $this->normalizeAuthResult($authDetails['spf']),
                    'aligned' => $authDetails['spf_aligned'] ?? null,
                    'explanation' => $authDetails['spf_explanation'] ?? sprintf('SPF: %s', $authDetails['spf']),
                    'classification' => $classification,
                ]);
            }

            // DKIM
            if (isset($authDetails['dkim'])) {
                AuthenticationEvidence::create([
                    'detection_result_id' => $result->id,
                    'check_type' => 'dkim',
                    'result' => $this->normalizeAuthResult($authDetails['dkim']),
                    'aligned' => $authDetails['dkim_aligned'] ?? null,
                    'explanation' => $authDetails['dkim_explanation'] ?? sprintf('DKIM: %s', $authDetails['dkim']),
                    'classification' => $classification,
                ]);
            }

            // DMARC
            if (isset($authDetails['dmarc'])) {
                AuthenticationEvidence::create([
                    'detection_result_id' => $result->id,
                    'check_type' => 'dmarc',
                    'result' => $this->normalizeAuthResult($authDetails['dmarc']),
                    'aligned' => $authDetails['dmarc_aligned'] ?? null,
                    'explanation' => $authDetails['dmarc_explanation'] ?? sprintf('DMARC: %s', $authDetails['dmarc']),
                    'classification' => $classification,
                ]);
            }
        }

        // Keyword Evidence
        if (isset($ruleResults['keyword']['details'])) {
            $keywordDetails = $ruleResults['keyword']['details'];
            
            // Extract category-based keywords
            foreach ($keywordDetails as $key => $value) {
                // Match keys like found_high_urgency, found_credential_request, etc.
                if (strpos($key, 'found_') === 0 && is_array($value)) {
                    $category = substr($key, 6); // Remove 'found_' prefix
                    KeywordEvidence::create([
                        'detection_result_id' => $result->id,
                        'category' => $category,
                        'keywords_found' => $value,
                        'count' => count($value),
                        'explanation' => sprintf('Found %d %s keywords: %s', 
                            count($value), 
                            str_replace('_', ' ', $category),
                            implode(', ', array_slice($value, 0, 3)) // Show first 3
                        ),
                        'classification' => $classification,
                    ]);
                }
            }
        }

        // HTML Anomaly Evidence
        if (isset($ruleResults['html_anomaly']['details']) && is_array($ruleResults['html_anomaly']['details'])) {
            $htmlDetails = $ruleResults['html_anomaly']['details'];
            
            // HTML anomaly returns an array of strings (details)
            foreach ($htmlDetails as $index => $anomalyDescription) {
                if (is_string($anomalyDescription)) {
                    HtmlAnomalyEvidence::create([
                        'detection_result_id' => $result->id,
                        'anomaly_type' => $this->categorizeHtmlAnomaly($anomalyDescription),
                        'severity' => $this->getAnomalySeverity($anomalyDescription),
                        'explanation' => $anomalyDescription,
                        'metadata' => null,
                        'classification' => $classification,
                    ]);
                }
            }
        }
    }

    /**
     * Normalize authentication result values
     */
    private function normalizeAuthResult(string $result): string
    {
        $result = strtolower($result);
        
        // Map various result formats to standard enum values
        $mapping = [
            'pass' => 'pass',
            'fail' => 'fail',
            'neutral' => 'neutral',
            'none' => 'none',
            'unknown' => 'unknown',
            'suspicious' => 'unknown',
            'weak_pass' => 'pass',
            'strong_pass' => 'pass',
            'permerror' => 'unknown',
            'temperror' => 'unknown',
        ];

        return $mapping[$result] ?? 'unknown';
    }

    /**
     * Categorize HTML anomaly from description
     */
    private function categorizeHtmlAnomaly(string $description): string
    {
        $lower = strtolower($description);
        
        if (stripos($description, 'hidden') !== false) {
            return 'hidden_content';
        } elseif (stripos($description, 'camouflaged') !== false || stripos($description, 'color') !== false) {
            return 'camouflaged_text';
        } elseif (stripos($description, 'zero') !== false) {
            return 'zero_sized_elements';
        } elseif (stripos($description, 'iframe') !== false) {
            return 'iframe';
        }
        
        return 'other_anomaly';
    }

    /**
     * Get severity of HTML anomaly
     */
    private function getAnomalySeverity(string $description): string
    {
        // Suspicious hidden text is high severity
        if (stripos($description, 'suspicious') !== false) {
            return 'high';
        }
        
        // Camouflaged text is high severity
        if (stripos($description, 'camouflaged') !== false) {
            return 'high';
        }
        
        // Regular hidden text is medium
        if (stripos($description, 'hidden') !== false) {
            return 'medium';
        }
        
        return 'medium';
    }
}
