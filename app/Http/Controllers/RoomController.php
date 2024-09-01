<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Import the Room model
use App\Models\Property; // Import the Property model
use Inertia\Inertia;


class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        return Inertia::render('Rooms/Index', ['rooms' => $rooms]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Property $property)
    {
        return Inertia::render('Rooms/Create', [
            'property' => $property,
            'types' => config('room.types'),
            'statuses' => config('room.statuses')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Property $property)
    {
        if ($property->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }
        $attributes = $this->validateRoom($request);
        $attributes['property_id'] = $property->id;
        $attributes['tenant_id'] = auth()->user()->tenant->id;

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
        return $room;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        if ($room->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }
        $attributes = $this->validateRoom($request);
        $room->update($attributes);
        return redirect()->route('properties.show', $room->property)->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {

        if ($room->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }
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
            'status' => 'required|in:' . implode(',', config('room.statuses')),
            ///'property_id' => 'required|exists:properties,id',
        ]);
    }

}
