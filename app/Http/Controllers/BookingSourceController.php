<?php

namespace App\Http\Controllers;

use App\Models\BookingSource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingSourceController extends Controller
{
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
        $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
