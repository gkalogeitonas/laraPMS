<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => 1, // Default to the first hotel
            'user_id' => 1, // Default to the first user
            'number' => $this->faker->unique()->numberBetween(100, 500),
            'type' => $this->faker->randomElement(['single', 'double', 'triple', 'apartment']),
            'status' => $this->faker->randomElement(['available', 'booked', 'out of service']),
        ];
    }
}
