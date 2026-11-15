<?php

use App\Models\Booking;
use App\Models\BookingSource;
use App\Models\BookingStatus;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->travelTo('2026-06-15 10:00:00');

    $this->tenant = Tenant::factory()->create();
    $this->user = createAsUserWithActiveTenant($this->tenant);

    $this->property = Property::factory()->create(['tenant_id' => $this->tenant->id, 'name' => 'Seaside']);
    $this->room = Room::factory()->create(['tenant_id' => $this->tenant->id, 'property_id' => $this->property->id, 'name' => 'Blue Room']);
});

it('reschedules a Booking to other dates', function () {
    $booking = bookingFor($this->room, '2026-06-20', '2026-06-23', ['name' => 'Grace Hopper']);

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-25',
        'check_out' => '2026-06-29',
    ])->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success', "Grace Hopper's Booking was rescheduled. Check-in: Sat 20 Jun 2026 → Thu 25 Jun 2026. Check-out: Tue 23 Jun 2026 → Mon 29 Jun 2026.");

    $booking->refresh();
    expect($booking->room_id)->toBe($this->room->id)
        ->and((string) $booking->check_in)->toStartWith('2026-06-25')
        ->and((string) $booking->check_out)->toStartWith('2026-06-29');
});

it('moves a Booking to a Room of another Property without touching its price, guest, source or status', function () {
    $otherProperty = Property::factory()->create(['tenant_id' => $this->tenant->id, 'name' => 'Mountain Lodge']);
    $otherRoom = Room::factory()->create(['tenant_id' => $this->tenant->id, 'property_id' => $otherProperty->id, 'name' => 'Chalet 2']);
    $status = BookingStatus::factory()->create(['tenant_id' => $this->tenant->id]);
    $source = BookingSource::factory()->create(['tenant_id' => $this->tenant->id]);
    $booking = bookingFor($this->room, '2026-06-20', '2026-06-23', [
        'name' => 'Ada Lovelace',
        'total_guests' => 2,
        'price' => 80,
        'booking_status_id' => $status->id,
        'booking_source_id' => $source->id,
    ]);

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $otherRoom->id,
        'check_in' => '2026-06-20',
        'check_out' => '2026-06-24',
    ])->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success', "Ada Lovelace's Booking was rescheduled. Room: Blue Room (Seaside) → Chalet 2 (Mountain Lodge). Check-out: Tue 23 Jun 2026 → Wed 24 Jun 2026.");

    $booking->refresh();
    expect($booking->room_id)->toBe($otherRoom->id)
        ->and((float) $booking->price)->toBe(80.0)
        ->and($booking->total_cost)->toEqual(320)
        ->and($booking->name)->toBe('Ada Lovelace')
        ->and($booking->total_guests)->toBe(2)
        ->and($booking->booking_status_id)->toEqual($status->id)
        ->and($booking->booking_source_id)->toEqual($source->id);
});

it('refuses a check-out on or before the check-in', function (string $checkOut) {
    $booking = bookingFor($this->room, '2026-06-20', '2026-06-23');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-25',
        'check_out' => $checkOut,
    ])->assertSessionHasErrors('check_out');

    expect((string) $booking->refresh()->check_in)->toStartWith('2026-06-20');
})->with(['same day' => '2026-06-25', 'day before' => '2026-06-24']);

it('refuses to reschedule a Booking that has already ended', function () {
    $booking = bookingFor($this->room, '2026-06-10', '2026-06-14');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-20',
        'check_out' => '2026-06-24',
    ])->assertSessionHasErrors('check_out');

    expect((string) $booking->refresh()->check_in)->toStartWith('2026-06-10');
});

it('lets an in-progress Booking change only its check-out', function () {
    $booking = bookingFor($this->room, '2026-06-12', '2026-06-17');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-12',
        'check_out' => '2026-06-20',
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect((string) $booking->refresh()->check_out)->toStartWith('2026-06-20');
});

it('refuses to change the Room, check-in or past check-out of an in-progress Booking', function (string $field, Closure $change) {
    $otherRoom = Room::factory()->create(['tenant_id' => $this->tenant->id, 'property_id' => $this->property->id]);
    $booking = bookingFor($this->room, '2026-06-12', '2026-06-17');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-12',
        'check_out' => '2026-06-17',
        ...$change($otherRoom),
    ])->assertSessionHasErrors($field);

    $booking->refresh();
    expect($booking->room_id)->toBe($this->room->id)
        ->and((string) $booking->check_in)->toStartWith('2026-06-12')
        ->and((string) $booking->check_out)->toStartWith('2026-06-17');
})->with([
    'Room' => ['room_id', fn (Room $otherRoom) => ['room_id' => $otherRoom->id]],
    'check-in' => ['check_in', fn () => ['check_in' => '2026-06-13']],
    'check-out in the past' => ['check_out', fn () => ['check_out' => '2026-06-14']],
]);

it('refuses to reschedule another tenant\'s Booking', function () {
    [$otherRoom, $booking] = outsideActiveTenant(function () {
        $otherRoom = Room::factory()->create();

        return [$otherRoom, bookingFor($otherRoom, '2026-06-20', '2026-06-23')];
    });

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $this->room->id,
        'check_in' => '2026-06-25',
        'check_out' => '2026-06-29',
    ])->assertNotFound();

    $booking = Booking::withoutGlobalScopes()->find($booking->id);
    expect($booking->room_id)->toBe($otherRoom->id)
        ->and((string) $booking->check_in)->toStartWith('2026-06-20');
});

it('refuses to reschedule a Booking into another tenant\'s Room', function () {
    $otherRoom = outsideActiveTenant(fn () => Room::factory()->create());
    $booking = bookingFor($this->room, '2026-06-20', '2026-06-23');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $otherRoom->id,
        'check_in' => '2026-06-20',
        'check_out' => '2026-06-23',
    ])->assertSessionHasErrors('room_id');

    expect($booking->refresh()->room_id)->toBe($this->room->id);
});

it('treats a Booking that checks in today as not yet started', function () {
    $otherRoom = Room::factory()->create(['tenant_id' => $this->tenant->id, 'property_id' => $this->property->id]);
    $booking = bookingFor($this->room, '2026-06-15', '2026-06-18');

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $otherRoom->id,
        'check_in' => '2026-06-16',
        'check_out' => '2026-06-19',
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect($booking->refresh()->room_id)->toBe($otherRoom->id);
});

it('names only the Room when the new Room is in the same Property', function () {
    $otherRoom = Room::factory()->create(['tenant_id' => $this->tenant->id, 'property_id' => $this->property->id, 'name' => 'Green Room']);
    $booking = bookingFor($this->room, '2026-06-20', '2026-06-23', ['name' => 'Alan Turing']);

    patch(route('bookings.reschedule', $booking), [
        'room_id' => $otherRoom->id,
        'check_in' => '2026-06-20',
        'check_out' => '2026-06-23',
    ])->assertSessionHas('success', "Alan Turing's Booking was rescheduled. Room: Blue Room → Green Room.");
});
