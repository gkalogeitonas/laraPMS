<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Import the Room model
use App\Models\Property; // Import the Property model
use Inertia\Inertia;


class RoomController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Room::class, 'room');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasActiveTenant()) {
            $rooms = Room::all(); // Global scope will filter by active tenant automatically
            return Inertia::render('Rooms/Index', ['rooms' => $rooms]);
        } else {
            return Inertia::render('Rooms/Index', ['rooms' => []]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Property $property)
    {
        $this->authorize('update', $property);
        return Inertia::render('Rooms/Create', [
            'property' => $property,
            'types' => config('room.types'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        $attributes = $this->validateRoom($request);
        $attributes['property_id'] = $property->id;
        Room::create($attributes);

        return redirect()->route('properties.show', $property)->with('success', 'Room created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        // Load the room with the property and bookings
        $room = $room->load(['property', 'bookings.bookingStatus', 'bookings.bookingSource']);

        // Get current booking if the room is occupied
        $today = now()->format('Y-m-d');
        $currentBooking = $room->bookings()
            ->where('check_in', '<=', $today)
            ->where('check_out', '>', $today)
            ->with(['bookingStatus'])
            ->first();

        // Get upcoming bookings
        $upcomingBookings = $room->bookings()
            ->where('check_in', '>', $today)
            ->orderBy('check_in')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                // Ensure dates are in Y-m-d format for consistency
                $booking->check_in = \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d');
                $booking->check_out = \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d');
                return $booking;
            });

        // Get recent bookings for this room
        $recentBookings = $room->bookings()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                // Ensure dates are in Y-m-d format for consistency
                $booking->check_in = \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d');
                $booking->check_out = \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d');
                return $booking;
            });

        // Calculate occupancy rate for the last 30 days
        $thirtyDaysAgo = \Carbon\Carbon::today()->subDays(30);
        $totalDaysOccupied = 0;

        for ($date = $thirtyDaysAgo->copy(); $date->lte(\Carbon\Carbon::today()); $date->addDay()) {
            $isOccupied = $room->bookings->contains(function($booking) use ($date) {
                $checkIn = \Carbon\Carbon::parse($booking->check_in);
                $checkOut = \Carbon\Carbon::parse($booking->check_out);
                return $date->between($checkIn, $checkOut->subDay());
            });

            if ($isOccupied) {
                $totalDaysOccupied++;
            }
        }

        $occupancyRate = round(($totalDaysOccupied / 30) * 100);

        // Calculate average rate from bookings in the last 6 months
        $sixMonthsAgo = now()->subMonths(6);
        $averageRate = $room->bookings()
            ->where('created_at', '>=', $sixMonthsAgo)
            ->avg('price') ?? 0;

        // Calculate total revenue from this room
        $totalRevenue = $room->bookings()->sum('price');

        // Calculate revenue for current month
        $currentMonthRevenue = $room->bookings()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('price');

        // Get booking trends for this room (monthly)
        $bookingTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $bookingsCount = $room->bookings()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $bookingTrends[] = [
                'month' => $month->format('M'),
                'count' => $bookingsCount
            ];
        }

        return Inertia::render('Rooms/Show', [
            'room' => $room,
            'currentBooking' => $currentBooking,
            'upcomingBookings' => $upcomingBookings,
            'recentBookings' => $recentBookings,
            'occupancyRate' => $occupancyRate,
            'averageRate' => $averageRate,
            'totalRevenue' => $totalRevenue,
            'currentMonthRevenue' => $currentMonthRevenue,
            'bookingTrends' => $bookingTrends,
            'isOccupied' => $currentBooking !== null
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {

        return Inertia::render('Rooms/Edit', [
            'room' => $room,
            'types' => config('room.types'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $attributes = $this->validateRoom($request);
        $room->update($attributes);
        return redirect()->route('properties.show', $room->property)->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $property = $room->property;
        $room->delete();
        return redirect()->route('properties.show', $property)->with('success', 'Room deleted successfully.');
    }


    private function validateRoom(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:5|max:255',
            'type' => 'required|in:' . implode(',', config('room.types')),
            'price' => 'nullable|numeric|min:0', // Add price validation
        ]);
    }

}
