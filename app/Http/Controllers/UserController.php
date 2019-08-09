<?php

namespace App\Http\Controllers;

use App\User;
use App\Module;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'usr')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return User::get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        $request['password'] = bcrypt(config('app.default_pass'));

        User::create($request->all());

        return ['message' => 'User successfully recorded.'];
    }

    public function show(User $user)
    {
        $this->authorize('module', $this->module);

        return $user->access();
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        $user->update($request->all());

        return ['message' => 'User successfully updated.'];
    }

    public function destroy($id)
    {
        abort(403);
    }
}
