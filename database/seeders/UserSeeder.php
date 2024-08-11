<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create the first user
        User::create([
            'id' => 1,
            'name' => 'gkalog',
            'email' => 'gkalog2@gmail.com',
            'password' => Hash::make('37014'),
        ]);

        // Create the second user
        User::create([
            'id' => 2,
            'name' => 'Chrisanthi Studios',
            'email' => 'info@chrisanthi-studios.gr',
            'password' => Hash::make('37014'),
        ]);
    }
}
