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

    public function receivedTransmittals(Company $company)
    {
        $transmittals = Auth::user()->branch->transmittals()
            ->where('ref' , 'like', $company->code . '%')
            ->where('returned', null)
            ->orderBy('id', 'desc')
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
        });
    }

    public function users()
    {
        return User::get();
    }
}
