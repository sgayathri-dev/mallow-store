<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_endpoint_returns_products_below_threshold(): void
    {
        Product::factory()->create(['stock_on_hand' => 2]);
        Product::factory()->create(['stock_on_hand' => 50]);

        $response = $this->getJson('/api/products/low-stock?threshold=10');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}