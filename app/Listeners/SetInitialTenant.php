<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetInitialTenant
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        // Check if the user has any tenants
        if ($user->tenants()->exists()) {
            // Get the first tenant associated with the user
            $tenant = $user->tenants()->first();

            // Set the user's active tenant
            $user->setActiveTenant($tenant);
        }
    }
}
