<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Import the Room model
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('hotel:id,name')->where('user_id', auth()->id())->get(); // Fetch all rooms belonging to the currently authenticated user along with their hotel
        return Inertia::render('Rooms/Index', ['rooms' => $rooms]);
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
        //
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
        Room::where('id', $id)->delete();
        return redirect()->route('rooms.index');
    }
}