<?php
use App\Models\User;
use App\Models\Property;

it('can create a user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
});

it('a user has properties', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);

    expect($user->properties->contains($property))->toBeTrue();
});
