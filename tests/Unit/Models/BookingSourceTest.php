<?php

use App\Models\BookingSource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;



uses(RefreshDatabase::class);


test('each users have access only to it own actives tenants booking sources', function () {
    //create two tenants
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    //create a users for each tenant
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    //assign the users to the tenants
    $tenant1->users()->attach($user1);
    $tenant2->users()->attach($user2);
    //tennant on will have 2 booking sources
    BookingSource::factory()->count(2)->create([
        'tenant_id' => $tenant1->id
    ]);
    //tenant two will have 3 booking sources
    BookingSource::factory()->count(3)->create([
        'tenant_id' => $tenant2->id
    ]);
    $this->actingAs($user1);
    $user1->setActiveTenant($tenant1);

    //get the booking sources
    $bookingSources = BookingSource::all();
    //assert that the user has access to only 2 booking sources
    expect($bookingSources->count())->toBe(2);
    //assert that the user has access to only booking sources that belongs to tenant 1
    $bookingSources->each(function ($bookingSource) use ($tenant1) {
        expect($bookingSource->tenant_id)->toBe($tenant1->id);
    });
});

test('a user without active tenant cannot see a list of booking sources', function () {
    //create two tenants
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    //create a users for each tenant
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    //assign the users to the tenants
    $tenant1->users()->attach($user1);
    $tenant2->users()->attach($user2);
    //tennant on will have 2 booking sources
    BookingSource::factory()->count(2)->create([
        'tenant_id' => $tenant1->id
    ]);
    //tenant two will have 3 booking sources
    BookingSource::factory()->count(3)->create([
        'tenant_id' => $tenant2->id
    ]);
    $this->actingAs($user1);

    //get the booking sources
    $bookingSources = BookingSource::all();
    //assert that the user has access to only 2 booking sources
    expect($bookingSources->count())->toBe(0);
});


test('a booking source is assigned active tenant id on creation', function () {
    //create a tenant
    $tenant = Tenant::factory()->create([
        'id' => 2
    ]);
    //create a user and assign to tenant
    $user = User::factory()->create();
    $tenant->users()->attach($user);
    $this->actingAs($user);
    $user->setActiveTenant($tenant);

    //create a booking source without specifying tenant_id
    $bookingSource = BookingSource::factory()->create();

    //assert that the booking source has the active tenant id
    expect($bookingSource->tenant_id)->toBe($tenant->id);
});
