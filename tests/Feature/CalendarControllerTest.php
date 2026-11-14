<?php

use App\Models\Booking;
use App\Models\BookingSource;
use App\Models\BookingStatus;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders the calendar with property groups, bookings and the window', function () {
    $this->travelTo('2026-03-10 09:00:00');

    $data = createOwnerAndProperty();
    $owner = $data['owner'];
    $property = $data['property'];
    $tenant = $data['tenant'];

    $room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
    ]);
    $status = BookingStatus::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Confirmed',
        'color' => '#16a34a',
    ]);
    $source = BookingSource::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Direct',
    ]);
    $booking = Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id,
        'name' => 'Jane Doe',
        'total_guests' => 3,
        'check_in' => '2026-03-12',
        'check_out' => '2026-03-15',
        'price' => 50,
        'booking_status_id' => $status->id,
        'booking_source_id' => $source->id,
    ]);

    actingAs($owner);

    $this->get(route('calendar'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->where('window', ['start' => '2026-03-08', 'days' => 30])
            ->has('groups', 1)
            ->where('groups.0.id', $property->id)
            ->where('groups.0.name', $property->name)
            ->has('groups.0.rooms', 1)
            ->where('groups.0.rooms.0', ['id' => $room->id, 'name' => $room->name])
            ->has('bookings', 1)
            ->where('bookings.0', [
                'id' => $booking->id,
                'roomId' => $room->id,
                'checkIn' => '2026-03-12',
                'checkOut' => '2026-03-15',
                'guestName' => 'Jane Doe',
                'guests' => 3,
                'nights' => 3,
                'totalCost' => 150,
                'sourceName' => 'Direct',
                'statusName' => 'Confirmed',
                'color' => '#16a34a',
                'editUrl' => route('bookings.edit', $booking),
            ])
        );
});

it('does not show the property groups or bookings of another tenant', function () {
    $data = createOwnerAndProperty();
    $room = Room::factory()->create([
        'property_id' => $data['property']->id,
        'tenant_id' => $data['tenant']->id,
    ]);
    Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $data['tenant']->id,
        'check_in' => now()->toDateString(),
        'check_out' => now()->addDays(2)->toDateString(),
    ]);

    $other = createOwnerAndProperty();
    $otherRoom = Room::factory()->create([
        'property_id' => $other['property']->id,
        'tenant_id' => $other['tenant']->id,
    ]);
    $otherBooking = Booking::factory()->create([
        'room_id' => $otherRoom->id,
        'tenant_id' => $other['tenant']->id,
        'check_in' => now()->toDateString(),
        'check_out' => now()->addDays(2)->toDateString(),
    ]);

    actingAs($other['owner']);

    $this->get(route('calendar'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->has('groups', 1)
            ->where('groups.0.id', $other['property']->id)
            ->has('bookings', 1)
            ->where('bookings.0.id', $otherBooking->id)
        );
});

test('a user without an active tenant gets empty property groups and bookings', function () {
    $owner = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $owner->tenants()->attach($tenant->id);
    $property = Property::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $room = Room::factory()->create([
        'property_id' => $property->id,
        'tenant_id' => $tenant->id,
    ]);
    $booking = Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id,
        'check_in' => now()->toDateString(),
        'check_out' => now()->addDays(2)->toDateString(),
    ]);

    $nonOwner = User::factory()->create();
    actingAs($nonOwner);

    $response = get(route('calendar'));
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->has('groups', 0)
            ->has('bookings', 0)
            ->has('window')
        );
});

it('returns only the bookings whose nights intersect the window', function () {
    $this->travelTo('2026-03-10 09:00:00');

    $data = createOwnerAndProperty();
    $tenant = $data['tenant'];
    $room = Room::factory()->create([
        'property_id' => $data['property']->id,
        'tenant_id' => $tenant->id,
    ]);
    $bookingFor = fn (string $checkIn, string $checkOut): Booking => Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
    ]);

    // The window covers the nights of 2026-03-08 up to and including 2026-04-06.
    $bookingFor('2026-03-01', '2026-03-08');
    $startsBefore = $bookingFor('2026-03-01', '2026-03-09');
    $inside = $bookingFor('2026-03-15', '2026-03-20');
    $endsAfter = $bookingFor('2026-04-06', '2026-04-10');
    $bookingFor('2026-04-07', '2026-04-10');

    actingAs($data['owner']);

    $this->get(route('calendar'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->has('bookings', 3)
            ->where('bookings.0.id', $startsBefore->id)
            ->where('bookings.1.id', $inside->id)
            ->where('bookings.2.id', $endsAfter->id)
        );
});

