<?php

namespace App\Services\Rules;

class AuthenticationRule
{
    protected $weight = 0.4;
    
    public function evaluate(array $email): array
    {
        $headers = $email['headers'] ?? [];
        //dd('Authentication evaluate function', $headers);
        
        if (empty($headers)) {
            return [
                'score' => 0.5,
                'details' => ['message' => 'No headers available / Unable to evaluate authentication'],
                'weight' => $this->weight,
            ];
        }
        
        $score = 0;
        $details = [];
        $passCount = 0;
        
        // Check SPF
        $spfResult = $this->checkSPF($headers);
        if ($spfResult) {
            if ($spfResult['result'] === 'pass' && ($spfResult['aligned'] ?? false)) {
                $score += 0.0;
                $details['spf'] = 'STRONG_PASS';
                $details['spf_explanation'] = 'SPF passed & aligned with domain/subdomain';
                $passCount += 2;
            } elseif ($spfResult['result'] === 'pass') {
                $score += 0.2;
                $details['spf'] = 'WEAK_PASS';
                $details['spf_explanation'] = 'SPF passed but not aligned with domain/subdomain';
                $passCount++;
            } elseif ($spfResult['result'] === 'fail') {
                $score += 0.8;
                $details['spf'] = 'FAIL';
                $details['spf_explanation'] = 'SPF failed';
                $passCount=0;
            }
            else {
                $score += 0.5;
                $details['spf'] = 'UNKNOWN/SUSPICIOUS';
                $details['spf_explanation'] = 'SPF result: Suspicious or unknown';
            }
        }

        //dd('After SPF check', $spfResult, $score, $details,$passCount);
        
        // Check DKIM
        $dkimResult = $this->checkDKIM($headers);
        if ($dkimResult && $dkimResult['result'] === 'pass') {
            $score += 0.1;  // Low suspicion for DKIM pass
            $details['dkim'] = 'PASS';
            $passCount++;
        } elseif ($dkimResult && $dkimResult['result'] === 'fail') {
            $score += 0.7;  // High suspicion for DKIM fail
            $details['dkim'] = 'FAIL';
        } else {
            $score += 0.4;  // Moderate suspicion for DKIM fail
            $details['dkim'] = 'UNKNOWN/SUSPICIOUS';
        }
        //dd('After DKIM check', $dkimResult, $score, $details);
        
        // Check DMARC
        $dmarcResult = $this->checkDMARC($headers);
        if ($dmarcResult && $dmarcResult['result'] === 'pass') {
            $score += 0.1;  // Low suspicion for DMARC pass
            $details['dmarc'] = 'PASS';
            $passCount++;
        } elseif ($dmarcResult && $dmarcResult['result'] === 'fail') {
            $score += 0.5;  // High suspicion for DMARC fail
            $details['dmarc'] = 'FAIL';
        } else {
            $score += 0.2;  // Low suspicion for no DMARC (many legit emails don't have DMARC)
            $details['dmarc'] = 'UNKNOWN/SUSPICIOUS';
        }
        //dd('After DMARC check', $dmarcResult, $score, $details);
        
        // Apply "2 out of 3" bonus/malus
        if ($passCount >= 2) {
            // At least 2 passed = reduce overall suspicion
            $score *= 0.5;  // Cut score in half
            $details['overall'] = 'STRONG_AUTH';
        } elseif ($passCount === 1) {
            // Only 1 passed = neutral
            $details['overall'] = 'WEAK_AUTH';
        } else {
            // None passed = increase suspicion
            $score *= 1.5;  // Increase score by 50%
            $details['overall'] = 'POOR_AUTH';
        }

        //dd('Final Authentication score and details', $score, $details);
        return [
            'score' => min(1.0, $score),
            'details' => $details,
            'weight' => $this->weight,
        ];
    }

    private function checkDMARC(array $headers): ?array
    {
        foreach ($headers as $header) {
            $trimmed = trim($header);
            
            if (stripos($trimmed, 'ARC-Authentication-Results') === 0 || 
                stripos($trimmed, 'Authentication-Results') === 0) {
                
                if (preg_match('/\bdmarc\s*=\s*(pass|fail|neutral|none|permerror|temperror)\b/i', $trimmed, $matches)) {
                    return ['result' => strtolower($matches[1])];
                }
            }
        }
        return null;
    }
        
    private function checkSPF(array $headers): ?array
    {
        //dd('Checking SPF', $headers);
        $spfHeaders = [];

        foreach ($headers as $header) {
            $trimmed = trim($header);
            
            // Collect all SPF-related headers
            if (preg_match('/(Received-SPF|X-Received-SPF|SPF-Result|ARC-Authentication-Results|Authentication-Results)/i', $trimmed)) {
                $spfHeaders[] = $trimmed;
                
                // Format 1: ARC-Authentication-Results or Authentication-Results
                if (preg_match('/\bspf\s*=\s*(pass|fail|softfail|neutral|none|permerror|temperror)\b/i', $trimmed, $matches)) {
                    $fromEmail = $this->extractFromEmail($headers);
                    return [
                        'result' => strtolower($matches[1]),
                        'aligned' => $this->checkDomainAlignment($fromEmail, $trimmed)
                    ];
                }

                // Format 2: Received-SPF with result
                if (preg_match('/^Received-SPF:\s*(pass|fail|softfail|neutral|none)\b/i', $trimmed, $matches)) {
                    $fromEmail = $this->extractFromEmail($headers);
                    return [
                        'result' => strtolower($matches[1]),
                        'aligned' => $this->checkDomainAlignment($fromEmail, $trimmed)
                    ];
                }
            }
        }

        // If we found SPF headers but couldn't parse result
        if (!empty($spfHeaders)) {
            // Debug: Show what we found
            //dd('SPF headers found but no result could be parsed', $spfHeaders);
            
            return [
                'result' => 'unknown',
                'reason' => 'no_clear_spf_result',
                'headers' => $spfHeaders
            ];
        }

        return null;
    }
    
