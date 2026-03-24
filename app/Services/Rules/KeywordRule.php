<?php

namespace App\Services\Rules;

class KeywordRule
{
    protected $weight = 0.2;  // 20% weight in final score
    
    // Context-aware keyword categories
    private $keywordCategories = [
        'high_urgency' => [
            'immediately',
            'urgent action',
            'urgently',
            'within 24 hours',
            'today only',
            'limited time',
            'act now',
            'right away',
            'as soon as possible',
            'deadline',
        ],
        'credential_request' => [
            'verify your password',
            'reset your password',
            'change your password',
            'update your credentials',
            'login details',
            'account verification',
            'confirm your identity',
            'security verification',
            'authentication required',
            're-enter your password',
        ],
        'financial' => [
            'banking alert',
            'suspicious transaction',
            'wire transfer',
            'payment confirmation',
            'invoice payment',
            'refund request',
            'account suspended',
            'overdue payment',
            'billing issue',
            'tax refund',
        ],
        'generic_notification' => [
            'notification',
            'alert',
            'update',
            'information',
            'notice',
            'reminder',
            'message',
            'communication',
        ],
        'threat_language' => [
            'your account will be disabled',
            'account closure',
            'suspended permanently',
            'terminated',
            'legal action',
            'fines',
            'penalties',
            'violation',
            'breach',
            'unauthorized access',
        ],
        'prize_winnings' => [
            'you have won',
            'congratulations you won',
            'claim your prize',
            'lottery winner',
            'jackpot',
            'reward',
            'free gift',
            'bonus offer',
        ]
    ];
    
    // Whitelisted sender patterns (lower suspicion for these)
    private $whitelistedSenders = [
        '@paypal.com',
        '@amazon.com',
        '@microsoft.com',
        '@google.com',
        '@apple.com',
    ];
    
    public function evaluate(array $email): array
    {
        //dd('KeywordRule evaluate called', $email);
        $body = strtolower($email['body'] ?? '');
        $subject = strtolower($email['subject'] ?? '');
        $headers = $email['headers'] ?? [];
        $from = $this->extractFromEmail($headers);
        
        // Combine subject and body for analysis
        $fullText = $subject . ' ' . $body;
        
        // Skip if text is too short
        if (strlen($fullText) < 10) {
            return [
                'score' => 0.0,
                'details' => ['reason' => 'Text too short'],
                'weight' => $this->weight,
            ];
        }
        
        // Check if sender is whitelisted (reduce suspicion)
        $isWhitelisted = $this->isWhitelistedSender($from);
        
        // Analyze keywords with context
        $analysis = $this->analyzeKeywords($fullText, $subject, $isWhitelisted);
        
        return [
            'score' => $analysis['score'],
            'details' => $analysis['details'],
            'weight' => $this->weight,
        ];
    }
    
    /**
     * Analyze keywords with context awareness
     */
    private function analyzeKeywords(string $text, string $subject, bool $isWhitelisted = false): array
    {
        $foundKeywords = [];
        $categoryScores = [];
        $details = [];
        
        // Count keywords in each category
        foreach ($this->keywordCategories as $category => $keywords) {
            $foundInCategory = [];
            
            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $foundInCategory[] = $keyword;
                    $foundKeywords[] = $keyword;
                }
            }
            
