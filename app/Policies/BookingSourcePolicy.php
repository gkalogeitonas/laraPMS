<?php

namespace App\Policies;

use App\Models\BookingSource;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingSourcePolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookingSource $BookingSource): bool
    {
        return $user->getActiveTenant()->id === $BookingSource->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasActiveTenant();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookingSource $BookingSource): bool
    {
        return $user->getActiveTenant()->id === $BookingSource->tenant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingSource $BookingSource): bool
    {
        return $user->hasActiveTenant() && ($user->getActiveTenant()->id === $BookingSource->tenant_id);
    }
}
