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

    $response = $this->get('/customers');

    $response->assertStatus(200);
    $response->assertSee($customer->name);
});

it('allows a user to create a customer for the active tenant', function () {
    $customerData = Customer::factory()->make()->toArray();

    $response = $this->post('/customers', $customerData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('customers', [
        'name' => $customerData['name'],
        'tenant_id' => $this->tenant->id,
    ]);
});

it('allows a user to update a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
    $updatedData = ['name' => 'Updated Name'];

    $response = $this->put("/customers/{$customer->id}", $updatedData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
    ]);
});

it('allows a user to delete a customer of the active tenant', function () {
    $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->delete("/customers/{$customer->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

it('prevents a user from managing customers of an inactive tenant', function () {
    $inactiveTenant = Tenant::factory()->create();
    $customer = Customer::factory()->create(['tenant_id' => $inactiveTenant->id]);

    $response = $this->get("/customers/{$customer->id}");
    $response->assertStatus(403);

    $response = $this->post('/customers', ['name' => 'Unauthorized']);
    $response->assertStatus(403);

    $response = $this->put("/customers/{$customer->id}", ['name' => 'Unauthorized']);
    $response->assertStatus(403);

    $response = $this->delete("/customers/{$customer->id}");
    $response->assertStatus(403);
});
