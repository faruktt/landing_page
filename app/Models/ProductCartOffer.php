<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCartOffer extends Model
{
    protected $fillable = ['product_id', 'min_cart_amount', 'reward_type', 'discount_amount', 'sort_order'];

    protected $casts = [
        'min_cart_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
