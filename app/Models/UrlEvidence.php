<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlEvidence extends Model
{
    protected $table = 'url_evidences';

    protected $fillable = [
        'detection_result_id',
        'url',
        'status',
        'threat_types',
        'explanation',
        'source',
        'classification',
    ];

    protected $casts = [
        'threat_types' => 'array', // JSON array (malware, phishing, etc.)
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
