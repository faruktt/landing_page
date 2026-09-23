<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_name')->nullable()->after('ip_address'); // 'steadfast', 'pathao', etc.
            $table->string('pathao_consignment_id')->nullable()->after('steadfast_delivery_status');
            $table->string('pathao_tracking_code')->nullable()->after('pathao_consignment_id');
            $table->string('pathao_delivery_status')->nullable()->after('pathao_tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_name',
                'pathao_consignment_id',
                'pathao_tracking_code',
                'pathao_delivery_status',
            ]);
        });
    }
};
