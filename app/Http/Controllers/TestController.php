<?php

namespace App\Http\Controllers;

use App\User;
use App\Action;
use App\Branch;
use App\Module;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'usr')->first();
    }

    public function index(Request $request)
    {
        // return $branch->inCharge;

        $user = User::findOrFail($request->get('id'));

        return $user->getActions()->where('code', 'crts')->count();
    }
}