<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreCustomerValidationTest extends TestCase
{
    public function test_customer_requires_name_and_email(): void
    {
        $response = $this->postJson('/api/customers', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'email',
        ]);
    }
}