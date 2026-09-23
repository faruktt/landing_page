<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Support\Delivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $customersData = [
            ['name' => 'রাকিবুল হাসান', 'phone' => '01711111111', 'address' => 'বাড়ি ১২, রোড ৫, ধানমন্ডি', 'district' => 'ঢাকা'],
            ['name' => 'মোহাম্মদ ইমরান', 'phone' => '01822222222', 'address' => 'আগ্রাবাদ বাণিজ্যিক এলাকা', 'district' => 'চট্টগ্রাম'],
            ['name' => 'শাহানা বেগম', 'phone' => '01933333333', 'address' => 'মিরপুর ১০', 'district' => 'ঢাকা'],
            ['name' => 'কামরুল হাসান', 'phone' => '01644444444', 'address' => 'জিইসি মোড়', 'district' => 'চট্টগ্রাম'],
            ['name' => 'তানভীর রহমান', 'phone' => '01555555555', 'address' => 'উত্তরা সেক্টর ৭', 'district' => 'ঢাকা'],
            ['name' => 'ফারহানা ইয়াসমিন', 'phone' => '01966666666', 'address' => 'শাহেব বাজার', 'district' => 'রাজশাহী'],
        ];

        $customers = collect($customersData)->map(fn ($data) => Customer::updateOrCreate(['phone' => $data['phone']], $data));

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        $orderPlan = [
            ['customer' => 0, 'product' => 'bunon-premium-panjabi', 'qty' => 1, 'size' => 'L', 'color' => 'কালো', 'daysAgo' => 0, 'status' => 'pending'],
            ['customer' => 1, 'product' => 'bunon-pro-buds', 'qty' => 1, 'color' => 'Black', 'daysAgo' => 0, 'status' => 'confirmed'],
            ['customer' => 2, 'product' => 'seedless-dates', 'qty' => 2, 'daysAgo' => 1, 'status' => 'delivered'],
            ['customer' => 3, 'product' => 'bunon-cotton-tshirt', 'qty' => 2, 'size' => 'M', 'color' => 'সাদা', 'daysAgo' => 2, 'status' => 'shipped'],
            ['customer' => 4, 'product' => 'bunon-fast-charger', 'qty' => 1, 'color' => 'White', 'daysAgo' => 2, 'status' => 'delivered'],
            ['customer' => 5, 'product' => 'bunon-organic-honey', 'qty' => 1, 'daysAgo' => 3, 'status' => 'delivered'],
            ['customer' => 0, 'product' => 'bunon-pro-buds', 'qty' => 1, 'color' => 'Blue', 'daysAgo' => 4, 'status' => 'cancelled'],
            ['customer' => 1, 'product' => 'bunon-premium-panjabi', 'qty' => 1, 'size' => 'XL', 'color' => 'নেভি ব্লু', 'daysAgo' => 5, 'status' => 'delivered'],
            ['customer' => 2, 'product' => 'seedless-dates', 'qty' => 3, 'daysAgo' => 6, 'status' => 'delivered'],
            ['customer' => 4, 'product' => 'bunon-cotton-tshirt', 'qty' => 1, 'size' => 'L', 'color' => 'গ্রে মেলাঞ্জ', 'daysAgo' => 7, 'status' => 'delivered'],
        ];

        foreach ($orderPlan as $plan) {
            $customer = $customers[$plan['customer']];
            $product = $products->firstWhere('slug', $plan['product']);
            if (! $product) {
                continue;
            }

            $unitPrice = (float) ($product->sale_price ?? $product->regular_price);
            $qty = $plan['qty'];
            $subtotal = $unitPrice * $qty;
            $delivery = Delivery::chargeFor($customer->district);
            $total = $subtotal + $delivery;
            $createdAt = now()->subDays($plan['daysAgo'])->subHours(rand(0, 20));

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'BN-'.strtoupper(Str::random(6)),
                'customer_name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'district' => $customer->district,
                'payment_method' => collect(['cod', 'bkash', 'nagad'])->random(),
                'subtotal' => $subtotal,
                'delivery_charge' => $delivery,
                'total' => $total,
                'status' => $plan['status'],
            ]);
            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'size' => $plan['size'] ?? null,
                'color' => $plan['color'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);
        }
    }
}
