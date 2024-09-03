<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PropertyController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Property::class, 'property');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Auth::user()->properties;
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
        // Validate the request
        $attributes = $this->validateProperty($request);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is associated with any tenants
        if ($user->tenants()->exists()) {
            // Get the first tenant associated with the user
            $tenant = $user->tenants()->first();

            // Create a new property with the validated attributes and set the tenant_id
            $property = new Property($attributes);
            $property->tenant_id = $tenant->id;

            // Save the property
            $property->save();

            // Redirect to the properties index with a success message
            return redirect()->route('properties.index')->with('success', 'Property created successfully.');
        }

        // If the user is not associated with any tenants, return an error response
        return redirect()->route('properties.index')->with('error', 'User is not associated with any tenants.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {

        return Inertia::render('Properties/Show', [
            'property' => $property,
            'rooms' => $property->rooms
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        return Inertia::render('Properties/Edit', [
            'property' => $property
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        //validate the request
        $attributes = $this->validateProperty($request);

        //update the property
        $property->update($attributes);
        return redirect()->route('properties.index')->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {

        Property::destroy($property->id);
        return redirect()->route('properties.index')->with('success', 'Property deleted successfully.');
    }

    private function validateProperty(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|min:5|max:255',
            'address' => 'nullable|string|min:5|max:255',
            'description' => 'nullable|string|min:5|max:255',
            'type' => 'nullable|string|min:3|max:255',
        ]);
    }
}
