<?php
// tests/Feature/SetInitialTenantTest.php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
