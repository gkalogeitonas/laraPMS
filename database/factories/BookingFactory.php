<?php

namespace Database\Factories;

use Carbon\Carbon;
use \App\Models\Room;
use \App\Models\Tenant;
use \App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = Carbon::instance($this->faker->dateTimeBetween('-1 month', '+1 month'));
        $end_date = $start_date->copy()->addDays($this->faker->numberBetween(1, 30));
        return [
            'room_id' => Room::factory(),
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'customer_id' => Customer::factory(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_guests' => $this->faker->randomNumber(),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'status' => "pending",
        ];
    }
}
