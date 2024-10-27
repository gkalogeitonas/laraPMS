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

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->BookingStatus = BookingStatus::factory()->create([
        'tenant_id' => $this->tenant->id
    ]);
    $this->BookingSource = BookingSource::factory()->create([
        'tenant_id' => $this->tenant->id
    ]);
    $this->user = User::factory()->create();
    $this->user->tenants()->attach($this->tenant);
    $this->actingAs($this->user);
    $this->user->setActiveTenant($this->tenant);

    $this->room = Room::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
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

    $response = get(route('bookings.index', ['check_in' => '2024-01-02', 'check_out' => '2024-01-07']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 2)
        ->where('bookings.data.0.id', $booking1->id)
        ->where('bookings.data.1.id', $booking3->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-12', 'check_out' => '2024-01-14']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 0)
    );

});


it('can filter booking by name', function () {
    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);

    $response = get(route('bookings.index', ['name' => $booking1->name]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['name' => $booking2->name]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking2->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['name' => 'random name']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );
});


it('can filter booking by both name and dates', function () {
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

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'name' => $booking1->name]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'name' => $booking2->name]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'name' => 'random name']));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );
});


it('can filter bookings by booking status', function () {
    $bookingStatus1 = BookingStatus::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $bookingStatus2 = BookingStatus::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_status_id' => $bookingStatus1->id,
    ]);

    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_status_id' => $bookingStatus2->id,
    ]);

    $response = get(route('bookings.index', ['booking_status_id' => $bookingStatus1->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['booking_status_id' => $bookingStatus2->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking2->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['booking_status_id' => 999]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );
});


it('can filter bookings by bookings status and dates', function () {
    $bookingStatus1 = BookingStatus::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $bookingStatus2 = BookingStatus::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_status_id' => $bookingStatus1->id,
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
    ]);


    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_status_id' => $bookingStatus2->id,
        'check_in' => '2024-02-15',
        'check_out' => '2024-02-20',
    ]);


    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'booking_status_id' => $bookingStatus1->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'booking_status_id' => $bookingStatus2->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'booking_status_id' => 999]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );
});


it('can filter bookings by booking source', function(){
    $bookingSource1 = BookingSource::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $bookingSource2 = BookingSource::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);


    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_source_id' => $bookingSource1->id,
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
    ]);

    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
        'booking_source_id' => $bookingSource2->id,
        'check_in' => '2024-02-15',
        'check_out' => '2024-02-20',
    ]);

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'booking_source_id' => $bookingSource1->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-03-10', 'booking_source_id' => $bookingSource1->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-01-10', 'booking_source_id' => $bookingSource2->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );

    $response = get(route('bookings.index', ['check_in' => '2024-01-01', 'check_out' => '2024-03-10', 'booking_source_id' => $bookingSource2->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking2->id)
        ->missing('bookings.data.2')
    );
});

it('can filter bookings by rooms', function(){
    $room1 = Room::factory()->create(['tenant_id' => $this->tenant->id]);
    $room2 = Room::factory()->create(['tenant_id' => $this->tenant->id]);

    $booking1 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $room1->id,
        'check_in' => '2024-01-01',
        'check_out' => '2024-01-10',
    ]);

    $booking2 = Booking::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $room2->id,
        'check_in' => '2024-02-15',
        'check_out' => '2024-02-20',
    ]);

    $response = get(route('bookings.index', ['room_id' => $room1->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking1->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['room_id' => $room2->id]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.id', $booking2->id)
        ->missing('bookings.data.2')
    );

    $response = get(route('bookings.index', ['room_id' => 999]));
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Bookings/Index')
        ->has('bookings')
        ->missing('bookings.data.0')
    );
});
