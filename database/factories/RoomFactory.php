<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use App\Models\User;

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
        $property = Property::factory()->create();
        return [
            'property_id' =>  $property,
            'tenant_id' =>  $property->tenant_id,
            'name' => 'Room ' . $this->faker->unique()->numberBetween(1, 100),
            'description' => $this->faker->paragraph,
            'status' => 'available',
            'type' => $this->faker->randomElement(['single', 'double', 'triple', 'apartment']),
        ];
    }
}

