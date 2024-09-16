<?php

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('can have many Properties', function() {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create properties associated with the tenant
    $properties = Property::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's properties relationship returns the correct properties
    expect($tenant->properties)->toHaveCount(3);
    expect($tenant->properties->pluck('id')->toArray())->toEqual($properties->pluck('id')->toArray());
});


it('can have many rooms', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create rooms associated with the tenant
    $rooms = Room::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's rooms relationship returns the correct rooms
    expect($tenant->rooms)->toHaveCount(3);
    expect($tenant->rooms->pluck('id')->toArray())->toEqual($rooms->pluck('id')->toArray());
});

it('can have many customers', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create customers associated with the tenant
    $customers = Customer::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Assert that the tenant's customers relationship returns the correct customers
    expect($tenant->customers)->toHaveCount(3);
    expect($tenant->customers->pluck('id')->toArray())->toEqual($customers->pluck('id')->toArray());
});
