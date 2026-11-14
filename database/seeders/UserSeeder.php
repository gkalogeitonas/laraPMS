<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create the first user
        // User::create([
        //     'id' => 1,
        //     'name' => 'gkalog',
        //     'email' => 'gkalog2@gmail.com',
        //     'password' => Hash::make('37014'),
        //     'tenant_id' => Tenant::factory(),
        // ]);
    }
}
