<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Http\Resources\BookingCalendarResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        // Fetch bookings
        $bookings = auth()->user()->getActiveTenant()->bookings()->with('room')->get();

        $events = BookingCalendarResource::collection($bookings);
        //return $events;

        // Fetch properties with rooms
        $properties = Auth::user()->getActiveProperties();
        //$properties = Property::with('rooms')->get();

        // Format properties and rooms as resources
        $resources = $properties->map(function ($property) {
            return [
                'name' => $property->name,
                'id' => 'P' . $property->id,
                'expanded' => true,
                'children' => $property->rooms->map(function ($room) {
                    return [
                        'name' => $room->name,
                        'id' => 'R' . $room->id,
                    ];
                }),
            ];
        });

        //return compact('events', 'resources');

        return Inertia::render('Calendar', [
            'events' => $events,
            'resources' => $resources,
        ]);
    }
}
