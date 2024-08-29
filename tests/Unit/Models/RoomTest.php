<?php

use App\Models\Room;
use App\Models\Tenant;

it('can create a Room', function () {
    $room = Room::factory()->create();

    expect($room)->toBeInstanceOf(room::class);
});


test('a Room belongs to a tenant', function () {
    $tenant = Tenant::factory()->create();
    $room = Room::factory()->create(['tenant_id' => $tenant->id]);

    expect($room->tenant)->toBeInstanceOf(Tenant::class);
    expect($room->tenant->id)->toBe($tenant->id);

});
