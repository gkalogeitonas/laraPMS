<?php

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;

it('can create a Customer', function () {
    $Customer = Customer::factory()->create();

    expect($Customer)->toBeInstanceOf(customer::class);
});


test('a customer belongs to a tenant', function () {
    $tenant = Tenant::factory()->create();
    $customer = customer::factory()->create(['tenant_id' => $tenant->id]);

    expect($customer->tenant)->toBeInstanceOf(Tenant::class);
    expect($customer->tenant->id)->toBe($tenant->id);

});


it('a customer can be accessed through a tenant', function () {
    // Create a tenant
    $tenant = Tenant::factory()->create();

    // Create a customer and associate them with the tenant
    $customer = Customer::factory()->create(['tenant_id' => $tenant->id]);

    // Refresh the tenant to ensure the relationship is loaded
    $tenant->load('customers');

    expect($tenant->customers->contains($customer))->toBeTrue();
});


it('returns customers of the active tenant', function () {
    // Create a user and set the active tenant
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant);
    $this->actingAs($user);
    $user->setActiveTenant($tenant);
    // Create customers for the active tenant
    $customersOfActiveTenant = Customer::factory()->count(3)->create(['tenant_id' => $tenant->id]);

    // Create customers for another tenant
    $otherTenant = Tenant::factory()->create();
    Customer::factory()->count(2)->create(['tenant_id' => $otherTenant->id]);

    // Get customers of the active tenant
    $customers = Customer::ofActiveTenant();

    // Assert that the correct customers are returned
    expect($customers)->toHaveCount(3);
    $customersOfActiveTenant->each(function ($customer) use ($customers) {
        expect($customers->contains($customer))->toBeTrue();
    });
});
