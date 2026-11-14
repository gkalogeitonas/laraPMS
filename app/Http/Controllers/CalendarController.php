<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingCalendarResource;
use App\Http\Resources\PropertyCalendarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    /**
     * Window sizes, in days, the Occupancy Calendar can show.
     */
    private const ALLOWED_WINDOW_DAYS = [14, 30, 90];

    /**
     * Number of days shown when no valid window size is requested.
     */
    private const DEFAULT_WINDOW_DAYS = 30;

    /**
     * Days before today at which the window starts.
     */
    private const DAYS_BEFORE_TODAY = 2;

    public function index(Request $request): Response
    {
        $windowStart = $this->windowStart($request->query('start'));
        $windowDays = $this->windowDays($request->query('days'));
        $windowEnd = $windowStart->copy()->addDays($windowDays);

        $window = [
            'start' => $windowStart->toDateString(),
            'days' => $windowDays,
        ];

        $tenant = $request->user()->hasActiveTenant() ? $request->user()->getActiveTenant() : null;

        if (! $tenant) {
            return Inertia::render('Calendar', [
                'groups' => [],
                'bookings' => [],
                'window' => $window,
            ]);
        }

        $properties = $tenant->properties()->with('rooms')->orderBy('name')->get();

        $bookings = $tenant->bookings()
            ->with(['bookingStatus', 'bookingSource'])
            ->where('check_in', '<', $windowEnd->toDateString())
            ->where('check_out', '>', $windowStart->toDateString())
            ->orderBy('check_in')
            ->get();

        return Inertia::render('Calendar', [
            'groups' => PropertyCalendarResource::collection($properties)->resolve($request),
            'bookings' => BookingCalendarResource::collection($bookings)->resolve($request),
            'window' => $window,
        ]);
    }

    /**
     * The requested window start, or today minus a couple of days when it is missing or invalid.
     */
    private function windowStart(mixed $start): Carbon
    {
        if (is_string($start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $date = Carbon::createFromFormat('!Y-m-d', $start);

            if ($date !== null && $date->toDateString() === $start) {
                return $date;
            }
        }

        return Carbon::today()->subDays(self::DAYS_BEFORE_TODAY);
    }

    /**
     * The requested window size, normalised to the default when it is not an allowed size.
     */
    private function windowDays(mixed $days): int
    {
        if (is_string($days) && ctype_digit($days) && in_array((int) $days, self::ALLOWED_WINDOW_DAYS, true)) {
            return (int) $days;
        }

        return self::DEFAULT_WINDOW_DAYS;
    }
}
