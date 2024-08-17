<?php

// tests/Feature/PropertyTest.php
use App\Models\User;
use App\Models\Property;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;


it('only allows owner to show their tenants properties', function () {
    // Create an owner (user) and a property for the tenant associated with that user
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    // Create another user who is not the owner
    $nonOwner = User::factory()->create();

    // Acting as the owner, they should be able to see the property
    $this->actingAs($owner)
        ->get(route('properties.show', $property))
        ->assertStatus(200);

    // Acting as a non-owner, they should not be able to see the property
    $this->actingAs($nonOwner)
        ->get(route('properties.show', $property))
        ->assertStatus(403);
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

it('only allows user to delete their property', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $otherUser->tenant->id]);

    actingAs($user);
    $response = delete(route('properties.destroy', $property));
    $response->assertStatus(403);

    actingAs($otherUser);
    $response = delete(route('properties.destroy', $property));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});



it('only allows owner to update their properties', function () {

});