it('gives a booking without a status no colour and no status name', function () {
    $data = createOwnerAndProperty();
    $room = Room::factory()->create([
        'property_id' => $data['property']->id,
        'tenant_id' => $data['tenant']->id,
    ]);
    Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $data['tenant']->id,
        'check_in' => now()->toDateString(),
        'check_out' => now()->addDay()->toDateString(),
        'booking_status_id' => null,
        'booking_source_id' => null,
    ]);

    actingAs($data['owner']);

    $this->get(route('calendar'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('bookings.0.color', null)
            ->where('bookings.0.statusName', null)
            ->where('bookings.0.sourceName', null)
        );
});

it('uses an explicit window from the start and days query parameters', function (int $days) {
    $this->travelTo('2026-03-10 09:00:00');

    actingAs(createOwnerAndProperty()['owner']);

    $this->get(route('calendar', ['start' => '2026-05-01', 'days' => $days]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->where('window', ['start' => '2026-05-01', 'days' => $days])
        );
})->with([14, 30, 90]);

it('falls back to the default window for invalid query parameters', function (array $query, array $window) {
    $this->travelTo('2026-03-10 09:00:00');

    actingAs(createOwnerAndProperty()['owner']);

    $this->get(route('calendar', $query))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page->where('window', $window));
})->with([
    'unsupported days' => [['start' => '2026-05-01', 'days' => 7], ['start' => '2026-05-01', 'days' => 30]],
    'non-numeric days' => [['days' => 'abc'], ['start' => '2026-03-08', 'days' => 30]],
    'malformed start' => [['start' => 'not-a-date', 'days' => 14], ['start' => '2026-03-08', 'days' => 14]],
    'impossible start' => [['start' => '2026-02-30'], ['start' => '2026-03-08', 'days' => 30]],
]);

it('returns only the bookings whose nights intersect an explicit window', function () {
    $data = createOwnerAndProperty();
    $tenant = $data['tenant'];
    $room = Room::factory()->create([
        'property_id' => $data['property']->id,
        'tenant_id' => $tenant->id,
    ]);
    $bookingFor = fn (string $checkIn, string $checkOut): Booking => Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $tenant->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
    ]);

    // The window covers the nights of 2026-06-01 up to and including 2026-06-14.
    $bookingFor('2026-05-25', '2026-06-01');
    $startsBefore = $bookingFor('2026-05-25', '2026-06-02');
    $spansWindow = $bookingFor('2026-05-30', '2026-06-20');
    $inside = $bookingFor('2026-06-05', '2026-06-08');
    $endsAfter = $bookingFor('2026-06-14', '2026-06-18');
    $bookingFor('2026-06-15', '2026-06-18');

    actingAs($data['owner']);

    $this->get(route('calendar', ['start' => '2026-06-01', 'days' => 14]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->where('window', ['start' => '2026-06-01', 'days' => 14])
            ->has('bookings', 4)
            ->where('bookings.0.id', $startsBefore->id)
            ->where('bookings.1.id', $spansWindow->id)
            ->where('bookings.2.id', $inside->id)
            ->where('bookings.3.id', $endsAfter->id)
        );
});

it('does not show bookings of another tenant inside an explicit window', function () {
    $data = createOwnerAndProperty();
    $room = Room::factory()->create([
        'property_id' => $data['property']->id,
        'tenant_id' => $data['tenant']->id,
    ]);
    Booking::factory()->create([
        'room_id' => $room->id,
        'tenant_id' => $data['tenant']->id,
        'check_in' => '2026-06-03',
        'check_out' => '2026-06-05',
    ]);

    $other = createOwnerAndProperty();

    actingAs($other['owner']);

    $this->get(route('calendar', ['start' => '2026-06-01', 'days' => 14]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page->has('bookings', 0));
});
