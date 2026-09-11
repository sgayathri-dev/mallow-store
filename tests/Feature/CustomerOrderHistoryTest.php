<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_customer_order_history_by_email(): void
    {
        $customer = Customer::factory()->create(['email' => 'jane@example.com']);
        Order::factory()->create(['customer_id' => $customer->id]);

        $response = $this->getJson('/api/customers/jane@example.com/orders');

        $response->assertStatus(200);
        $response->assertJsonPath('customer.email', 'jane@example.com');
        $response->assertJsonCount(1, 'orders');
    }
}