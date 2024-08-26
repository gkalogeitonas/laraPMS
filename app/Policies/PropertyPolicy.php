<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;


class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //dd($user->tenant->id);
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property)
    {
        return $user->tenant->id === $property->tenant->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property)
    {

        return $user->tenant->id === $property->tenant->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property)
    {
        return $user->tenant->id === $property->tenant->id;
    }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Property $property): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Property $property): bool
    // {
    //     //
    // }
}
