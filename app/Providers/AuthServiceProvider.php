<?php

namespace App\Providers;

use App\Models\Property;
use App\Policies\PropertyPolicy;
use App\Models\Room;
use App\Policies\RoomPolicy;
use App\Models\Customer;
use App\Policies\CustomerPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Property::class => PropertyPolicy::class,
        Room::class => RoomPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
