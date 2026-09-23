<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo',
        'favicon',
        'phone',
        'whatsapp',
        'email',
        'address',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'dhaka_delivery_charge',
        'default_delivery_charge',
        'facebook_pixel_id',
        'google_analytics_id',
        'landing_announcement',
        'short_text',
        'landing_badge_1',
        'landing_badge_2',
        'landing_badge_3',
        'landing_offer_title',
        'landing_offer_subtitle',
        'landing_benefits_title',
        'landing_benefits_subtitle',
        'landing_reviews_title',
        'landing_order_title',
        'landing_order_btn_text',
        'landing_guarantee_note',
        'landing_p1_title',
        'landing_p1_badge_1',
        'landing_p1_badge_2',
        'landing_p1_offer_text',
        'landing_p1_regular_price',
        'landing_p1_sale_price',
        'landing_p1_bullets',
        'landing_p1_image_1',
        'landing_p1_image_2',
        'landing_p2_title',
        'landing_p2_badge_1',
        'landing_p2_badge_2',
        'landing_p2_regular_price',
        'landing_p2_sale_price',
        'landing_p2_bullets',
        'landing_p2_image',
        'landing_benefits_list',
        'landing_outlook_btn_text',
        'landing_review_btn_text',
    ];

    protected $casts = [
        'dhaka_delivery_charge' => 'decimal:2',
        'default_delivery_charge' => 'decimal:2',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'site_name' => 'Electora',
            'dhaka_delivery_charge' => 70,
            'default_delivery_charge' => 130,
            'landing_announcement' => '২৪ ঘন্টা গ্যাস থাকবে আপনার রান্নাঘরে গ্যারান্টি দিয়ে বলছি।',
            'landing_badge_1' => '2 Year Service Warranty',
            'landing_badge_2' => '১ বছরের রিপ্লেসমেন্ট গ্যারান্টি',
            'landing_badge_3' => 'ক্যাশ অন ডেলিভারি',
            'landing_offer_title' => 'আজকের জন্য ধামাকা অফার!!!',
            'landing_offer_subtitle' => 'অফারটি শেষ হতে বাকি',
            'landing_benefits_title' => 'গ্যাস কম্প্রেসর এর বিশেষ উপকারিতাঃ',
            'landing_benefits_subtitle' => 'রান্নাঘরের গ্যাস সমস্যা চিরতরে দূর করতে এই ডিভাইস কেন সেরা',
            'landing_reviews_title' => 'গ্রাহকদের আস্থা ও ফিডব্যাক',
            'landing_order_title' => 'অর্ডার করতে নিচের ফর্ম টি পূরণ করুন👇',
            'landing_order_btn_text' => 'আপনার অর্ডারটি কনফার্ম করুন',
            'landing_guarantee_note' => 'পণ্য হাতে পেয়ে চেক করে সম্পূর্ণ মূল্য পরিশোধ করতে পারবেন।',
        ]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        $path = ltrim($this->logo, '/');

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

    public function getFaviconUrlAttribute(): ?string
    {
        if (! $this->favicon) {
            return null;
        }

        if (str_starts_with($this->favicon, 'http://') || str_starts_with($this->favicon, 'https://')) {
            return $this->favicon;
        }

        $path = ltrim($this->favicon, '/');

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
