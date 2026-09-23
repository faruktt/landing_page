<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompleteOrder extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'district',
        'payment_method',
        'cart_snapshot',
        'subtotal',
        'ip_address',
        'contacted_at',
    ];

    protected $casts = [
        'cart_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'contacted_at' => 'datetime',
    ];
}
