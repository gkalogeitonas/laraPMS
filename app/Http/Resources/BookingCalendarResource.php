<?php

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * A Booking bar of the Occupancy Calendar.
 *
 * @mixin Booking
 */
class BookingCalendarResource extends JsonResource
{
    /**
     * @return array{id: int, roomId: int, checkIn: string, checkOut: string, guestName: string, guests: int, nights: int, totalCost: float|int, sourceName: ?string, statusName: ?string, color: ?string, editUrl: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'roomId' => $this->room_id,
            'checkIn' => Carbon::parse($this->check_in)->toDateString(),
            'checkOut' => Carbon::parse($this->check_out)->toDateString(),
            'guestName' => $this->name,
            'guests' => $this->total_guests,
            'nights' => $this->total_days,
            'totalCost' => $this->total_cost,
            'sourceName' => $this->bookingSource?->name,
            'statusName' => $this->bookingStatus?->name,
            'color' => $this->bookingStatus?->color,
            'editUrl' => route('bookings.edit', $this->resource),
        ];
    }
}
