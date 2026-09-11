<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create products
        Product::factory(10)->create();

        // Create customers
        Customer::factory(5)->create();

        // Create orders with items
        Customer::all()->each(function ($customer) {

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => 0,
                'tax' => 0,
                'grand_total' => 0,
            ]);

            $subtotal = 0;
            $tax = 0;

            OrderItem::factory(fake()->numberBetween(1, 4))
                ->create([
                    'order_id' => $order->id,
                ])
                ->each(function ($item) use (&$subtotal, &$tax) {
                    $subtotal += $item->line_subtotal;
                    $tax += $item->line_tax;
                });

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
            ]);
        });
    }
}