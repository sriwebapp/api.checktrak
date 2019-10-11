<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Access;
use App\Action;
use App\Branch;
use App\Module;
use App\Company;
use Carbon\Carbon;
use App\PayeeGroup;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    public function accounts(Company $company)
    {
        return $company->accounts()->where('active', 1)->get();
    }

    public function actions()
    {
        return Action::get();
    }

    public function branches()
    {
        return Branch::get();
    }

    public function groups()
    {
        return Group::where('active', 1)
            ->orderBy('branch_id')->get();
    }

    public function checks(Transmittal $transmittal)
    {
        return $transmittal->checks()
            ->with('status')
            ->with('account')
            ->with('payee')
            ->get();
    }

    public function company($id)
    {
        return Company::findOrFail($id);
    }

    public function companies()
    {
        return Company::get();
    }

    public function access()
    {
        return Access::with('actions')->with('groups')->with('modules')->get();
    }

    public function modules()
    {
        return Module::get();
    }

    public function payees(Request $request, Company $company)
    {
        return $company->payees()
            ->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('search') . '%');
            })
            ->take(10)
            ->get();
    }

    public function payeeGroup()
    {
        return PayeeGroup::get();
    }

    public function transmittalRef(Company $company, Branch $branch)
    {
        $year = date('Y');

        $transmittal = $company->transmittals()
            ->where('branch_id', $branch->id)
            ->where('year', $year)
            ->orderBy('id', 'desc')
            ->first();

        $series = $transmittal ?
            sprintf('%04s', $transmittal->series + 1) :
            '0000';

        return [
            'series' => $series,
            'ref' => $company->code . '-' . $branch->code . '-' . $year . '-' . $series,
            'groups' => $branch->groups,
        ];
    }

    public function receivedTransmittals(Company $company)
    {
        $groups = Auth::user()->getGroups()->pluck('id');

        $transmittals = $company->transmittals()
            ->where('branch_id', Auth::user()->branch->id)
            ->whereIn('group_id', $groups)
            ->orderBy('id', 'desc')
            ->where('returned', null)
            ->with('checks')
            ->get();

        return $transmittals->filter(function ($transmittal) {
            $notClaimed = $transmittal->checks()
                ->where('status_id', 2)
                ->count();

            $received = $transmittal->checks()
                ->where('received', 0)
                ->count() === 0;

            return $notClaimed && $received;
        })->values()->all();
    }

    public function users()
    {
        return User::where('active', 1)->get();
    }

    public function branchUsers(Branch $branch)
    {
        return $branch->users()->where('active', 1)->get();
    }

    public function groupIncharge(Group $group)
    {
        return $group->incharge()->where('active', 1)->get();
    }
}