    private function checkDKIM(array $headers): ?array
    {
        // First, check Authentication-Results for DKIM validation
        $dkimResult = $this->checkDKIMFromAuthResults($headers);
        //dd('DKIM result from Authentication-Results', $dkimResult);
        if ($dkimResult !== null) {
            return $dkimResult;
        }
        
        // If no explicit result, check for DKIM-Signature presence
        if ($this->hasDKIMSignature($headers)) {
            //dd('DKIM signature found but no validation result, still in checkDKIM');
            // Has signature but no validation result - suspicious
            return ['result' => 'unknown', 'reason' => 'signature present but not validated'];
        }
        
        return null;
    }

    private function checkDKIMFromAuthResults(array $headers): ?array
    {
        foreach ($headers as $header) {
            $trimmed = trim($header);
            
            // Look for Authentication-Results header
            if (stripos($trimmed, 'Authentication-Results') === 0 || stripos($trimmed, 'ARC-Authentication-Results') === 0) {
                // Extract DKIM result
                if (preg_match('/dkim=(\w+)/i', $trimmed, $matches)) {
                    //dd('DKIM match found in Authentication-Results', $matches, $trimmed);
                    return [
                        'result' => strtolower($matches[1]),
                        'details' => $trimmed
                    ];
                }
                
                // Alternative pattern: dkim=pass or dkim=fail
                if (preg_match('/dkim\s*=\s*(pass|fail|neutral|none|permerror|temperror)/i', $trimmed, $matches)) {
                    //dd('DKIM alternative match found in Authentication-Results', $matches, $trimmed);
                    return [
                        'result' => strtolower($matches[1]),
                        'details' => $trimmed
                    ];
                }
            }
        }
        return null;
    }

    private function hasDKIMSignature(array $headers): bool
    {
        foreach ($headers as $header) {
            $trimmed = trim($header);
            
            // Check for DKIM-Signature header (various formats)
            if (stripos($trimmed, 'DKIM-Signature') === 0 ||
                preg_match('/^DKIM-Signature\s*:/i', $trimmed)) {
                    //dd('DKIM-Signature header found', $trimmed);
                return true;
            }
        }
        return false;
    }
    
    private function extractFromEmail(array $headers): string
    {
        foreach ($headers as $header) {
            // More flexible check for From header
            if (stripos(trim($header), 'From:') === 0) {
                // Try to extract email from various formats:
                // 1. From: Name <email@domain.com>
                if (preg_match('/<([^>]+)>/', $header, $matches)) {
                    return $matches[1];
                }
                // 2. From: email@domain.com
                if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $header, $matches)) {
                    return $matches[1];
                }
            }
        }
        return '';
    }
    
    private function checkDomainAlignment(string $fromEmail, string $spfHeader): bool
    {
        //dd('Checking domain alignment', $fromEmail, $spfHeader);
        // Extract domain from email
        $emailDomain = substr(strrchr($fromEmail, "@"), 1);
        
        // Pattern 1: domain=example.com (standard SPF Received header)
        if (preg_match('/\bdomain=([a-zA-Z0-9.-]+)\b/i', $spfHeader, $matches)) {
            $spfDomain = $matches[1];
            //dd($spfDomain, $emailDomain,'Pattern 1 matched');
            return $emailDomain === $spfDomain;
        }

        // Pattern 2: smtp.mailfrom=...@example.com (ARC-Authentication-Results format)
        if (preg_match('/smtp\.mailfrom=[^@]+@([a-zA-Z0-9.-]+)/i', $spfHeader, $matches)) {
            $spfDomain = $matches[1];
            //dd($spfDomain, $emailDomain,'Pattern 2 matched');
            return $emailDomain === $spfDomain;
        }

        // Pattern 3: mailfrom=...@example.com (alternative format)
        if (preg_match('/mailfrom=[^@]+@([a-zA-Z0-9.-]+)/i', $spfHeader, $matches)) {
            $spfDomain = $matches[1];
            //dd($spfDomain, $emailDomain,'Pattern 3 matched');
            return $emailDomain === $spfDomain;
        }

        // Pattern 4: Check if the domain is in the header at all (fallback)
        if (strpos($spfHeader, $emailDomain) !== false) {
            //dd($spfDomain, $emailDomain,'Pattern 4 matched');
            return true; // Lenient match - domain appears somewhere in header
        }
        //dd($spfDomain, $emailDomain);    
        return false;
    }
}