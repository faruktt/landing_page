<?php

namespace App\Support;

use App\Models\Setting;

class Delivery
{
    public static function chargeFor(string $district): float
    {
        $settings = Setting::current();

        return $district === config('delivery.dhaka_district')
            ? (float) ($settings->dhaka_delivery_charge ?: 70)
            : (float) ($settings->default_delivery_charge ?: 130);
    }
}
