<?php
// tests/Feature/SetInitialTenantTest.php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

it('sets the initial tenant when user logs in', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a user and associate them with the tenant
    $user = User::factory()->create();
    $user->tenants()->attach($tenant->id);

    // Simulate user login
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password', // Assuming the factory sets the password to 'password'
    ]);

    // Assert that the tenant ID is stored in the session
    expect(Session::get('active_tenant_id'))->toBe($tenant->id);
});


it('requires authentication to switch tenant', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->post('/switch-tenant', [
        'tenant_id' => $tenant->id,
    ]);

    $response->assertRedirect('/login');
});

it('requires a valid tenant ID to switch tenant', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/switch-tenant', [
        'tenant_id' => 999, // Invalid tenant ID
    ]);

    $response->assertSessionHasErrors('tenant_id');
});

it('allows an authenticated user to switch tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant->id);
    $this->actingAs($user);


    $response = $this->post('/switch-tenant', [
        'tenant_id' => $tenant->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Tenant switched successfully');
    $this->assertEquals($tenant->id, $user->fresh()->getActiveTenant()->id);

    $otherTenant = Tenant::factory()->create();
    $user->tenants()->attach($otherTenant->id);

    $response = $this->post('/switch-tenant', [
        'tenant_id' => $otherTenant->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Tenant switched successfully');
    $this->assertEquals($otherTenant->id, $user->fresh()->getActiveTenant()->id);

});

it('returns 403 if tenant does not belong to the user', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/switch-tenant', [
        'tenant_id' => $tenant->id,
    ]);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
    $response->assertJson(['error' => 'Tenant not found or does not belong to the user']);
});



