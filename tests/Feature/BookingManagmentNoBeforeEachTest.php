<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Property;
use App\Models\BookingStatus;
use App\Models\BookingSource;
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


it('can see only booking statuses and booking sources from own tenant in booking form', function () {


    // Create booking statuses and booking sources for another tenant
    $otherTenant = Tenant::factory()->create();
    $otherBookingStatus = BookingStatus::factory()->create(['tenant_id' => $otherTenant->id]);
    $otherBookingSource = BookingSource::factory()->create(['tenant_id' => $otherTenant->id]);


    // Create a tenant and a user
    $data = createUserWithTenant();
    $tenant = $data['tenant'];
    $user = $data['user'];

    // Create booking statuses and booking sources for the tenant
    $tenantBookingStatus = BookingStatus::factory()->create(['tenant_id' => $tenant->id]);
    $tenantBookingSource = BookingSource::factory()->create(['tenant_id' => $tenant->id]);

    // Log in as the user
    actingAs($user);

    // Access the booking form
    $response = $this->get(route('bookings.create'));

    // Assert that only the booking statuses and booking sources from the user's tenant are visible
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Create')
        ->has('bookingStatuses', 1)
        ->where('bookingStatuses.0.id', $tenantBookingStatus->id)
        ->where('bookingStatuses.0.name', $tenantBookingStatus->name)
        ->has('BookingSources', 1)
        ->where('BookingSources.0.id', $tenantBookingSource->id)
        ->where('BookingSources.0.name', $tenantBookingSource->name)
        ->whereNot('bookingStatuses.0.id', $otherBookingStatus->id)
        ->whereNot('BookingSources.0.id', $otherBookingSource->id)
    );
});
