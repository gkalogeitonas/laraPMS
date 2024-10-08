<?php

use App\Models\BookingStatus;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('belongs to a tenant', function () {
    $data = createUserWithTenant();
    $tenant = $data['tenant'];

    $bookingStatus = BookingStatus::create([
        'tenant_id' => $tenant->id,
        'name' => 'Confirmed'
    ]);

    expect($bookingStatus->tenant)->toBeInstanceOf(Tenant::class);
    expect($bookingStatus->tenant->id)->toBe($tenant->id);
});
