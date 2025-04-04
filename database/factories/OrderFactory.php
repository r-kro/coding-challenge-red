<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['connector', 'vpn_connection']),
            'status' => $this->faker->randomElement(['ordered', 'processing', 'completed']),
            'provider_order_id' => 'mock-' . $this->faker->unique()->uuid,
        ];
    }
}
