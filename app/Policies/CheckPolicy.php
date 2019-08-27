<?php

namespace App\Policies;

use App\User;
use App\Check;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Check $check)
    {
        //
    }

    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the check.
     *
     * @param  \App\User  $user
     * @param  \App\Check  $check
     * @return mixed
     */
    public function update(User $user, Check $check)
    {
        //
    }

    /**
     * Determine whether the user can delete the check.
     *
     * @param  \App\User  $user
     * @param  \App\Check  $check
     * @return mixed
     */
    public function delete(User $user, Check $check)
    {
        //
    }

    /**
     * Determine whether the user can restore the check.
     *
     * @param  \App\User  $user
     * @param  \App\Check  $check
     * @return mixed
     */
    public function restore(User $user, Check $check)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the check.
     *
     * @param  \App\User  $user
     * @param  \App\Check  $check
     * @return mixed
     */
    public function forceDelete(User $user, Check $check)
    {
        //
    }
}
