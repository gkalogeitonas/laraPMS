<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * A Reschedule of a Booking: a new Room, check-in date or check-out date.
 */
class RescheduleBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->booking());
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'room_id' => [
                'required',
                'integer',
                Rule::exists(Room::class, 'id')->where('tenant_id', $this->user()->getActiveTenant()?->id),
            ],
            'check_in' => ['required', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $booking = $this->booking();
                $today = Carbon::today();

                if (Carbon::parse($booking->check_out)->lt($today)) {
                    $validator->errors()->add('check_out', 'This Booking has already ended and can no longer be rescheduled.');

                    return;
                }

                if (Carbon::parse($booking->check_in)->gte($today)) {
                    return;
                }

                if ((int) $this->input('room_id') !== (int) $booking->room_id) {
                    $validator->errors()->add('room_id', 'This Booking is in progress, so its Room can no longer change.');
                }

                if (! Carbon::parse($booking->check_in)->isSameDay($this->date('check_in'))) {
                    $validator->errors()->add('check_in', 'This Booking is in progress, so its check-in can no longer change.');
                }

                if ($this->date('check_out')->lt($today)) {
                    $validator->errors()->add('check_out', 'The check-out of an in-progress Booking can not be in the past.');
                }
            },
        ];
    }

    public function booking(): Booking
    {
        return $this->route('booking');
    }
}
