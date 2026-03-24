<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectionResult extends Model
{
    protected $table = 'detection_results';

    protected $fillable = [
        'email_id',
        'message_id',
        'rule_result',
        'rule_score',
        'rule_details',
        'ml_result',
        'final_decision',
        'ml_confidence',
    ];

    protected $casts = [
        'rule_details' => 'array', // Automatically converts JSON ↔ array
        'ml_confidence' => 'decimal:2', // Optional: cast decimal
    ];

    public $timestamps = true; // kalau table ada created_at, updated_at

    /**
     * Relationship: A detection result belongs to an email
     */
    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    /**
     * Relationship: A detection result has many detected patterns
     */
    public function detectedPatterns(): HasMany
    {
        return $this->hasMany(DetectedPattern::class);
    }

    /**
     * Relationship: A detection result has many URL evidences
     */
    public function urlEvidences(): HasMany
    {
        return $this->hasMany(UrlEvidence::class);
    }

    /**
     * Relationship: A detection result has many authentication evidences
     */
    public function authenticationEvidences(): HasMany
    {
        return $this->hasMany(AuthenticationEvidence::class);
    }

    /**
     * Relationship: A detection result has many keyword evidences
     */
    public function keywordEvidences(): HasMany
    {
        return $this->hasMany(KeywordEvidence::class);
    }

    /**
     * Relationship: A detection result has many HTML anomaly evidences
     */
    public function htmlAnomalyEvidences(): HasMany
    {
        return $this->hasMany(HtmlAnomalyEvidence::class);
    }
}
