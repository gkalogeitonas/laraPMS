<?php
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Support\Facades\Session;

it('can create a user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
});



test('a user has properties through tenant', function () {
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


test('a user has an active tenant stored in session', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a user and associate them with the tenant
    $user = User::factory()->create();
    $user->tenants()->attach($tenant->id);

    // Set the user's active tenant
    $user->setActiveTenant($tenant);

    // Get the user's active tenant
    $activeTenant = $user->getActiveTenant();

    expect($activeTenant->id)->toBe($tenant->id);
});


test('setActiveTenant stores the tenant in the session', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a user and associate them with the tenant
    $user = User::factory()->create();
    $user->tenants()->attach($tenant->id);

    // Set the user's active tenant
    $user->setActiveTenant($tenant);
    // Assert that the tenant ID is stored in the session
    $this->assertEquals(Session::get('active_tenant_id'), $tenant->id);
});



test('a user has active properities', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a user and associate them with the tenant
    $user = User::factory()->create();
    $user->tenants()->attach($tenant->id);

    // Create a property for the tenant
    $property = Property::factory()->create(['tenant_id' => $tenant->id]);

    // Set the user's active tenant
    $user->setActiveTenant($tenant);

    // Get the user's active properties
    $activeProperties = $user->getActiveProperties();

    expect($activeProperties->contains($property))->toBeTrue();
});



it('returns true if the user has an active tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->setActiveTenant($tenant);

    expect($user->hasActiveTenant())->toBeTrue();
});

it('returns false if the user does not have an active tenant', function () {
    $user = User::factory()->create();

    expect($user->hasActiveTenant())->toBeFalse();
});

