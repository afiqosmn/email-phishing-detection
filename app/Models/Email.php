<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $fillable = [
        'user_id','message_id','date','from','subject','snippet','processing_status'
    ];

    protected $casts = [
        'processing_status' => 'string',
        'date' => 'datetime',
    ];

    // RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detectionResult(): HasOne
    {
        return $this->hasOne(DetectionResult::class);
    }

    /**
     * Relationship: An email has many detected patterns
     */
    public function detectedPatterns(): HasMany
    {
        return $this->hasMany(DetectedPattern::class);
    }
}
