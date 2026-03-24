<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeywordEvidence extends Model
{
    protected $table = 'keyword_evidences';

    protected $fillable = [
        'detection_result_id',
        'category',
        'keywords_found',
        'count',
        'explanation',
        'classification',
    ];

    protected $casts = [
        'keywords_found' => 'array', // JSON array of found keywords
    ];

    public $timestamps = true;

    /**
     * Relationship: Evidence belongs to a detection result
     */
    public function detectionResult(): BelongsTo
    {
        return $this->belongsTo(DetectionResult::class);
    }
}
