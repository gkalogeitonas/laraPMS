<?php
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;

it('can create a user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
});


it('a user has properties through tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $property = Property::factory()->create(['tenant_id' => $tenant->id]);

    expect($user->properties->contains($property))->toBeTrue();
});
