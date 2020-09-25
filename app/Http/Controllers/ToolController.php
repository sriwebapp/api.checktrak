<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Access;
use App\Action;
use App\Branch;
use App\Module;
use App\Report;
use App\Status;
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
        return $company->accounts()
            ->where('active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function actions()
    {
        return Action::get();
    }

    public function status()
    {
        return Status::get();
    }

    public function branches()
    {
        return Branch::orderBy('id', 'desc')->get();
    }

    public function groups()
    {
        return Branch::has('groups')->with('groups')->get();
    }

    public function staledChecks(Company $company)
    {
        return $company->checks()
            ->where('date', '<=', Carbon::now()->subDays(180)->format('Y-m-d'))
            ->whereNotIn('status_id', [5, 6, 7]) /*cancelled, cleared, staled*/
            ->with('status')
            ->with('payee')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function checks(Transmittal $transmittal)
    {
        return $transmittal->checks()
            ->with('history')
            ->with('status')
            ->with('payee')
            ->orderBy('id', 'desc')
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
        return Access::with('actions')
            ->with('groups')
            ->with('modules')
            ->with('reports')
            ->get();
    }

    public function modules()
    {
        return Module::get();
    }

    public function reports()
    {
        return Report::get();
    }

    public function payees(Request $request, Company $company)
    {
        return $company->payees()
            ->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('search') . '%');
            })
            ->orderBy('id', 'desc')
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
            '0001';

        return [
            'series' => $series,
            'ref' => $company->code . '-' . $branch->code . '-' . $year . '-' . $series,
            'groups' => $branch->groups,
        ];
    }

    public function receivedTransmittals(Company $company)
    {
        $groups = Auth::user()->getGroups()->pluck('id');

        return $company->transmittals()
            ->where('branch_id', Auth::user()->branch->id)
            ->whereIn('group_id', $groups)
            ->orderBy('id', 'desc')
            ->where( function($q) {
                $q->where( function($x) {
                    $x->whereColumn('received_checks', 'sent_checks')
                        ->where('returned', null);
                })->orWhere( function($x) {
                    $x->where('returned_all', 0)
                        ->where('returned', '<>', null);
                });
            })->get();
    }

    public function sentTransmittals(Company $company)
    {
        $groups = Auth::user()->getGroups()->pluck('id');

        return $company->transmittals()
            ->where('branch_id', Auth::user()->branch->id)
            ->whereIn('group_id', $groups)
            ->whereColumn('received_checks', '<>', 'sent_checks')
            ->where('returned', null)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function returnedTransmittals(Company $company)
    {
        $groups = Auth::user()->getGroups()->pluck('id');

        return $company->transmittals()
            ->whereIn('group_id', $groups)
            ->whereColumn('received_checks', '<>', 'sent_checks')
            ->where('returned', '<>', null)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function users()
    {
        return User::where('active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function branchUsers(Branch $branch)
    {
        return $branch->users()->orderBy('id', 'desc')->get();
    }

    public function groupIncharge(Group $group)
    {
        return $group->incharge()->where('active', 1)->get();
    }

    public function transmittals(Request $request, Company $company)
    {
        return $company->transmittals()
            ->where('ref', 'like', '%' . $request->get('search') . '%')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
    }

    public function masterlistReport(Company $company)
    {
        return  [
            'accounts' =>  $company->accounts()->where('active', 1)->get(),
            'payees' => $company->payees()->take(10)->get(),
            'transmittals' => $company->transmittals()->take(10)->get(),
            'status' => Status::get(),
            'branches' => Branch::where('active', 1)->get(),
            'groups' => Group::where('active', 1)->get(),
            'users' => User::where('active', 1)->get(),
        ];
    }
}
