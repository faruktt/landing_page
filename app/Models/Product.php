<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'template',
        'image',
        'youtube_url',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'free_gift_text',
        'offer_ends_at',
        'sizes',
        'colors',
        'stock',
        'status',
    ];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
        'offer_ends_at' => 'datetime',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(ProductBenefit::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->orderBy('sort_order');
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class)->orderBy('sort_order');
    }

    public function trustPoints(): HasMany
    {
        return $this->hasMany(ProductTrustPoint::class)->orderBy('sort_order');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id');
    }

    public function cartOffers(): HasMany
    {
        return $this->hasMany(ProductCartOffer::class)->orderBy('min_cart_amount');
    }

    public function bestCartOffer(float $cartTotal): ?ProductCartOffer
    {
        return $this->cartOffers
            ->filter(fn ($offer) => $cartTotal >= (float) $offer->min_cart_amount)
            ->sortByDesc('min_cart_amount')
            ->first();
    }

    protected function youtubeEmbedUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->youtube_url) {
                return null;
            }

            preg_match(
                '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/',
                $this->youtube_url,
                $matches
            );

            return isset($matches[1]) ? 'https://www.youtube.com/embed/'.$matches[1] : null;
        });
    }

    public function discountPercent(): ?int
    {
        if (! $this->sale_price || $this->regular_price <= 0) {
            return null;
        }

        return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
    }

    public function displayPrice(): string
    {
        return number_format((float) ($this->sale_price ?? $this->regular_price), 2);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $path = ltrim($this->image, '/');

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            $withoutStorage = substr($path, 8);
            if (file_exists(public_path($withoutStorage))) {
                return asset($withoutStorage);
            }
        }

        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset($path);
    }
}
