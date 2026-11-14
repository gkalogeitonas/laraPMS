<?php

namespace Database\Seeders;

use App\Models\BookingStatus;
use Illuminate\Database\Seeder;

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
