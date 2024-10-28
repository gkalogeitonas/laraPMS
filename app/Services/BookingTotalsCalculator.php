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
        $this->startDate = ($startDate) ? Carbon::parse($startDate) : false;
        $this->endDate = ($endDate) ? Carbon::parse($endDate) : false;
    }

    public function calculate()
    {
        $totalAmount = 0;
        $totalDays = 0;

        foreach ($this->bookings as $booking) {
            $bookingStartDate = Carbon::parse($booking->check_in);
            $bookingEndDate = Carbon::parse($booking->check_out);

            $effectiveStartDate = $this->getEffectiveStartDate($bookingStartDate);
            $effectiveEndDate = $this->getEffectiveEndDate($bookingEndDate);

            $daysInRange = $effectiveStartDate->diffInDays($effectiveEndDate, false);

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

    private function getEffectiveStartDate($bookingStartDate)
    {   if (!$this->startDate) {
            return $bookingStartDate;
        }
        return $bookingStartDate->greaterThan($this->startDate) ? $bookingStartDate : $this->startDate;
    }

    private function getEffectiveEndDate($bookingEndDate)
    {
        if (!$this->endDate) {
            return $bookingEndDate;
        }
        return $bookingEndDate->lessThan($this->endDate) ? $bookingEndDate : $this->endDate;
    }
}
