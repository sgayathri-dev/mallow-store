<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_cannot_be_oversold(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 10,
            'stock_on_hand' => 1,
        ]);

        $service = app(OrderService::class);

        $orderData1 = [
            'customer_email' => $customer1->email,
            'customer_name'  => $customer1->name,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $orderData2 = [
            'customer_email' => $customer2->email,
            'customer_name'  => $customer2->name,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $successCount = 0;

        try {
            $service->createOrder($orderData1);
            $successCount++;
        } catch (\Throwable $e) {
            // Order failed
        }

        try {
            $service->createOrder($orderData2);
            $successCount++;
        } catch (\Throwable $e) {
            // Order failed
        }

        $this->assertEquals(1, $successCount);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 0,
        ]);

        $this->assertDatabaseCount('orders', 1);
    }
}