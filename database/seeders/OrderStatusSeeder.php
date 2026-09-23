<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['key' => 'pending', 'label' => 'পেন্ডিং', 'color' => 'yellow', 'sort_order' => 1, 'is_default' => true],
            ['key' => 'confirmed', 'label' => 'কনফার্মড', 'color' => 'blue', 'sort_order' => 2, 'is_default' => false],
            ['key' => 'shipped', 'label' => 'শিপড', 'color' => 'indigo', 'sort_order' => 3, 'is_default' => false],
            ['key' => 'delivered', 'label' => 'ডেলিভারড', 'color' => 'emerald', 'sort_order' => 4, 'is_default' => false],
            ['key' => 'cancelled', 'label' => 'বাতিল', 'color' => 'red', 'sort_order' => 5, 'is_default' => false],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(['key' => $status['key']], $status);
        }
    }
}
