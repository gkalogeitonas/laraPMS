<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
        $user = User::factory()->create();
        return [
            'user_id' =>  $user->id,
            'tenant_id' => $user->tenant_id,
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'type' => 'hotel',
        ];
    }
}
