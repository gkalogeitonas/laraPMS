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
     * Number of days shown by the Occupancy Calendar.
     */
    private const WINDOW_DAYS = 30;

    /**
     * Days before today at which the window starts.
     */
    private const DAYS_BEFORE_TODAY = 2;

    public function index(Request $request): Response
    {
        $windowStart = Carbon::today()->subDays(self::DAYS_BEFORE_TODAY);
        $windowEnd = $windowStart->copy()->addDays(self::WINDOW_DAYS);

        $window = [
            'start' => $windowStart->toDateString(),
            'days' => self::WINDOW_DAYS,
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
}
