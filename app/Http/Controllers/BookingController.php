<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
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

        $query = auth()->user()->getActiveTenant()->bookings()->with('room');

        if ($request->has('check_in') && $request->has('check_out')) {
            $query->whereBetween('check_in', [$request->check_in, $request->check_out])
            ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
        }

        $bookings = $query->paginate(10);

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
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
        $room = Room::find($request->room_id);
        $this->authorize('update', $room);
        $attributes = $this->validateBooking($request);
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
        $user = auth()->user();
        return $request->validate([
            'room_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'customer_id' => 'nullable',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'booking_status_id' => [
                'nullable',
                Rule::exists('booking_statuses', 'id')->where(function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);
                }),
            ],
            'total_guests' => 'required|integer',
            'price' => 'required|numeric',
        ]);
    }
}
