<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first();

        if (!$product) {
            $product = Product::factory()->create();
        }

        $quantity = fake()->numberBetween(1, 5);

        $unitPrice = $product->price;

        $taxPercentage = $product->tax_percentage;

        $lineSubtotal = $quantity * $unitPrice;

        $lineTax = $lineSubtotal * ($taxPercentage / 100);

        $lineTotal = $lineSubtotal + $lineTax;

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_percentage' => $taxPercentage,
            'line_subtotal' => $lineSubtotal,
            'line_tax' => $lineTax,
            'line_total' => $lineTotal,
        ];
    }
}