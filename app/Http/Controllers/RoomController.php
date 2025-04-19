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
        return Inertia::render('Rooms/Show', ['room' => $room]);
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
