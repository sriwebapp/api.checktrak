<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
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
        return $company->accounts;
    }

    public function actions()
    {
        return Action::get();
    }

    public function branches()
    {
        return Branch::get();
    }

    public function checks(Transmittal $transmittal)
    {
        return $transmittal->checks()
            ->where('received', 0)
            ->orWhere('status_id', '<>', 3)
            ->with('account')
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

    public function groups()
    {
        return Group::with('actions')->with('branches')->with('modules')->get();
    }

    public function modules()
    {
        return Module::get();
    }

    public function payees(Company $company)
    {
        return $company->payees()->with('group')->get();
    }

    public function payeeGroup()
    {
        return PayeeGroup::get();
    }

    public function series(Company $company, Branch $branch)
    {
        $year = date('Y');

        $transmittal = $branch->transmittals()
            ->where('ref' , 'like', $company->code . '%')
            ->where('ref' , 'like', '%' . $year . '%')
            ->orderBy('id', 'desc')
            ->first();

        $series = $transmittal ?
            sprintf('%04s', explode('-', $transmittal->ref)[3] + 1) :
            '0000';

        return [
            'ref' => $company->code . '-' . $branch->code . '-' . $year . '-' . $series,
            'incharge' => $branch->incharge_id
        ];
    }

    // public function sentTransmittals(Company $company)
    // {
    //     $transmittals = Auth::user()->branch->transmittals()
    //         ->where('ref' , 'like', $company->code . '%')
    //         ->where('returned', null)
    //         ->where('received' , 0)
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     return $transmittals;
    // }

    public function users()
    {
        return User::get();
    }
}
