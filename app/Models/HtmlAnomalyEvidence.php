<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HtmlAnomalyEvidence extends Model
{
    protected $table = 'html_anomaly_evidences';

    protected $fillable = [
        'detection_result_id',
        'anomaly_type',
        'severity',
        'explanation',
        'metadata',
        'classification',
    ];

    protected $casts = [
        'metadata' => 'array', // Additional context about the anomaly
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
