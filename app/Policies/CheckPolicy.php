<?php

namespace App\Policies;

use App\User;
use App\Check;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->getActions()->where('code', 'crt')->count();
    }

    public function transmit(User $user)
    {
        return $user->getActions()->where('code', 'trm')->count();
    }
}
