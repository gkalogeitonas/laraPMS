<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Http\Resources\BookingCalendarResource;
use App\Http\Resources\PropertyCalendarResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('Calendar', [
                'events' => [],
                'resources' => [],
            ]);
        }
        // Fetch bookings
        $bookings = auth()->user()->getActiveTenant()->bookings()->with('room')->get();

        $events = BookingCalendarResource::collection($bookings);
        //return $events;

        // Fetch properties with rooms
        $properties = Auth::user()->getActiveProperties();

        $resources = PropertyCalendarResource::collection($properties);

        return Inertia::render('Calendar', [
            'events' => $events,
            'resources' => $resources,
        ]);
    }
}
