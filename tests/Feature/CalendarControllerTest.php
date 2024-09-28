<?php

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use App\Models\Tenant;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('renders the calendar with events and resources', function () {
    $data = createOwnerAndProperty();
    $owner = $data['owner'];
    $property = $data['property'];
    $tenant = $data['tenant'];

    // Create a room and a booking
	$room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id
    ]);
	$booking = Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id
    ]);

    actingAs($owner);
	// Make a request to the calendar index route
	$response = $this->get(route('calendar'));
    //dd($response);
	// Assert the response
	$response->assertStatus(200)
		->assertInertia(fn (Assert $page) => $page
			->component('Calendar')
			->has('events.data', 1)
			->has('resources.data', 1)
			->where('events.data.0.id', $booking->id)
			->where('resources.data.0.id', 'P' . $property->id)
			->where('resources.data.0.children.0.id', 'R' . $room->id)
		);
});

it('prevent renders  events and resources for other users', function () {
    $data = createOwnerAndProperty();
    $owner = $data['owner'];
    $property = $data['property'];
    $tenant = $data['tenant'];

    // Create a room and a booking
	$room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id
    ]);
	$booking = Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id
    ]);

    $otherTenant = Tenant::factory()->create();
    $nonOwner = User::factory()->create();
    $nonOwner->tenants()->attach($otherTenant->id);
    $nonOwner->setActiveTenant($otherTenant);
    actingAs($nonOwner);
	// Make a request to the calendar index route
	$response = $this->get(route('calendar'));
    //dd($response);
	// Assert the response
	$response->assertStatus(200)
		->assertInertia(fn (Assert $page) => $page
			->component('Calendar')
			->has('events.data', 0)
			->has('resources.data', 0)
		);
});


test('a user without tenant can not view the bookings or Resources on Calendar', function () {
    $owner = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $owner->tenants()->attach($tenant->id);
    $property = Property::factory()->create([
        'tenant_id' => $tenant->id
    ]);


    // Create a room and a booking
	$room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id
    ]);
	$booking = Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id
    ]);

    $nonOwner = User::factory()->create();
    actingAs($nonOwner);

    $response = get(route('calendar'));
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
        ->component('Calendar')
        ->has('events', 0)
        ->has('resources', 0)
    );
});




