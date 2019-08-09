<?php

namespace App\Http\Controllers;

use App\User;
use App\Action;
use App\Module;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail($request->get('id'));

        $user->branch = $user->getBranches()->pluck('code');
        $user->action = $user->getActions()->pluck('code');
        $user->module = $user->getModules()->pluck('code');
        // $user->branch = $user->getBranches();

        return $user;
    }
}
