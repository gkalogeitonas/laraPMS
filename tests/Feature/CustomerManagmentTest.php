<?php

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create();
    $this->user->tenants()->attach($this->tenant);
    $this->actingAs($this->user);
    $this->user->setActiveTenant($this->tenant);
});

it('allows a user to view customers of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->get(route('customers.index'));

    $response->assertStatus(200);
    $response->assertSee($customer->name);
});

it('allows a user to create a customer for the active tenant', function () {
    $customerData = Customer::factory()->make()->toArray();

    $response = $this->post(route('customers.store'), $customerData);

    $response->assertStatus(302)->with('success', 'Customer created successfully.');
    $this->assertDatabaseHas('customers', [
        'name' => $customerData['name'],
        'tenant_id' => $this->tenant->id,
    ]);
});

it('prevents a user to create a customer to other tenant', function () {
    $otherTenant = Tenant::factory()->create();
    $customerData = Customer::factory()->make(['tenant_id'=>$otherTenant->id])->toArray();

    $response = $this->post(route('customers.store'), $customerData);
    $this->assertDatabaseMissing('customers', [
        'name' => $customerData['name'],
        'tenant_id' => 'other_tenant_id',
    ]);
    $this->assertDatabaseHas('customers', [
        'name' => $customerData['name'],
        'tenant_id' => $this->tenant->id,
    ]);

});

it('allows a user to update a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
    $updatedData = [
        'name' => 'Updated Name',
        'email' => $customer->email,
        'phone' => $customer->phone,
        'address' => $customer->address,
    ];

    $response = $this->patch(route('customers.update', $customer), $updatedData);
    $response->assertStatus(302);
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name'
    ]);
});

it('allows a user to delete a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->delete("/customers/{$customer->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

it('prevents a user from managing others tenants customer', function () {
    $otherTenant = Tenant::factory()->create();
    $customer = Customer::factory()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->get(route('customers.index'));
    $response->assertStatus(200);
    $response->assertDontSee($customer->name);

    $response = $this->get(route('customers.edit', $customer));
    $response->assertStatus(403);

    $response = $this->patch(route('customers.update', $customer), []);
    $response->assertStatus(403);

    $response = $this->delete(route('customers.destroy', $customer));
    $response->assertStatus(403);
});


it('allows a user to view a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->get(route('customers.show', $customer));

    $response->assertStatus(200);
    $response->assertSee($customer->name);
    $response->assertSee($customer->email); // Assuming the customer has an email field
});


it('allows a user to edit a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->get(route('customers.edit', $customer));

    $response->assertStatus(200);
    $response->assertSee($customer->name);
    $response->assertSee($customer->email); // Assuming the customer has an email field
});
