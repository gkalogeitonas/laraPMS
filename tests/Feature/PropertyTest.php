<?php

// tests/Feature/PropertyTest.php
use App\Models\User;
use App\Models\Property;



// it('a user can create a property', function () {
//     $tenant = Tenant::factory()->create();
//     $user = User::factory()->create(['tenant_id' => $tenant->id]);

//     $response = $this->actingAs($user)->post('/properties', [
//         'name' => 'chrisanthi-studios',
//         'address' => '123 Main St',
//         'description' => 'Beautiful studios.',
//         'type' => 'bnb',
//         'tenant_id' => $tenant->id
//     ]);

//     $response->assertStatus(200);
//     $property = Property::where('name', 'chrisanthi-studios')->first();
//     $this->assertNotNull($property);
//     $this->assertEquals('123 Main St', $property->address);
//     $this->assertEquals('Beautiful studios.', $property->description);
//     $this->assertEquals('bnb', $property->type);
//     $this->assertEquals($tenant->id, $property->tenant_id);
// });

// it('a user can only manage their properties', function () {
//     $user = User::factory()->create();
//     $otherUser = User::factory()->create();
//     $property = Property::factory()->create(['user_id' => $otherUser->id]);

//     actingAs($user);
//     $response = get("/properties/{$property->id}");
//     $response->assertStatus(403);
// });
