<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookingSource;

class BookingSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookingSource::factory()->create([
            'tenant_id' => 1,
            'name' => 'Booking.com',
        ]);
        BookingSource::factory()->create([
            'tenant_id' => 1,
            'name' => 'Individual',
        ]);
    }
}
