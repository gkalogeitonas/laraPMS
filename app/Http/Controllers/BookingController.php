<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSource;
use App\Models\Room;
use App\Models\BookingStatus;
use Illuminate\Http\Request;
use App\Services\BookingTotalsCalculator;
use Inertia\Inertia;
use App\Traits\BookingFilters;


class BookingController extends Controller
{

    use BookingFilters;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         //check if user has active tenant
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('Bookings/Index', [
                'bookings' => [],
                'totals' => [
                    'total_amount' => 0,
                    'total_days' => 0,
                ],
            ]);
        }

        $query = Booking::with('room', 'bookingStatus', 'bookingSource');

        $query = $this->applyBookingFilters($query, $request);

        // Calculate the totals using the BookingTotalsCalculator
        $totalsCalculator = new BookingTotalsCalculator($query->get(), $request->start_date, $request->end_date);
        $totals = $totalsCalculator->calculate();

        $bookings = $query->paginate(10);

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'bookingStatuses' => BookingStatus::all(),
            'BookingSources' => BookingSource::all(),
            'Rooms' => Room::with('property')->get(), // Simplified from auth()->user()->getActiveTenant()->rooms()
            'totals' => $totals,
            'filters' => request()->all('start_date', 'end_date', 'name', 'booking_status_id', 'booking_source_id', 'room_id'),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Check if room_id is provided and if it belongs to the active tenant
        $preselectedRoom = null;
        if ($request->has('room_id')) {
            $preselectedRoom = Room::find($request->room_id);
            if ($preselectedRoom && $preselectedRoom->tenant_id !== auth()->user()->getActiveTenant()->id) {
                $preselectedRoom = null; // Reset if room doesn't belong to active tenant
            }
        }

        return Inertia::render('Bookings/Create', [
            'rooms' => auth()->user()->getActiveTenant()->rooms,
            'bookingStatuses' => BookingStatus::all(),
            'BookingSources' => BookingSource::all(),
            'preselectedRoom' => $preselectedRoom, // Pass the preselected room to the view
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $room = Room::find($request->room_id);
        $attributes = $this->validateBooking($request);

        // Remove this line, as tenant_id will be set automatically by the trait
        // $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;

        Booking::create($attributes);
        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return $booking->load('room', 'bookingStatus', 'bookingSource');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //return  $booking->load(['room', 'bookingStatus', 'bookingSource']);
        $this->authorize('update', $booking);
        return Inertia::render('Bookings/Edit', [
            'booking' => $booking->load(['room', 'bookingStatus', 'bookingSource']),
            'rooms' => auth()->user()->getActiveTenant()->rooms,
            'bookingStatuses' => BookingStatus::all(),
            'BookingSources' => BookingSource::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Booking $booking, Request $request)
    {
        $this->authorize('update', $booking);
        $attributes = $this->validateBooking($request);
        $booking->update($attributes);
        return redirect()->route('bookings.index')->with('success', 'Booking updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted.');
    }

    private function validateBooking(Request $request)
    {
        $booking_statuses = BookingStatus::all(); // The global scope will filter by tenant
        $booking_sources = BookingSource::all();  // The global scope will filter by tenant
        $rooms = Room::all();                     // The global scope will filter by tenant

        return $request->validate([
            'room_id' => 'required|in:' . $rooms->pluck('id')->join(','),
            'name' => 'required|string|min:3|max:255',
            'customer_id' => 'nullable',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'booking_status_id' => 'nullable|in:' . $booking_statuses->pluck('id')->join(','),
            'booking_source_id' => 'nullable|in:' . $booking_sources->pluck('id')->join(','),
            'total_guests' => 'required|integer',
            'price' => 'required|numeric',
        ]);
    }
}
