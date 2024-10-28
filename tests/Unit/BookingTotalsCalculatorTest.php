<?php
use App\Services\BookingTotalsCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

it('calculates with no bookings', function () {
    $bookings = new Collection();
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_amount'])->toBe(0);
    expect($result['total_days'])->toBe(0);
});

it('calculates with bookings within range', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2023-01-05', 'check_out' => '2023-01-10', 'price' => 100],
        (object) ['check_in' => '2023-01-15', 'check_out' => '2023-01-20', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_amount'])->toBe(1250);
    expect($result['total_days'])->toBe(10);
});

it('calculates with bookings partially within range', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2022-12-25', 'check_out' => '2023-01-05', 'price' => 100],
        (object) ['check_in' => '2023-01-25', 'check_out' => '2023-02-05', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_days'])->toBe(10);
    expect($result['total_amount'])->toBe(1300);
});


it('calculates with booking partially within range', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2023-01-25', 'check_out' => '2023-02-05', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_days'])->toBe(6);
});

it('calculates with bookings outside range', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2022-12-01', 'check_out' => '2022-12-31', 'price' => 100],
        //clear(object) ['check_in' => '2023-02-01', 'check_out' => '2023-02-28', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-02', '2023-01-30');

    $result = $calculator->calculate();
    expect($result['total_days'])->toBe(0);
    expect($result['total_amount'])->toBe(0);
});

it('calculates with overlapping bookings', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2023-01-10', 'check_out' => '2023-01-20', 'price' => 100],
        (object) ['check_in' => '2023-01-15', 'check_out' => '2023-01-25', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_amount'])->toBe(2500);
    expect($result['total_days'])->toBe(20);
});

it('calculates with single day bookings', function () {
    $bookings = new Collection([
        (object) ['check_in' => '2023-01-10', 'check_out' => '2023-01-11', 'price' => 100],
        (object) ['check_in' => '2023-01-20', 'check_out' => '2023-01-21', 'price' => 150],
    ]);
    $calculator = new BookingTotalsCalculator($bookings, '2023-01-01', '2023-01-31');

    $result = $calculator->calculate();

    expect($result['total_amount'])->toBe(250);
    expect($result['total_days'])->toBe(2);
});
