<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoProductsSeeder extends Seeder
{
    public function run(): void
    {
        $fashion = Category::updateOrCreate(['slug' => 'fashion'], ['name' => 'ফ্যাশন']);
        $organic = Category::updateOrCreate(['slug' => 'organic-food'], ['name' => 'অর্গানিক ফুড']);
        $electronics = Category::updateOrCreate(['slug' => 'electronics'], ['name' => 'ইলেকট্রনিক্স']);

        $panjabi = Product::updateOrCreate(
            ['slug' => 'bunon-premium-panjabi'],
            [
                'category_id' => $fashion->id,
                'name' => 'BUNON Premium Panjabi',
                'template' => 1,
                'short_description' => 'আভিজাত্যের প্রতীক',
                'description' => '১০০% খাঁটি কটন ফেব্রিক, হাতের কাজের এমব্রয়ডারি আর পারফেক্ট ফিটিং। বিশেষ উৎসব হোক বা অফিস, প্রতিটি মুহূর্তে আপনাকে করে তুলবে আলাদা।',
                'regular_price' => 2700,
                'sale_price' => 1590,
                'free_gift_text' => 'ফ্রি গিফট: ম্যাচিং পকেট স্কয়ার',
                'sizes' => ['M', 'L', 'XL', 'XXL', '3XL'],
                'colors' => ['কালো', 'মেরুন', 'নেভি ব্লু', 'সাদা'],
                'stock' => 50,
                'status' => 'active',
            ]
        );
        $panjabi->benefits()->delete();
        $panjabi->benefits()->createMany([
            ['title' => 'নিঃশ্বাস নেওয়া ফেব্রিক', 'text' => 'গরমেও আরামদায়ক, বাতাস চলাচল করে এমন প্রিমিয়াম কটন কাপড়।', 'sort_order' => 0],
            ['title' => 'নিখুঁত ফিনিশিং', 'text' => 'দক্ষ কারিগরের হাতের কাজ, প্রতিটি সেলাইয়ে নিখুঁত মান নিশ্চিত।', 'sort_order' => 1],
            ['title' => 'পারফেক্ট ফিটিং', 'text' => 'সব সাইজে পাওয়া যায়, সাইজ চার্ট দেখে সহজেই বেছে নিন।', 'sort_order' => 2],
        ]);
        $panjabi->reviews()->delete();
        $panjabi->reviews()->createMany([
            ['customer_name' => 'রাকিবুল হাসান', 'location' => 'ঢাকা', 'rating' => 5, 'comment' => 'ফেব্রিক কোয়ালিটি অসাধারণ, ছবিতে যেমন দেখেছি ঠিক তেমনই পেয়েছি।', 'sort_order' => 0],
            ['customer_name' => 'মোহাম্মদ ইমরান', 'location' => 'চট্টগ্রাম', 'rating' => 4, 'comment' => 'ক্যাশ অন ডেলিভারি থাকায় নিশ্চিন্তে অর্ডার করেছিলাম। সাইজ পারফেক্ট ফিট হয়েছে।', 'sort_order' => 1],
        ]);

        $tshirt = Product::updateOrCreate(
            ['slug' => 'bunon-cotton-tshirt'],
            [
                'category_id' => $fashion->id,
                'name' => 'BUNON Cotton T-Shirt',
                'template' => 1,
                'short_description' => 'আরামদায়ক প্রতিদিনের পরিধান',
                'description' => '১০০% কম্বড কটন দিয়ে তৈরি, নরম ও আরামদায়ক। প্রতিদিনের ক্যাজুয়াল লুকের জন্য পারফেক্ট।',
                'regular_price' => 950,
                'sale_price' => 650,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['কালো', 'সাদা', 'গ্রে মেলাঞ্জ'],
                'stock' => 120,
                'status' => 'active',
            ]
        );
        $tshirt->benefits()->delete();
        $tshirt->benefits()->createMany([
            ['title' => 'নরম কটন ফেব্রিক', 'text' => 'সারাদিন আরামে পরার জন্য বিশেষভাবে তৈরি।', 'sort_order' => 0],
            ['title' => 'রঙ নষ্ট হয় না', 'text' => 'বারবার ওয়াশেও রঙ থাকবে অটুট।', 'sort_order' => 1],
        ]);
        $tshirt->reviews()->delete();
        $tshirt->reviews()->createMany([
            ['customer_name' => 'সাকিব আল হাসান', 'location' => 'খুলনা', 'rating' => 5, 'comment' => 'কাপড়ের মান অনেক ভালো, দামের তুলনায় সেরা।', 'sort_order' => 0],
        ]);

        $dates = Product::updateOrCreate(
            ['slug' => 'seedless-dates'],
            [
                'category_id' => $organic->id,
                'name' => 'বিচি মুক্ত সিডলেস খেজুর',
                'template' => 2,
                'short_description' => 'ফ্রেশ, সুস্বাদু (বিচিছাড়া) খেজুর',
                'description' => 'এই খেজুরে রয়েছে প্রচুর পরিমাণে ফাইবার, আয়রন, ভিটামিন এবং ম্যাগনেসিয়ামসহ নানান পুষ্টিগুণ।',
                'regular_price' => 1100,
                'sale_price' => 900,
                'stock' => 200,
                'status' => 'active',
            ]
        );
        $dates->benefits()->delete();
        $dates->benefits()->createMany([
            ['text' => 'হার্টকে ক্ষতি থেকে রক্ষা করতে সাহায্য করে এমন অ্যান্টিঅক্সিডেন্ট ও পটাশিয়াম রয়েছে।', 'sort_order' => 0],
            ['text' => 'রক্তস্বল্পতায় ভোগা রোগী প্রতিদিন খেতে পারেন, দৈনিক প্রয়োজনীয় আয়রনের প্রায় ১১% পূরণ করে।', 'sort_order' => 1],
            ['text' => 'উচ্চ ফাইবার হজমশক্তি উন্নত করে এবং কোষ্ঠকাঠিন্য প্রতিরোধে সহায়তা করে।', 'sort_order' => 2],
        ]);
        $dates->reviews()->delete();
        $dates->reviews()->createMany([
            ['customer_name' => 'শাহানা বেগম', 'location' => 'ঢাকা', 'rating' => 5, 'comment' => 'খেজুরগুলো একদম ফ্রেশ আর নরম ছিল, বাচ্চারাও খুব পছন্দ করেছে।', 'sort_order' => 0],
            ['customer_name' => 'কামরুল হাসান', 'location' => 'চট্টগ্রাম', 'rating' => 4, 'comment' => 'বিচি নাই বলে খেতে অনেক সহজ, স্বাদও মিষ্টি আর তাজা।', 'sort_order' => 1],
        ]);

        $honey = Product::updateOrCreate(
            ['slug' => 'bunon-organic-honey'],
            [
                'category_id' => $organic->id,
                'name' => 'BUNON অর্গানিক মধু',
                'template' => 2,
                'short_description' => '১০০% খাঁটি সুন্দরবনের মধু',
                'description' => 'কোনো ভেজাল ছাড়া প্রাকৃতিক মধু, সরাসরি সুন্দরবন থেকে সংগ্রহ করা হয়।',
                'regular_price' => 800,
                'sale_price' => 650,
                'stock' => 150,
                'status' => 'active',
            ]
        );
        $honey->benefits()->delete();
        $honey->benefits()->createMany([
            ['text' => 'রোগ প্রতিরোধ ক্ষমতা বৃদ্ধিতে সহায়ক।', 'sort_order' => 0],
            ['text' => 'প্রাকৃতিক এনার্জির চমৎকার উৎস।', 'sort_order' => 1],
        ]);
        $honey->reviews()->delete();
        $honey->reviews()->createMany([
            ['customer_name' => 'ফারহানা ইয়াসমিন', 'location' => 'রাজশাহী', 'rating' => 5, 'comment' => 'খুবই খাঁটি মধু, আগে যেটা কিনতাম তার থেকে অনেক ভালো।', 'sort_order' => 0],
        ]);

        $buds = Product::updateOrCreate(
            ['slug' => 'bunon-pro-buds'],
            [
                'category_id' => $electronics->id,
                'name' => 'BUNON Pro Buds',
                'template' => 3,
                'short_description' => 'যেখানেই যান, সাউন্ড থাকবে সাথে',
                'description' => 'সত্যিকারের ওয়্যারলেস ফ্রিডম, অ্যাক্টিভ নয়েজ ক্যান্সেলেশন, ৩০+ ঘন্টা ব্যাটারি ব্যাকআপ আর ক্রিস্টাল ক্লিয়ার সাউন্ড।',
                'regular_price' => 3050,
                'sale_price' => 1890,
                'colors' => ['Black', 'White', 'Blue'],
                'stock' => 100,
                'status' => 'active',
            ]
        );
        $buds->specs()->delete();
        $buds->specs()->createMany([
            ['label' => 'ব্যাটারি ব্যাকআপ', 'value' => '৩০ ঘন্টা পর্যন্ত', 'sort_order' => 0],
            ['label' => 'নয়েজ ক্যান্সেলেশন', 'value' => 'ANC Active', 'sort_order' => 1],
            ['label' => 'ব্লুটুথ ভার্সন', 'value' => '5.3', 'sort_order' => 2],
            ['label' => 'ওয়াটার রেজিস্ট্যান্স', 'value' => 'IPX5', 'sort_order' => 3],
        ]);
        $buds->reviews()->delete();
        $buds->reviews()->createMany([
            ['customer_name' => 'তানভীর রহমান', 'location' => 'ঢাকা', 'rating' => 5, 'comment' => 'সাউন্ড কোয়ালিটি এই দামের তুলনায় অসাধারণ। ব্যাটারি ব্যাকআপও ভালো।', 'sort_order' => 0],
            ['customer_name' => 'মেহেদী হাসান', 'location' => 'চট্টগ্রাম', 'rating' => 4, 'comment' => 'নয়েজ ক্যান্সেলেশন ফিচারটা সত্যিই কাজ করে।', 'sort_order' => 1],
        ]);

        $charger = Product::updateOrCreate(
            ['slug' => 'bunon-fast-charger'],
            [
                'category_id' => $electronics->id,
                'name' => 'BUNON 65W Fast Charger',
                'template' => 3,
                'short_description' => 'মিনিটেই ফুল চার্জ',
                'description' => 'GaN টেকনোলজিতে তৈরি কমপ্যাক্ট চার্জার, সব ডিভাইসের জন্য নিরাপদ ও দ্রুত চার্জিং।',
                'regular_price' => 1800,
                'sale_price' => 1290,
                'colors' => ['Black', 'White'],
                'stock' => 80,
                'status' => 'active',
            ]
        );
        $charger->specs()->delete();
        $charger->specs()->createMany([
            ['label' => 'আউটপুট', 'value' => '৬৫ ওয়াট', 'sort_order' => 0],
            ['label' => 'পোর্ট', 'value' => '2x USB-C, 1x USB-A', 'sort_order' => 1],
            ['label' => 'টেকনোলজি', 'value' => 'GaN', 'sort_order' => 2],
        ]);
        $charger->reviews()->delete();
        $charger->reviews()->createMany([
            ['customer_name' => 'ইমতিয়াজ আহমেদ', 'location' => 'সিলেট', 'rating' => 5, 'comment' => 'সাইজে ছোট কিন্তু চার্জিং স্পিড অসাধারণ।', 'sort_order' => 0],
        ]);
    }
}
