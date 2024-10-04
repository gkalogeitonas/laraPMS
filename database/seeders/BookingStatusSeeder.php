<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookingStatus;

class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = config('booking.status');

        foreach ($statuses as $status) {
            BookingStatus::factory()->create([
                'name' => $status,
                'tenant_id' => 1,
            ]);
        }
    }
}
