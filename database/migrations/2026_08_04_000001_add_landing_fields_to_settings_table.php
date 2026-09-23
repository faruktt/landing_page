<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('landing_announcement')->nullable();
            $table->string('landing_badge_1')->nullable();
            $table->string('landing_badge_2')->nullable();
            $table->string('landing_badge_3')->nullable();
            $table->string('landing_offer_title')->nullable();
            $table->string('landing_offer_subtitle')->nullable();
            $table->string('landing_benefits_title')->nullable();
            $table->string('landing_benefits_subtitle')->nullable();
            $table->string('landing_reviews_title')->nullable();
            $table->string('landing_order_title')->nullable();
            $table->string('landing_order_btn_text')->nullable();
            $table->string('landing_guarantee_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'landing_announcement',
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
            ]);
        });
    }
};
