<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BookingTotalsCalculator
{
    protected $bookings;
    protected $startDate;
    protected $endDate;

    public function __construct(Collection $bookings, $startDate, $endDate)
    {
        $this->bookings = $bookings;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }

    public function calculate()
    {
        $totalAmount = 0;
        $totalDays = 0;

        foreach ($this->bookings as $booking) {
            $bookingStartDate = Carbon::parse($booking->check_in);
            $bookingEndDate = Carbon::parse($booking->check_out);

            $effectiveStartDate = $bookingStartDate->greaterThan($this->startDate) ? $bookingStartDate : $this->startDate;
            $effectiveEndDate = $bookingEndDate->lessThan($this->endDate) ? $bookingEndDate : $this->endDate;


            $daysInRange = $effectiveStartDate->diffInDays($effectiveEndDate, false);
            //dd($this->startDate, $this->endDate, $booking, $daysInRange);

            if ($daysInRange > 0) {
                $totalDays += $daysInRange;
                $totalAmount += $booking->price * $daysInRange;
            }
        }

        return [
            'total_amount' => $totalAmount,
            'total_days' => $totalDays,
        ];
    }
}
