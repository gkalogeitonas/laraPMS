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
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $property = Property::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->properties->contains($property))->toBeTrue();
});
