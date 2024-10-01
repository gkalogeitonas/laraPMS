<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingStatus;
use Inertia\Inertia;

class BookingStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('bookingStatuses/index', [
                'bookingStatuses' => []
            ])->with('error', 'No active tenant.');
        }
        return Inertia::render('bookingStatuses/index', [
            'bookingStatuses' => auth()->user()->getActiveTenant()->bookingStatuses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'name' => 'required'
        ]);
        $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;

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
