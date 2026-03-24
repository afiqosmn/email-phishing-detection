<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlCache extends Model
{
    protected $table = 'url_caches';
    protected $fillable = ['url', 'url_hash', 'status', 'threat_types', 'last_checked', 'explanation'];

    // Cast JSON fields
    protected $casts = [
        'threat_types' => 'array',
    ];
}
