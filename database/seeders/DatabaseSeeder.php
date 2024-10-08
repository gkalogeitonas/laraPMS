<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Booking;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PropertySeeder::class,
            CustomerSeeder::class,
            BookingStatusSeeder::class,
            BookingSourceSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
