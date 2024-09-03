<?php
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;

it('can create a user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
});



it('a user has properties through tenant', function () {
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
