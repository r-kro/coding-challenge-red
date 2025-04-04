<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MockProviderClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('provider.use_mock', true);
        putenv('PROVIDER_USE_MOCK=true');
    }

    public function test_order_is_created_by_mock_provider()
    {
        $payload = [
            'name' => 'Mock Test Order',
            'type' => 'connector',
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertCreated()
                 ->assertJsonFragment([
                     'name' => 'Mock Test Order',
                     'type' => 'connector',
                     'status' => 'ordered',
                 ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('Mock Test Order', $order->name);
        $this->assertEquals('ordered', $order->status);

        $this->assertStringStartsWith('mock-', $order->provider_order_id);
    }

    public function test_mock_provider_returns_completed_status()
    {
        $order = Order::factory()->create([
            'status' => 'ordered',
            'provider_order_id' => 'mock-1234',
        ]);

        // update status of order by command
        $this->artisan('orders:sync-status')
            ->expectsOutputToContain('Updated status for order')
            ->assertExitCode(0);

        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }
}
