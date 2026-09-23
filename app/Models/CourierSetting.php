<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CourierSetting extends Model
{
    public const COURIER_STEADFAST = 'steadfast';
    public const COURIER_PATHAO = 'pathao';

    protected $fillable = [
        'courier',
        'name',
        'is_active',
        'api_key',
        'secret_key',
        'client_id',
        'client_secret',
        'username',
        'password',
        'store_id',
        'environment',
        'additional_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional_settings' => 'array',
    ];

    /**
     * Get or initialize settings for a given courier.
     */
    public static function getSetting(string $courier): self
    {
        return static::firstOrCreate(
            ['courier' => $courier],
            [
                'name' => $courier === self::COURIER_PATHAO ? 'Pathao Courier' : 'Steadfast Courier',
                'is_active' => false,
                'api_key' => $courier === self::COURIER_STEADFAST ? config('services.steadfast.api_key') : null,
                'secret_key' => $courier === self::COURIER_STEADFAST ? config('services.steadfast.secret_key') : null,
                'environment' => 'production',
            ]
        );
    }

    /**
     * Get all active couriers.
     *
     * @return Collection<int, static>
     */
    public static function activeCouriers(): Collection
    {
        return static::where('is_active', true)->get()->keyBy('courier');
    }

    /**
     * Check if a specific courier is active.
     */
    public static function isCourierActive(string $courier): bool
    {
        return (bool) static::where('courier', $courier)->where('is_active', true)->exists();
    }
}
