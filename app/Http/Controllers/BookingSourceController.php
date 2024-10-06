<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingSourceController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(BookingSource::class, 'bookingSource');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('BookingSources/Index', [
            'bookingSources' => BookingSource::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BookingSources/Create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasActiveTenant()) {
            return redirect()->route('booking-sources.index')->with('error', 'No active tenant.');
        }
        $attributes  = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        BookingSource::create($attributes);
        return redirect()->route('booking-sources.index')->with('success', 'Booking Source created.');
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
    public function edit(BookingSource $bookingSource)
    {
        return Inertia::render('BookingSources/Edit', [
            'bookingSource' => $bookingSource
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookingSource $bookingSource)
    {
        $attributes  = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bookingSource->update($attributes);
        return redirect()->route('booking-sources.index')->with('success', 'Booking Source updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingSource $bookingSource)
    {
        $bookingSource->delete();
        return redirect()->route('booking-sources.index')->with('success', 'Booking Source deleted.');
    }
}
