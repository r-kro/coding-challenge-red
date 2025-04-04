<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order()
    {
        $payload = [
            'name' => 'Test Bestellung',
            'type' => 'connector',
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertCreated()
                 ->assertJsonStructure(['id', 'name', 'type', 'status'])
                 ->assertJsonFragment([
                     'name' => 'Test Bestellung',
                     'type' => 'connector',
                     'status' => 'ordered',
                 ]);

        $this->assertDatabaseHas('orders', ['name' => 'Test Bestellung']);
    }

    public function test_get_single_order()
    {
        $order = Order::factory()->create([
            'name' => 'VPN Bestellung',
            'type' => 'connector',
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertOk()->assertJsonFragment(['id' => $order->id]);
    }

    public function test_order_deletion_only_when_completed()
    {
        $order = Order::factory()->create(['status' => 'ordered']);

        $response = $this->deleteJson("/api/orders/{$order->id}");
        $response->assertStatus(400);

        $order->status = 'completed';
        $order->save();

        $response = $this->deleteJson("/api/orders/{$order->id}");
        $response->assertNoContent();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_filter_and_sort_orders()
    {
        Order::factory()->create(['name' => 'Test']);
        Order::factory()->create(['name' => 'Dummy']);

        $response = $this->getJson('/api/orders?sort=name');

        $response->assertOk();
        $names = array_column($response->json(), 'name');
        $this->assertEquals(['Dummy', 'Test'], $names);
    }
}
