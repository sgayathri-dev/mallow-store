<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_successfully(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'testcustomer@example.com',
            'name' => 'Test Customer',
        ]);

        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 10,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);

        $response->assertJsonPath(
            'data.subtotal',
            '200.00'
        );

        $response->assertJsonPath(
            'data.tax',
            '20.00'
        );

        $response->assertJsonPath(
            'data.grand_total',
            '220.00'
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 8,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
        ]);
    }

    public function test_order_cannot_be_created_when_stock_is_insufficient(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'stocktest@example.com',
            'name' => 'Stock Test Customer',
        ]);

        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 10,
            'stock_on_hand' => 2,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => "Insufficient stock for product: {$product->name}",
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 2,
        ]);

        $this->assertDatabaseCount('orders', 0);
    }
}