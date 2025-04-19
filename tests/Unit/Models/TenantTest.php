<?php

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\BookingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('can have many Properties', function() {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create properties associated with the tenant
    $properties = Property::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's properties relationship returns the correct properties
    expect($tenant->properties)->toHaveCount(3);
    expect($tenant->properties->pluck('id')->toArray())->toEqual($properties->pluck('id')->toArray());
});


it('can have many rooms', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    $user = createAsUserWithActiveTenant($tenant);

    // Create rooms associated with the tenant
    $rooms = Room::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's rooms relationship returns the correct rooms
    expect($tenant->rooms)->toHaveCount(3);
    expect($tenant->rooms->pluck('id')->toArray())->toEqual($rooms->pluck('id')->toArray());
});

it('can have many customers', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create customers associated with the tenant
    $customers = Customer::factory()->count(3)->create(['tenant_id' => $tenant->id]);
    $user = createAsUserWithActiveTenant($tenant);
    // Assert that the tenant's customers relationship returns the correct customers
    expect($tenant->customers)->toHaveCount(3);
    expect($tenant->customers->pluck('id')->toArray())->toEqual($customers->pluck('id')->toArray());
});

it('can have Bookings', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();
    $user = createAsUserWithActiveTenant($tenant);

    // Create a room associated with the tenant
    $room = Room::factory()->create(['tenant_id' => $tenant->id]);

    // Create a booking associated with the tenant
    $booking = Booking::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
    ]);

    // Assert that the tenant's bookings relationship returns the correct bookings
    expect($tenant->bookings)->toHaveCount(1);
    expect($tenant->bookings->first()->id)->toEqual($booking->id);
});

it('can have many Booking statuses', function () {
    $data = createUserWithTenant();
    $owner = $data['user'];
    $tenant = $data['tenant'];

    // Create booking statuses associated with the tenant
    $bookingStatuses = BookingStatus::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's booking statuses relationship returns the correct booking statuses
    expect($tenant->bookingStatuses)->toHaveCount(3);
    expect($tenant->bookingStatuses->pluck('id')->toArray())->toEqual($bookingStatuses->pluck('id')->toArray());
});
