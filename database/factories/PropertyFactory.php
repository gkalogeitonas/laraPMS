<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Tenant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
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
