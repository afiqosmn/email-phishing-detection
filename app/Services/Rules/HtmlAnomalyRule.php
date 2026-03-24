<?php

namespace App\Services\Rules;

class HtmlAnomalyRule
{
    private $whitelistedPatterns = [
        '/utm_[a-z_]+=[^"\'\s]*/i',
        '/<img[^>]+height="1"[^>]+width="1"/i',
        '/<!--.*unsubscribe.*-->/i',
        '/display:\s*none.*email-prefs/i',
    ];

    protected $weight = 0.1;
    
    // FIX: Accept array like other rules
    public function evaluate(array $email): array
    {
        //dd('HtmlAnomalyRule evaluate called', $email);
        $html = $email['body'] ?? '';
        
        if (empty($html)) {
            return ['score' => 0, 'details' => [], 'weight' => 0.1];
        }
        
        $score = 0;
        $details = [];
        
        // 1. Check for suspicious hidden text
        $hiddenText = $this->extractHiddenText($html);
        
        if (!empty($hiddenText)) {
            if (!$this->isWhitelistedHidden($hiddenText)) {
                if ($this->containsSuspiciousContent($hiddenText)) {
                    $score += 0.7;
                    $details[] = 'Suspicious hidden text';
                } else {
                    $score += 0.3;
                    $details[] = 'Hidden text detected';
                }
            }
        }
        
        // 2. Check for color matching
        if ($this->hasColorMatchingBackground($html)) {
            $score += 0.8;
            $details[] = 'Text camouflaged with background';
        }
        
        // 3. Check for zero-sized elements
        if ($this->hasZeroSizedElements($html)) {
            $score += 0.5;
            $details[] = 'Zero-sized elements found';
        }
        
        return [
            'score' => min(1.0, $score),
            'details' => $details,
            'weight' => 0.1
        ];
    }
    
    /**
     * Extract hidden text from HTML content
     */
    private function extractHiddenText(string $html): string
    {
        // Extract text with display:none, visibility:hidden, opacity:0, etc.
        if (preg_match('/<[^>]+style="[^"]*(display:\s*none|visibility:\s*hidden|opacity:\s*0)[^"]*"[^>]*>([^<]+)<\/[^>]+>/i', $html, $matches)) {
            return $matches[2] ?? '';
        }
        return '';
    }
    
    private function containsSuspiciousContent(string $text): bool
    {
        $suspiciousPatterns = [
            '/password/i',
            '/login/i',
            '/verify.*account/i',
            '/click.*here/i',
            '/urgent/i',
            '/immediately/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        return false;
    }
    
    private function hasColorMatchingBackground(string $html): bool
    {
        // Check for text color matching background color
        return preg_match('/color:\s*#([0-9a-f]{6})\s*;.*background(?:-color)?:\s*#(\1)/i', $html) ||
               preg_match('/color:\s*rgba?\([^)]+\)\s*;.*background(?:-color)?:\s*rgba?\([^)]+\)/i', $html);
    }
    
    private function hasZeroSizedElements(string $html): bool
    {
        // Check for elements with width/height = 0
        return preg_match('/(width|height|max-width|max-height):\s*0(?:px)?/i', $html) ||
               preg_match('/<[^>]+(width|height)="0"[^>]*>/i', $html);
    }
    
    private function isWhitelistedHidden(string $hiddenText): bool
    {
        foreach ($this->whitelistedPatterns as $pattern) {
            if (preg_match($pattern, $hiddenText)) {
                return true;
            }
        }
        return false;
    }
}