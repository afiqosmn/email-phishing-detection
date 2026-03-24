<?php

namespace App\Services;

use App\Services\Rules\AuthenticationRule;
use App\Services\Rules\UrlRule;
use App\Services\Rules\KeywordRule;
use App\Services\Rules\HtmlAnomalyRule;

class RuleManager
{
    protected array $rules;

    public function __construct(
        AuthenticationRule $authenticationRule,
        UrlRule $urlRule,
        KeywordRule $keywordRule,
        HtmlAnomalyRule $htmlRule
    ) {
        $this->rules = [
            'authentication' => $authenticationRule,
            'url'            => $urlRule,
            'keyword'        => $keywordRule,
            'html_anomaly'   => $htmlRule,
        ];
    }

    /**
     * Run all rules and return raw findings
     */
    public function run(array $email): array
    {
        $results = [];

        foreach ($this->rules as $name => $rule) {
            $results[$name] = $rule->evaluate($email);
        }

        return $results;
    }
}
