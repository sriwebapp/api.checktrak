<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Action;
use App\Branch;
use App\Module;
use App\Company;
use App\PayeeGroup;
use Illuminate\Http\Request;

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

    public function users()
    {
        return User::get();
    }
}
