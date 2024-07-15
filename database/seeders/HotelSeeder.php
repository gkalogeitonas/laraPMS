<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		DB::table('hotels')->insert([
			'name' => 'Chrisanthi-studios',
			'address' => 'Agios Prokopion Naxos',
			'phone' => '2285042729',
			'email' => 'info@chrisanthi-studios.gr',
			'manager' => 'Poppy',
			// Assuming you have a user_id column as discussed, you need to specify it.
			// Here, we're assuming the user with ID 1 is the owner. Adjust as necessary.
			'user_id' => 1,
			'created_at' => now(),
			'updated_at' => now(),
		]);
	}
}
