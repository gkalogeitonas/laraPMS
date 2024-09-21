<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Bookings/Index', [
            'bookings' => auth()->user()->getActiveTenant()->bookings()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //return auth()->user()->getActiveTenant()->rooms;
        return Inertia::render('Bookings/Create', [
            'rooms' => auth()->user()->getActiveTenant()->rooms,
            'statuses' => Config::get('booking.status'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $this->validateBooking($request);
        $room = Room::find($request->room_id);
        $this->authorize('update', $room);
        $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;
        Booking::create($attributes);
        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        return $request->validate([
            'room_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'customer_id' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:' . implode(',', Config::get('booking.status')),
            'total_guests' => 'required|integer',
            'price' => 'required|numeric',
        ]);
    }
}
