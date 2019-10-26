<?php

namespace App\Policies;

use App\User;
use App\Check;
use App\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->getActions()->where('code', 'crt')->count();
    }

    public function import(User $user)
    {
        return $user->getActions()->where('code', 'imt')->count();
    }

    public function transmit(User $user, Company $company, Collection $checks)
    {
        $transmittable = $checks->every( function($check) use ($company) {
            return $check->company == $company
                && $check->received
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
                && $user->branch == $check->branch;
        });

        $accessible = $user->getActions()->where('code', 'rcv')->count();

        return $receivable && $accessible;
    }

    public function claim(User $user, Company $company, Collection $checks)
    {
        $claimable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->branch == $check->branch
                && $check->received
                && in_array($check->status_id, [1, 2, 4]); /*created, transmitted, returned*/
        });

        $accessible = $user->getActions()->where('code', 'clm')->count();

        return $claimable && $accessible;
    }

    public function clear(User $user, Check $check, Company $company)
    {
        $clearable = $check->company == $company
                && $user->getGroups()->where('id', $check->group->id )->count()
                && $check->status_id === 3; /*claimed*/

        $accessible = $user->getActions()->where('code', 'clr')->count();

        return $clearable && $accessible;
    }

    public function return(User $user, Collection $checks)
    {
        $returnable = $checks->every( function($check) use ($user) {
            return $user->branch == $check->branch
                && $check->received;
        });

        $accessible = $user->getActions()->where('code', 'rtn')->count();

        return $accessible && $returnable;
    }

    public function cancel(User $user, Company $company, Collection $checks)
    {
        $cancelable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getGroups()->where('id', $check->group->id )->count()
                && $check->received
                && in_array($check->status_id, [1, 4]); /*created, returned*/
        });

        $accessible = $user->getActions()->where('code', 'cnl')->count();

        return $accessible && $cancelable;
    }

    public function stale(User $user, Company $company, Collection $checks)
    {
        $stalable = $checks->every( function($check) use ($company, $user) {
            return $check->company == $company
                && $user->getGroups()->where('id', $check->group->id )->count()
                && $check->date <= Carbon::now()->subDays(80)->format('Y-m-d')
                && ! in_array($check->status_id, [5, 6, 7]); /*cancelled, cleared, staled*/
        });

        $accessible = $user->getActions()->where('code', 'stl')->count();

        return $accessible && $stalable;
    }

    public function edit(User $user, Check $check, Company $company)
    {
        $editable = $check->company == $company
            && $check->status_id !== 6; /*cleared*/

        $accessible = $user->getActions()->where('code', 'edt')->count();

        return $accessible && $editable;
    }

    public function delete(User $user, Check $check, Company $company)
    {
        $deletable = $check->company == $company
            && $check->status_id === 1; /*created*/

        $accessible = $user->getActions()->where('code', 'dlt')->count();

        return $accessible && $deletable;
    }

    public function undo(User $user, Check $check, Company $company)
    {
        $undoable = $check->company == $company
            && $check->history()->where('action_id', '<>', 3)->count() >= 2
            && $check->history()->orderBy('id', 'desc')->first()->action_id !== 11 /*undo*/
            && $check->history()->orderBy('id', 'desc')->first()->action_id !== 12; /*stale*/

        $accessible = $user->getActions()->where('code', 'und')->count();

        return $accessible && $undoable;
    }

    public function show(User $user, Check $check, Company $company)
    {
        $showable = $check->company == $company
            && $user->getGroups()->where('id', $check->group->id )->count();

        return $showable;
    }
}
