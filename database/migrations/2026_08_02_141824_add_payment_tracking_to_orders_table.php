<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('total');
            $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('due_amount');
        });

        DB::table('orders')->update(['due_amount' => DB::raw('total')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'due_amount', 'payment_status']);
        });
    }
};
