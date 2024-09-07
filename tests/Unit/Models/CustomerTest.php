<?php

use App\Models\Customer;
use App\Models\Tenant;

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
