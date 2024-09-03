<?php
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;

it('can create a property', function () {
    $property = Property::factory()->create();

    expect($property)->toBeInstanceOf(Property::class);
});

it('a property belongs to a tenant', function () {
    $tenant = Tenant::factory()->create();
    $property = Property::factory()->create(['tenant_id' => $tenant->id]);

    expect($property->tenant)->toBeInstanceOf(Tenant::class);
    expect($property->tenant->id)->toBe($tenant->id);
});


it('a property can be accessed through a user', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a user and associate them with the tenant
    $user = User::factory()->create();
    $user->tenants()->attach($tenant->id);

    // Create a property for the tenant
    $property = Property::factory()->create(['tenant_id' => $tenant->id]);

    // Refresh the user to ensure the relationship is loaded
    $user->load('properties');

    expect($user->properties->contains($property))->toBeTrue();
});
