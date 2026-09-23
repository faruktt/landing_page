<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_name',
        'phone',
        'address',
        'district',
        'ip_address',
        'courier_name',
        'steadfast_consignment_id',
        'steadfast_tracking_code',
        'steadfast_delivery_status',
        'pathao_consignment_id',
        'pathao_tracking_code',
        'pathao_delivery_status',
        'payment_method',
        'subtotal',
        'delivery_charge',
        'discount_amount',
        'total',
        'paid_amount',
        'due_amount',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function hasCourierConsignment(): bool
    {
        return ! empty($this->steadfast_consignment_id) || ! empty($this->pathao_consignment_id);
    }

    public function getActiveTrackingCodeAttribute(): ?string
    {
        return $this->pathao_tracking_code ?: $this->steadfast_tracking_code;
    }

    public function getActiveConsignmentIdAttribute(): ?string
    {
        return $this->pathao_consignment_id ?: $this->steadfast_consignment_id;
    }

    public function getActiveCourierNameAttribute(): ?string
    {
        if ($this->courier_name) {
            return $this->courier_name;
        }

        if ($this->steadfast_consignment_id) {
            return 'steadfast';
        }

        if ($this->pathao_consignment_id) {
            return 'pathao';
        }

        return null;
    }
}
