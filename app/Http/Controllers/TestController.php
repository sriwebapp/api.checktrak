<?php

namespace App\Http\Controllers;

use App\User;
use App\Action;
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
        $user = User::findOrFail($request->get('id'));

        return $user->access();
    }
}
