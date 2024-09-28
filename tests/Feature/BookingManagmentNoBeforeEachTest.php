<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Property;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('a user without tenant can not view Bookings in  bookings index', function () {
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

    $response = get(route('bookings.index'));
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings', 0)
    );
});
