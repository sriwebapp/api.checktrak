<?php

namespace App\Policies;

use App\User;
use App\Check;
use App\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->getActions()->where('code', 'crt')->count();
    }

    public function transmit(User $user, Company $company, Collection $checks)
    {
        $transmittable = $checks->every( function($check) use ($company) {
            return $check->company == $company && $check->received;
        });

        $accessible = $user->getActions()->where('code', 'trm')->count();

        return $transmittable && $accessible;
    }

    public function receive(User $user, Company $company, Collection $checks)
    {
        $receivable = $checks->every( function($check) use ($company) {
            return $check->company == $company && ! $check->received;
        });

        $accessible = $user->getActions()->where('code', 'rcv')->count();

        return $receivable && $accessible;
    }
}
