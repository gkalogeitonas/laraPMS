<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('belongs to a room', function () {
    $room = Room::factory()->create();
    $booking = Booking::factory()->create(['room_id' => $room->id]);

    expect($booking->room)->toBeInstanceOf(Room::class);
    expect($booking->room->id)->toBe($room->id);
});

it('belongs to a BookingStatus', function () {
    $data = createUserWithTenant();

    $booking = Booking::factory()->create([
        'booking_status_id' => BookingStatus::factory()->create()->id,
        'tenant_id' => $data['tenant']->id,
    ]);

    expect($booking->BookingStatus)->toBeInstanceOf(BookingStatus::class);
});

it('calculates total_days correctly', function () {
    $booking = Booking::factory()->create([
        'check_in' => Carbon::parse('2023-01-01'),
        'check_out' => Carbon::parse('2023-01-10'),
    ]);

    expect($booking->total_days)->toBe(9);
});

it('calculates total_cost correctly', function () {
    $booking = Booking::factory()->create([
        'check_in' => Carbon::parse('2023-01-01'),
        'check_out' => Carbon::parse('2023-01-10'),
        'price' => 100.00,
    ]);

    expect($booking->total_cost)->toBe(900.00);
});