            if (!empty($foundInCategory)) {
                $categoryScores[$category] = count($foundInCategory);
                $details['found_' . $category] = $foundInCategory;
            }
        }
        
        // Calculate base score
        $score = $this->calculateKeywordScore($categoryScores, $foundKeywords, $subject);
        
        // Adjust score based on sender whitelist
        if ($isWhitelisted) {
            $score *= 0.5;  // Reduce suspicion by 50% for whitelisted senders
            $details['whitelisted_sender'] = true;
        }
        
        // Check for suspicious combinations
        $combinationScore = $this->checkSuspiciousCombinations($categoryScores);
        $score = max($score, $combinationScore);
        
        // Check for excessive keywords (spam indicator)
        if (count($foundKeywords) >= 5) {
            $score = min(1.0, $score + 0.3);
            $details['excessive_keywords'] = count($foundKeywords);
        }
        
        $details['total_keywords_found'] = count($foundKeywords);
        $details['categories_triggered'] = array_keys($categoryScores);
        
        return [
            'score' => min(1.0, $score),
            'details' => $details,
        ];
    }
    
    /**
     * Calculate score based on keyword findings
     */
    private function calculateKeywordScore(array $categoryScores, array $foundKeywords, string $subject): float
    {
        if (empty($foundKeywords)) {
            return 0.0;
        }
        
        $score = 0.0;
        
        // High urgency keywords are very suspicious
        if (isset($categoryScores['high_urgency'])) {
            $score += 0.4 * min(2, $categoryScores['high_urgency']); // Cap at 0.8
        }
        
        // Credential requests are high risk
        if (isset($categoryScores['credential_request'])) {
            $score += 0.5;
        }
        
        // Threat language is concerning
        if (isset($categoryScores['threat_language'])) {
            $score += 0.4;
        }
        
        // Financial keywords are moderately suspicious
        if (isset($categoryScores['financial'])) {
            $score += 0.3;
        }
        
        // Prize/winnings are common in phishing
        if (isset($categoryScores['prize_winnings'])) {
            $score += 0.4;
        }
        
        // Generic notifications are low risk
        if (isset($categoryScores['generic_notification'])) {
            $score += 0.1;
        }
        
        // Check if subject line contains urgent keywords (more suspicious)
        $urgentInSubject = false;
        foreach ($this->keywordCategories['high_urgency'] as $keyword) {
            if (stripos($subject, $keyword) !== false) {
                $urgentInSubject = true;
                break;
            }
        }
        
        if ($urgentInSubject) {
            $score += 0.2;
        }
        
        return min(1.0, $score);
    }
    
    /**
     * Check for suspicious keyword combinations
     */
    private function checkSuspiciousCombinations(array $categoryScores): float
    {
        // High urgency + credential request = very suspicious
        if (isset($categoryScores['high_urgency']) && isset($categoryScores['credential_request'])) {
            return 0.9;
        }
        
        // Financial + urgency = suspicious
        if (isset($categoryScores['financial']) && isset($categoryScores['high_urgency'])) {
            return 0.7;
        }
        
        // Threat + credential request = suspicious
        if (isset($categoryScores['threat_language']) && isset($categoryScores['credential_request'])) {
            return 0.8;
        }
        
        // Prize + urgency = suspicious
        if (isset($categoryScores['prize_winnings']) && isset($categoryScores['high_urgency'])) {
            return 0.6;
        }
        
        return 0.0;
    }
    
    /**
     * Extract email from headers
     */
    private function extractFromEmail(array $headers): string
    {
        foreach ($headers as $header) {
            if (stripos($header, 'From:') === 0) {
                // Extract email from "From: Name <email@domain.com>" or "From: email@domain.com"
                if (preg_match('/<([^>]+)>/', $header, $matches)) {
                    return strtolower(trim($matches[1]));
                } elseif (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $header, $matches)) {
                    return strtolower(trim($matches[1]));
                }
            }
        }
        
        return '';
    }
    
    /**
     * Check if sender is whitelisted
     */
    private function isWhitelistedSender(string $fromEmail): bool
    {
        if (empty($fromEmail)) {
            return false;
        }
        
        foreach ($this->whitelistedSenders as $pattern) {
            if (str_contains($fromEmail, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Getter for weight
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
    
    /**
     * Setter for weight
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
    
    /**
     * Method to add custom keywords (optional)
     */
    public function addKeywords(string $category, array $keywords): self
    {
        if (!isset($this->keywordCategories[$category])) {
            $this->keywordCategories[$category] = [];
        }
        
        $this->keywordCategories[$category] = array_merge(
            $this->keywordCategories[$category],
            $keywords
        );
        
        return $this;
    }
    
    /**
     * Method to add whitelisted senders (optional)
     */
    public function addWhitelistedSender(string $domainPattern): self
    {
        $this->whitelistedSenders[] = $domainPattern;
        return $this;
    }
}