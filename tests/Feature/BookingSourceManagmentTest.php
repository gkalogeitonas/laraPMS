<?php
// tests/Feature/BookingSourceTest.php

use App\Models\BookingSource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can create a booking source', function () {
    $tenant = Tenant::factory()->create();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);

    expect($bookingSource)->toBeInstanceOf(BookingSource::class);
    expect($bookingSource->name)->toBe('Website');
});


it('has a name', function () {
    $tenant = Tenant::factory()->create();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);

    expect($bookingSource->name)->toBe('Website');
});


test('a user can create a booking source', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $this->actingAs($owner);
    $response = $this->post('/booking-sources', [
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('booking_sources', ['name' => 'Website']);
});

test('a user without active tenant cannot create a booking source', function () {
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $response = $this->post(route('booking-sources.store'), [
        'name' => 'Website'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseMissing('booking_sources', ['name' => 'Website']);
});

test('a user with an active tenant can see a list of booking sources', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);
    $this->actingAs($owner);
    $response = $this->get(route('booking-sources.index'));

    $response->assertStatus(200);
    $response->assertSee('Website');
});

test('a user without an active tenant will see empty list of booking sources', function () {
    $tenant = Tenant::factory()->create();
    $bookingSource = BookingSource::create([
        'name' => 'Website',
        'tenant_id' => $tenant->id,
    ]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $response = $this->get(route('booking-sources.index'));

    $response->assertStatus(200);
    $response->assertDontSee('Website');
});

test('a user can edit a booking source', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);
    $this->actingAs($owner);
    $response = $this->patch(route('booking-sources.update', $bookingSource->id), [
        'name' => 'Referral'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('booking_sources', ['name' => 'Referral']);
});

it('can delete a booking source', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);
    $this->actingAs($owner);
    $response = $this->delete(route('booking-sources.destroy', $bookingSource->id));

    $response->assertStatus(302);
    $this->assertDatabaseMissing('booking_sources', ['name' => 'Website']);
});


it('prevents a user without an active tenant from deleting a booking source', function () {
    $tenant = Tenant::factory()->create();
    $bookingSource = BookingSource::create([
        'name' => 'Website',
        'tenant_id' => $tenant->id,
    ]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $response = $this->delete(route('booking-sources.destroy', $bookingSource->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('booking_sources', ['name' => 'Website']);
});

it('prevents a user from deleting another tenant\'s booking source', function () {
    list('owner' => $nonowner,  'tenant' => $tenant) = createOwnerAndProperty();
    $otherTenant = Tenant::factory()->create();
    $bookingSource = BookingSource::create([
        'tenant_id' => $otherTenant->id,
        'name' => 'Website'
    ]);
    $this->actingAs($nonowner);
    $response = $this->delete(route('booking-sources.destroy', $bookingSource->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('booking_sources', ['name' => 'Website']);
});

test('a users can see only its own booking sources', function () {
    list('owner' => $owner,  'tenant' => $tenant) = createOwnerAndProperty();
    $bookingSource = BookingSource::create([
        'tenant_id' => $tenant->id,
        'name' => 'Website'
    ]);
    $otherTenant = Tenant::factory()->create();
    $otherBookingSource = BookingSource::create([
        'tenant_id' => $otherTenant->id,
        'name' => 'Booking.com'
    ]);
    //create 2 more booking sources for the other tenant
    BookingSource::factory()->count(2)->create([
        'tenant_id' => $otherTenant->id,
    ]);
    $this->actingAs($owner);
    $response = $this->get(route('booking-sources.index'));


    $response->assertStatus(200);
    $response->assertSee('Website');
    $response->assertDontSee('Booking.com');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('BookingSources/Index')
        ->has('bookingSources', 1)
        ->where('bookingSources.0.id', $bookingSource->id)
    );
});
