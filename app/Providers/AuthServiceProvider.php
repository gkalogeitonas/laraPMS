<?php

namespace App\Providers;

use App\Models\Property;
use App\Policies\PropertyPolicy;
use App\Models\Room;
use App\Policies\RoomPolicy;
use App\Models\Customer;
use App\Policies\CustomerPolicy;
use App\Models\Booking;
use App\Policies\BookingPolicy;
use App\Models\BookingStatus;
use App\Policies\BookingStatusPolicy;
use App\Models\BookingSource;
use App\Policies\BookingSourcePolicy;
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
        Booking::class => BookingPolicy::class,
        BookingStatus::class => BookingStatusPolicy::class,
        BookingSource::class => BookingSourcePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
