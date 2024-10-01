<?php
// tests/Feature/BookingStatusTest.php

use App\Models\BookingStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can create a booking status', function () {
    $tenant = Tenant::factory()->create();
    $bookingStatus = BookingStatus::create([
        'tenant_id' => $tenant->id,
        'name' => 'Confirmed'
    ]);

    expect($bookingStatus)->toBeInstanceOf(BookingStatus::class);
    expect($bookingStatus->name)->toBe('Confirmed');
});


it('has a name', function () {
    $tenant = Tenant::factory()->create();
    $bookingStatus = BookingStatus::create([
        'tenant_id' => $tenant->id,
        'name' => 'Confirmed'
    ]);

    expect($bookingStatus->name)->toBe('Confirmed');
});


test('a user can create a booking status', function () {

    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $this->actingAs($owner);
    $response = $this->post('/booking-statuses', [
        'tenant_id' => $tenant->id,
    'name' => 'Confirmed'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('booking_statuses', ['name' => 'Confirmed']);
});

test('a user without active tenant cannot create a booking status', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $response = $this->post('/booking-statuses', [
        'name' => 'Confirmed'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseMissing('booking_statuses', ['name' => 'Confirmed']);
});

test('a user with an active tenant can see a list of booking statuses with statues', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $bookingStatus = BookingStatus::create([
        'tenant_id' => $tenant->id,
        'name' => 'Confirmed'
    ]);
    $this->actingAs($owner);
    $response = $this->get('/booking-statuses');

    $response->assertStatus(200);
    $response->assertSee('Confirmed');
});

test('a user without an active tenant will see empty list of booking status', function () {
    $tenant = Tenant::factory()->create();
    $bookingStatus = BookingStatus::create([
        'name' => 'Confirmed',
        'tenant_id' => $tenant->id,
    ]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $response = $this->get('/booking-statuses');

    $response->assertStatus(200);
    $response->assertDontSee('Confirmed');
});
