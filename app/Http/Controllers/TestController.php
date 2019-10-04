<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Group;
use App\Action;
use App\Branch;
use App\Module;
use App\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\UserRegisteredNotification;

class TestController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'usr')->first();
    }

    public function index(Request $request)
    {
        return $request;
        // $company = Company::findOrFail($request->get('id'));

        // return $company->checks()->where('number', '1782810')->first();

        $user = User::findOrFail($request->get('id'));

        return $user->accessibility();
    }
}
