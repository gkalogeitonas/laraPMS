<?php

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('belongs to a room', function () {
    $room = Room::factory()->create();
    $booking = Booking::factory()->create(['room_id' => $room->id]);

    expect($booking->room)->toBeInstanceOf(Room::class);
    expect($booking->room->id)->toBe($room->id);
});

it('calculates total_days correctly', function () {
    $booking = Booking::factory()->create([
        'start_date' => Carbon::parse('2023-01-01'),
        'end_date' => Carbon::parse('2023-01-10'),
    ]);

    expect($booking->total_days)->toBe(9);
});

it('calculates total_cost correctly', function () {
    $booking = Booking::factory()->create([
        'start_date' => Carbon::parse('2023-01-01'),
        'end_date' => Carbon::parse('2023-01-10'),
        'price' => 100.00,
    ]);

    expect($booking->total_cost)->toBe(900.00);
});
