<?php

namespace App\Policies;

use App\User;
use App\Module;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
{
    use HandlesAuthorization;

    public function module(User $user, Module $module)
    {
        return $user->getModules()->where('code', $module->code)->count();;
    }
}
