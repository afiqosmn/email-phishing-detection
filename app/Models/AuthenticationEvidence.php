<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticationEvidence extends Model
{
    protected $table = 'authentication_evidences';

    protected $fillable = [
        'detection_result_id',
        'check_type',
        'result',
        'aligned',
        'explanation',
        'classification',
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
