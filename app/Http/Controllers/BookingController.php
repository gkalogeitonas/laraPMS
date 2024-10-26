<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSource;
use App\Models\Room;
use App\Models\BookingStatus;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         //check if user has active tenant
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('Bookings/Index', [
                'bookings' => []
            ]);
        }

        $query = auth()->user()->getActiveTenant()->bookings()->with('room', 'bookingStatus');


        if ($request->has('check_in') && $request->has('check_out')) {
            $query->where(function($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
            });
        }

        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('booking_status_id')) {
            $query->where('booking_status_id', $request->booking_status_id);
        }


        if ($request->has('booking_source_id')) {
            $query->where('booking_source_id', $request->booking_source_id);
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $bookings = $query->paginate(10);

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'bookingStatuses' => BookingStatus::all(),
            'BookingSources' => BookingSource::all(),
            'Rooms' => auth()->user()->getActiveTenant()->rooms()->with('property')->get(),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Bookings/Create', [
            'rooms' => auth()->user()->getActiveTenant()->rooms,
            'bookingStatuses' => BookingStatus::all(),
            'BookingSources' => BookingSource::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $room = Room::find($request->room_id);
        $attributes = $this->validateBooking($request);
        $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;
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
        //dd($request->all());
        $booking_statuses = auth()->user()->getActiveTenant()->bookingStatuses;
        $booking_sources = auth()->user()->getActiveTenant()->bookingSources;
        $rooms = auth()->user()->getActiveTenant()->rooms;
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
