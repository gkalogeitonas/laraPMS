<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //validate the request
        $attributes = $request->validate([
            'name' => 'required|string|min:5|max:255',
            'address' => 'nullable|string|min:5|max:255',
            'description' => 'nullable|string|min:5|max:255',
            'type' => 'required|string|min:3|max:255',
        ]);

        //dd( auth()->user()->tenant);
        //dd( auth()->user()->id);
        //create a new property
        $property = new Property($attributes);
        $property->user_id = Auth::id(); // Set user_id to the ID of the current user
        $property->tenant_id = Auth::user()->tenant_id; // Set tenant_id to the tenant_id of the current user

        $property->save();

        //redirect to the properties index
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {   if (Auth::user()->id !== $property->user_id) {
            abort(403);
        }
        return Inertia::render('Properties/Show', [
            'property' => $property
        ]);
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
        //delete the property
        if (Auth::user()->id !== Property::find($id)->user_id) {
            abort(403);
        }
        Property::destroy($id);
    }
}
