<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use App\Models\User;
use App\Models\Tenant;

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
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Create a property associated with the tenant
        $property = Property::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        return [
            'property_id' => $property->id,
            'tenant_id' => $property->tenant_id,
            'name' => 'Room ' . $this->faker->unique()->numberBetween(1, 100),
            'description' => $this->faker->paragraph,
            'type' => $this->faker->randomElement(config('room.types')),
        ];
    }
}

