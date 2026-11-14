<?php

namespace Database\Factories;

use App\Models\BookingSource;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingSource>
 */
class BookingSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory()->create(),
            'name' => $this->faker->name,
        ];
    }
}
