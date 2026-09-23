<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = ['ip_address', 'reason', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function isBlocked(string $ip): bool
    {
        return static::where('ip_address', $ip)->where('expires_at', '>', now())->exists();
    }
}
