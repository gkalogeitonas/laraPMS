<?php

// tests/Feature/PropertyTest.php
use App\Models\User;
use App\Models\Property;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;


it('owners can see a list of their properties', function () {
    // Create an owner (user) and a property for the tenant associated with that user
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    // Create another user who is not the owner
    $nonOwner = User::factory()->create();

    // Acting as the owner, they should be able to see the property
    $this->actingAs($owner)
        ->get(route('properties.index'))
        ->assertStatus(200)
        ->assertSee($property->name);

    // Acting as a non-owner, they should not be able to see the property
    $this->actingAs($nonOwner)
        ->get(route('properties.index'))
        ->assertStatus(200)
        ->assertDontSee($property->name);
});

it('only allows owner to show their tenants properties', function () {
    // Create an owner (user) and a property for the tenant associated with that user
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    // Create another user who is not the owner
    $nonOwner = User::factory()->create();

    // Acting as the owner, they should be able to see the property
    $this->actingAs($owner)
        ->get(route('properties.show', $property))
        ->assertStatus(200)
        ->assertSee($property->name);

    // Acting as a non-owner, they should not be able to see the property
    $this->actingAs($nonOwner)
        ->get(route('properties.show', $property))
        ->assertStatus(403);
});


it('allows a user to create a property and redirect to properties index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = post('/properties', [
        'name' => 'chrisanthi-studios',
        'address' => '123 Main St',
        'description' => 'Beautiful studios.',
        'type' => 'bnb'
    ]);

    //$response->assertOk();
    $this->assertDatabaseHas('properties', ['name' => 'chrisanthi-studios']);
    $response->assertRedirect(route('properties.index'));
    $response->assertSessionHas('success', 'Property created successfully.');
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
    //$response->assertStatus(200);
    $response->assertRedirect(route('properties.index'));
    $response->assertSessionHas('success', 'Property deleted successfully.');
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});



it('allows owner to update their properties', function () {
    // Create an owner (user) and a property for the tenant associated with that user
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    // Data to update the property
    $updateData = [
        'name' => 'Updated Property Name',
        'address' => 'Updated Property Address',
    ];

    // Acting as the owner, they should be able to update the property
    $this->actingAs($owner)
        ->patch(route('properties.update', $property), $updateData)
        ->assertStatus(302)
        ->assertRedirect(route('properties.index'));

    // Refresh the property instance and check if the update was successful
    $property->refresh();
    expect($property->name)->toBe('Updated Property Name');
    expect($property->address)->toBe('Updated Property Address');

});


it('Do not allows not owner to update a properties', function () {
    // Create an owner (user) and a property for the tenant associated with that user
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    // Create another user who is not the owner
    $nonOwner = User::factory()->create();

    // Data to update the property
    $updateData = [
        'name' => 'Updated Property Name',
        'address' => 'Updated Property Address',
    ];

    // Acting as a non-owner, they should not be able to update the property
    $this->actingAs($nonOwner)
        ->patch(route('properties.update', $property), $updateData)
        ->assertStatus(403);

    // Ensure the property was not updated by the non-owner
    $property->refresh();
    expect($property->name)->not->toBe('Updated Property Name');
    expect($property->address)->not->toBe('Updated Property Address');
});

it('a property requires a name', function () {
    $user = User::factory()->create();
    actingAs($user);

    $response = post('/properties', [
        'name' => '',
        'address' => '123 Main St',
        'description' => 'Beautiful studios.',
        'type' => 'bnb'
    ]);

    $response->assertSessionHasErrors('name');
});


