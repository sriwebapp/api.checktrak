<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Action;
use App\Branch;
use App\Module;
use Illuminate\Http\Request;

class ToolController extends Controller
{
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
        return Group::with('actions')->with('branches')->with('modules')->get();
    }

    public function modules()
    {
        return Module::get();
    }

    public function users()
    {
        return User::get();
    }
}
