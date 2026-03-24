<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GmailSync extends Model
{
    protected $fillable = [
        'user_id',
        'sync_type',
        'emails_fetched',
        'emails_processed',
        'last_message_id',
        'started_at',
        'completed_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the duration in seconds
     */
    public function getDurationSeconds(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at->diffInSeconds($this->started_at);
        }
        return null;
    }
}
