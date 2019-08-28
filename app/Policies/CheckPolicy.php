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
                && in_array($check->status_id, [1, 4]); /*created, returned*/
        });

        $accessible = $user->getActions()->where('code', 'trm')->count();

        return $transmittable && $accessible;
    }

    public function receive(User $user, Company $company, Collection $checks)
    {
        $receivable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && ! $check->received
                && $user->getBranches()->where('id', $check->branch()->id )->count();
        });

        $accessible = $user->getActions()->where('code', 'rcv')->count();

        return $receivable && $accessible;
    }

    public function claim(User $user, Company $company, Collection $checks)
    {
        $claimable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getBranches()->where('id', $check->branch()->id )->count()
                && $check->received
                && in_array($check->status_id, [1, 2, 4]); /*created, transmitted, returned*/
        });

        $accessible = $user->getActions()->where('code', 'clm')->count();

        return $claimable && $accessible;
    }

    public function clear(User $user, Company $company, Collection $checks)
    {
        $clearable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getBranches()->where('id', $check->branch()->id )->count()
                && $check->status_id === 3; /*claimed*/
        });

        $accessible = $user->getActions()->where('code', 'clr')->count();

        return $clearable && $accessible;
    }

    public function return(User $user, Collection $checks)
    {
        $returnable = $checks->every( function($check) use ($user) {
            return $user->getBranches()->where('id', $check->branch()->id )->count()
                && $check->received;
        });

        $accessible = $user->getActions()->where('code', 'rtn')->count();

        return $accessible && $returnable;
    }

    public function cancel(User $user, Company $company, Collection $checks)
    {
        $returnable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getBranches()->where('id', $check->branch()->id )->count()
                && $check->received
                && in_array($check->status_id, [1, 4]); /*created, returned*/
        });

        $accessible = $user->getActions()->where('code', 'cnl')->count();

        return $accessible && $returnable;
    }

    public function edit(User $user, Check $check, Company $company)
    {
        $editable = $check->company == $company
            && $check->status_id !== 6; /*cleared*/;

        $accessible = $user->getActions()->where('code', 'edt')->count();

        return $accessible && $editable;
    }
}
