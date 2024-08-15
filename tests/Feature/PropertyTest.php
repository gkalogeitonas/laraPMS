<?php

// tests/Feature/PropertyTest.php
use App\Models\User;
use App\Models\Property;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;


it('only allows owner to show their property', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $user->id,
        'name' => 'Chrisanthi Studios',
    ]);

    actingAs($user);
    $response = get("/properties/{$property->id}");
    $response->assertOk();
    $response->assertSee('Chrisanthi Studios');
    actingAs($otherUser);
    $response = get("/properties/{$property->id}");
    $response->assertStatus(403);
});


it('allows a user to create a property', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = post('/properties', [
        'name' => 'chrisanthi-studios',
        'address' => '123 Main St',
        'description' => 'Beautiful studios.',
        'type' => 'bnb'
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('properties', ['name' => 'chrisanthi-studios']);
});

it('only allows owner to delete their property', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $otherUser->id]);

    actingAs($user);
    $response = delete("/properties/{$property->id}");
    $response->assertStatus(403);

    actingAs($otherUser);
    $response = delete("/properties/{$property->id}");
    $response->assertStatus(200);
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});

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
