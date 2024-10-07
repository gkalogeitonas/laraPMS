<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\BookingStatus;
use Inertia\Inertia;

class BookingStatusController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(BookingStatus::class, 'bookingStatus');
    }
    /**
     * Display a listing of the resource.
     */
    public function Index()
    {
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('bookingStatuses/Index', [
                'bookingStatuses' => []
            ])->with('error', 'No active tenant.');
        }
        return Inertia::render('bookingStatuses/Index', [
            'bookingStatuses' => BookingStatus::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('bookingStatuses/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasActiveTenant()) {
            return redirect()->route('booking-statuses.index')->with('error', 'No active tenant.');
        }
        $attributes = $this->validate($request, [
            'name' => 'required',
            'color' => 'string|nullable'
        ]);
        BookingStatus::create($attributes);
        return redirect()->route('booking-statuses.index')->with('success', 'Booking Status created.');

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
    public function edit(BookingStatus $bookingStatus)
    {
        return Inertia::render('bookingStatuses/Edit', [
            'bookingStatus' => $bookingStatus
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(BookingStatus $bookingStatus, Request $request)
    {
        $attributes = $this->validate($request, [
            'name' => 'required',
            'color' => 'string|nullable'
        ]);
        $bookingStatus->update($attributes);
        return redirect()->route('booking-statuses.index')->with('success', 'Booking Status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingStatus $bookingStatus)
    {
        $bookingStatus->delete();
        return redirect()->route('booking-statuses.index')->with('error', 'Booking Status deleted.');
    }
}
