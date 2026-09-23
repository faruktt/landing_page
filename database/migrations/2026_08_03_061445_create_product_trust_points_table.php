<?php

use App\Models\Product;
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
        Schema::create('product_trust_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaults = [
            'সরাসরি সোর্স থেকে ফ্রেশ প্রোডাক্ট সংগ্রহ করে আপনার হাতে পৌঁছে দিই।',
            'কোয়ালিটি চেক করার পরই প্রতিটি প্রোডাক্ট প্যাকেজিং করা হয়।',
            'অর্ডার করতে অগ্রিম ১ টাকাও পেমেন্ট করতে হবে না।',
            'সারা বাংলাদেশে ক্যাশ অন হোম ডেলিভারী সুবিধা।',
            'পণ্য হাতে পাওয়ার পর প্রয়োজনে ফেরত/রিফান্ড নেওয়া যাবে।',
            'প্রত্যাশা অনুযায়ী প্রোডাক্ট না পেলে সাথে সাথে রিটার্ন সুবিধা থাকছে।',
        ];

        foreach (Product::all() as $product) {
            foreach ($defaults as $index => $text) {
                $product->trustPoints()->create(['text' => $text, 'sort_order' => $index]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_trust_points');
    }
};
