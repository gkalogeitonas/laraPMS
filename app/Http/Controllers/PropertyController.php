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
        $properties = Property::where('tenant_id', Auth::user()->tenant_id)->get();
        return Inertia::render('Properties/Index', [
            'properties' => $properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Properties/Create');
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

        $property = new Property($attributes);
        $property->tenant_id = Auth::user()->tenant_id; // Set tenant_id to the tenant_id of the current user

        $property->save();
        //redirect to the properties index with a success message
        return redirect()->route('properties.index')->with('success', 'Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Check if the authenticated user's tenant ID matches the property's tenant ID
        if (Auth::user()->tenant->id !== $property->tenant->id) {
            abort(403); // If not, abort with a 403 Forbidden status
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
        $property = Property::findOrFail($id);
        if (Auth::user()->tenant->id !== $property->tenant->id) {
            abort(403); // If not, abort with a 403 Forbidden status
        }
        //validate the request
        $attributes = $request->validate([
            'name' => 'required|string|min:5|max:255',
            'address' => 'nullable|string|min:5|max:255',
            'description' => 'nullable|string|min:5|max:255',
            'type' => 'nullable|string|min:3|max:255',
        ]);

        //update the property
        $property->update($attributes);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        //delete the property
        if (Auth::user()->tenant->id !== $property->tenant->id) {
            abort(403); // If not, abort with a 403 Forbidden status
        }

        Property::destroy($property->id);
    }
}
