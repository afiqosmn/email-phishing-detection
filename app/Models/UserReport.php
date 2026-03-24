<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    protected $table = 'user_reports';

    protected $fillable = [
        'user_id',
        'email_id',
        'report_type',
        'reason',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: A report belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A report belongs to an email
     */
    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    /**
     * Get report type display name
     */
    public function getReportTypeLabel(): string
    {
        return match ($this->report_type) {
            'false_positive' => 'False Positive',
            'false_negative' => 'False Negative',
            'unrequested_phishing' => 'Unrequested Phishing Report',
            'whitelist_request' => 'Whitelist Request',
            default => 'Other',
        };
    }

    /**
     * Get status display name
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'acknowledged' => 'Acknowledged',
            'dismissed' => 'Dismissed',
            default => 'Unknown',
        };
    }

    /**
     * Get badge color for status
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'submitted' => 'blue',
            'reviewed' => 'yellow',
            'acknowledged' => 'green',
            'dismissed' => 'gray',
            default => 'gray',
        };
    }
}
