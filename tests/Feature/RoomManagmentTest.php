<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows the owner to create a room', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    actingAs($owner);

    $response = post(route('properties.rooms.store', $property), [
        'name' => 'Room 101',
        'description' => 'A cozy room',
        'type' => 'single',
    ]);

    //$response->assertRedirect(route('properties.show', $property));
    assertDatabaseHas('rooms', ['name' => 'Room 101']);
});

it('prevents a non-owner from creating a room', function () {
    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);

    actingAs($nonOwner);

    $response = post(route('properties.rooms.store', $property), [
        'name' => 'Room 101',
        'description' => 'A cozy room',
        'price' => 100,
        'status' => 'available',
    ]);

    $response->assertStatus(403);
});

it('allows the owner to update a room', function () {
    // $owner = User::factory()->create();
    // $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);
    // $room = Room::factory()->create(['property_id' => $property->id]);

    // actingAs($owner);

    // $response = patch(route('properties.rooms.update', [$property, $room]), [
    //     'name' => 'Updated Room Name',
    //     'description' => 'Updated description',
    //     'price' => 150,
    //     'status' => 'occupied',
    // ]);

    // $response->assertRedirect(route('properties.show', $property));
    // assertDatabaseHas('rooms', ['name' => 'Updated Room Name']);
});

it('prevents a non-owner from updating a room', function () {
    // $owner = User::factory()->create();
    // $nonOwner = User::factory()->create();
    // $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);
    // $room = Room::factory()->create(['property_id' => $property->id]);

    // actingAs($nonOwner);

    // $response = patch(route('properties.rooms.update', [$property, $room]), [
    //     'name' => 'Updated Room Name',
    //     'description' => 'Updated description',
    //     'price' => 150,
    //     'status' => 'occupied',
    // ]);

    // $response->assertStatus(403);
});

it('allows the owner to delete a room', function () {
    // $owner = User::factory()->create();
    // $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);
    // $room = Room::factory()->create(['property_id' => $property->id]);

    // actingAs($owner);

    // $response = delete(route('properties.rooms.destroy', [$property, $room]));

    // $response->assertRedirect(route('properties.show', $property));
    // assertDatabaseMissing('rooms', ['id' => $room->id]);
});

it('prevents a non-owner from deleting a room', function () {
    // $owner = User::factory()->create();
    // $nonOwner = User::factory()->create();
    // $property = Property::factory()->create(['tenant_id' => $owner->tenant->id]);
    // $room = Room::factory()->create(['property_id' => $property->id]);

    // actingAs($nonOwner);

    // $response = delete(route('properties.rooms.destroy', [$property, $room]));

    // $response->assertStatus(403);
});


it('allows a user to view a room', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $user->tenant->id]);
    $room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $user->tenant->id
    ]);

    actingAs($user);

    $response = get(route('rooms.show', $room));

    $response->assertStatus(200);
    $response->assertSee($room->name);
    $response->assertSee($room->description);
    $response->assertSee($room->type);
});



it('allows a user to view rooms from the property show page', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $user->tenant->id]);
    $rooms = Room::factory()->count(3)->create([
        'property_id' => $property->id,
        'tenant_id' => $user->tenant->id
    ]);

    actingAs($user);

    $response = get(route('properties.show', $property));

    $response->assertStatus(200)
            ->assertSee($rooms[0]->name)
            ->assertSee($rooms[1]->name)
            ->assertSee($rooms[2]->name);

});
