<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::factory()->count(2)->create([
            'tenant_id' => 1,
            'room_id' => 1,
        ]);
        Booking::factory()->count(2)->create([
            'tenant_id' => 1,
            'room_id' => 3,
        ]);
        Booking::factory()->count(2)->create([
            'tenant_id' => 1,
            'room_id' => 5,
        ]);
    }
}
