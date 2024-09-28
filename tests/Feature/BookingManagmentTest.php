<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Booking;
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
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post(route('bookings.store'), $bookingData);

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
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post(route('bookings.store'), $bookingData);

    $response->assertStatus(403);

    assertDatabaseMissing('bookings', $bookingData);
});

test('a booking requires a room', function () {
    $bookingData = [
        'name' => 'Booking Name',
        'customer_id' => $this->customer->id,
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post(route('bookings.store'), $bookingData);
    $response->assertSessionHasErrors('room_id');
});

test('a booking requires end date to be after start date', function () {
    $bookingData = [
        'room_id' => $this->room->id,
        'name' => 'Booking Name',
        'customer_id' => $this->customer->id,
        'check_in' => '2024-01-10',
        'check_out' => '2024-01-01',
        'total_guests' => 2,
        'price' => 100.00,
        'status' => 'pending',
    ];

    $response = post(route('bookings.store'), $bookingData);
    $response->assertSessionHasErrors('check_out');
});

test('a user can update a booking', function () {
    $booking = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = patch(route('bookings.update', $booking->id), [
        'name' => 'New Name',
        'room_id' => $booking->room->id,
        'customer_id' => $booking->customer,
        'check_in' => $booking->check_in,
        'check_out' => $booking->check_out,
        'total_guests' => $booking->total_guests,
        'price' => $booking->price,
        'status' => $booking->status,
    ]);

    $response->assertRedirect(route('bookings.index'))->assertSessionHas('success', 'Booking updated.');

    $booking->refresh();

    expect($booking->name)->toBe('New Name');
});

it('prevents non tenant members to update a booking', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $booking = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = patch('/bookings/' . $booking->id, [
        'name' => 'New Name',
        'room_id' => $booking->room->id,
        'customer_id' => $booking->customer,
        'check_in' => $booking->check_in,
        'check_out' => $booking->check_out,
        'total_guests' => $booking->total_guests,
        'price' => $booking->price,
        'status' => $booking->status,
    ]);

    $response->assertStatus(403);
});

test('a user can delete a booking', function () {
    $booking = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = delete(route('bookings.destroy', $booking->id));

    $response->assertRedirect(route('bookings.index'))->assertSessionHas('success', 'Booking deleted.');

    expect(Booking::count())->toBe(0);
});

it('prevents non tenant members to delete a booking', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $booking = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = delete('/bookings/' . $booking->id);

    $response->assertStatus(403);
});

it('can view the bookings index', function () {
    $response = get(route('bookings.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
    );
});

it('can view the bookings in bookings Index', function () {
    $booking = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = get(route('bookings.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking->id)
    );
});

it('can not view other tenant bookings in bookings Index', function () {
    $otherTenant = Tenant::factory()->create();
    $otherRoom = Room::factory()->create(['tenant_id' => $otherTenant->id]);
    $otherBooking = Booking::factory()->create([
        'tenant_id' => $otherTenant->id,
        'room_id' => $otherRoom->id,
    ]);

    $response = get(route('bookings.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 0)
    );
});


it('can filter booking by dates', function () {
    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
    ]);

    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'check_in' => '2024-02-15',
        'check_out' => '2024-02-20',
    ]);

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $booking3 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'check_in' => '2024-01-05',
        'check_out' => '2024-01-11',
    ]);

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 2)
        ->where('bookings.data.0.id', $booking1->id)
        ->where('bookings.data.1.id', $booking3->id)
        ->missing('bookings.data.2')
    );

});
