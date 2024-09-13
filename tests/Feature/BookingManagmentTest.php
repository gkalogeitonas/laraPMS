<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Customer;
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

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create();
    $this->user->tenants()->attach($this->tenant);
    $this->actingAs($this->user);
    $this->user->setActiveTenant($this->tenant);

    $this->room = Room::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
});

it('can create a booking', function () {


    $bookingData = [
        'room_id' => $this->room->id,
        'name' => 'Booking Name',
        'customer_id' => $this->customer->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post('/bookings', $bookingData);

    $response->assertRedirect(route('bookings.index'))->assertSessionHas('success', 'Booking created.');

    assertDatabaseHas('bookings', $bookingData);
});

it('prevents non tenant members to create a booking', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $bookingData = [
        'room_id' => $this->room->id,
        'name' => 'Booking Name',
        'customer_id' => $this->customer->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post('/bookings', $bookingData);

    $response->assertStatus(403);

    assertDatabaseMissing('bookings', $bookingData);
});

test('a booking requires a room', function () {
    $bookingData = [
        'name' => 'Booking Name',
        'customer_id' => $this->customer->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post('/bookings', $bookingData);
    $response->assertSessionHasErrors('room_id');
});
