<?php

namespace App\Policies;

use App\Models\BookingStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingStatusPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasActiveTenant();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookingStatus $bookingStatus): bool
    {
        return $user->getActiveTenant()->id === $bookingStatus->tenant_id;
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
    public function update(User $user, BookingStatus $bookingStatus): bool
    {
        return $user->getActiveTenant()->id === $bookingStatus->tenant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingStatus $bookingStatus): bool
    {
        return $user->hasActiveTenant() && ($user->getActiveTenant()->id === $bookingStatus->tenant_id);
    }
}
