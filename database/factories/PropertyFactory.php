<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
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

        // Create a user and associate it with the tenant
        $user = User::factory()->create();
        $user->tenants()->attach($tenant->id);

        return [
            'tenant_id' => $tenant->id,
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'type' => 'hotel',
        ];
    }
}
