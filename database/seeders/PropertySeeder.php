<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class PropertySeeder extends Seeder
{
    public function run()
    {
        $tenant = Tenant::factory()->create(['name' => 'Chrisanthi Studios']);
        $myUser = User::create([
            'id' => 1,
            'name' => 'gkalog',
            'email' => 'gkalog2@gmail.com',
            'password' => Hash::make('37014'),
            'tenant_id' => $tenant->id,
        ]);
        echo "User created with id: " . $myUser->id . "\n";
        echo "Tenant created with id: " . $myUser->tenant_id . "\n";

        // Create the first property with 8 rooms for user 2
        $property = Property::factory()->create([
            'name' => 'Chrisanthi Studios',
            'tenant_id' => $myUser->tenant_id,
        ]);

        Room::factory()->count(8)->create([
            'property_id' => $property->id,
            'tenant_id' => $myUser->tenant_id,
        ]);

        // Create 5 more properties with 10 rooms each for new users
        for ($j = 1; $j <= 5; $j++) {
            $user = User::factory()->create();

            $property = Property::factory()->create([
                'tenant_id' => $user->tenant_id,
            ]);

            Room::factory()->count(10)->create([
                'property_id' => $property->id,
                'tenant_id' => $user->tenant_id,
            ]);
        }
    }
}
