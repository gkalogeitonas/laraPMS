<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room; // Ensure this uses your actual Room model namespace

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::factory()->count(10)->create([
            'hotel_id' => 1, // Default to the first hotel
            'user_id' => 1, // Default to the first user
        ]);
    }
}
