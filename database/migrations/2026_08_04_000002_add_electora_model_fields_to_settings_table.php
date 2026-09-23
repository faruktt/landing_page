<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('landing_p1_title')->nullable();
            $table->string('landing_p1_badge_1')->nullable();
            $table->string('landing_p1_badge_2')->nullable();
            $table->string('landing_p1_offer_text')->nullable();
            $table->string('landing_p1_regular_price')->nullable();
            $table->string('landing_p1_sale_price')->nullable();
            $table->text('landing_p1_bullets')->nullable();
            $table->string('landing_p1_image_1')->nullable();
            $table->string('landing_p1_image_2')->nullable();

            $table->string('landing_p2_title')->nullable();
            $table->string('landing_p2_badge_1')->nullable();
            $table->string('landing_p2_badge_2')->nullable();
            $table->string('landing_p2_regular_price')->nullable();
            $table->string('landing_p2_sale_price')->nullable();
            $table->text('landing_p2_bullets')->nullable();
            $table->string('landing_p2_image')->nullable();

            $table->text('landing_benefits_list')->nullable();
            $table->string('landing_outlook_btn_text')->nullable();
            $table->string('landing_review_btn_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
