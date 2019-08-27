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
            return $check->company == $company
                && in_array($check->status_id, [1/*created*/, 4/*returned*/]);
        });

        $accessible = $user->getActions()->where('code', 'trm')->count();

        return $transmittable && $accessible;
    }

    public function receive(User $user, Company $company, Collection $checks)
    {
        $receivable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && ! $check->received
                && $user->getBranches()->where('id', $check->transmittals->reverse()->first()->branch->id )->count();
        });

        $accessible = $user->getActions()->where('code', 'rcv')->count();

        return $receivable && $accessible;
    }

    public function claim(User $user, Company $company, Collection $checks)
    {
        $claimable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getBranches()->where('id', $check->transmittals->reverse()->first()->branch->id )->count()
                && ! in_array($check->status_id, [3/*claimed*/, 5/*cancelled*/, 6/*cleared*/, 7/*staled*/]);
        });

        $accessible = $user->getActions()->where('code', 'clm')->count();

        return $claimable && $accessible;
    }
}
