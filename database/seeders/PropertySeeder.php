<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // Create the first property with 8 rooms for user 2
        $property = Property::factory()->create([
            'user_id' => 2,
            'name' => 'Chrisanthi Studios',
        ]);

        Room::factory()->count(8)->create([
            'property_id' => $property->id,
            'user_id' => 2,
        ]);

        // Create 5 more properties with 10 rooms each for new users
        for ($j = 1; $j <= 5; $j++) {
            $user = User::factory()->create();

            $property = Property::factory()->create([
                'user_id' => $user->id,
            ]);

            Room::factory()->count(10)->create([
                'property_id' => $property->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
