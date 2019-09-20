<?php

namespace App\Http\Controllers;

use App\User;
use App\Access;
use App\Action;
use App\Branch;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return User::with('branch')->with('access')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:191|unique:users',
            'branch_id' => 'required|integer|exists:branches,id',
            'access_id' => 'required|integer|exists:accesses,id',
        ]);

        $request['password'] = bcrypt(config('app.default_pass'));

        $user = User::create($request->all());

        Log::info($request->user()->name . ' created new user.');

        return [
            'user' => $user,
            'message' => 'User successfully recorded.',
        ];
    }

    public function show(User $user)
    {
        $this->authorize('module', $this->module);

        $user->access;
        $user->actions;
        $user->modules;
        $user->branches;

        return $user;
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:20|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        $user->update($request->only('name', 'username', 'email', 'branch_id', 'active'));

        Log::info($request->user()->name . ' updated a user: ' . $user->email);

        return ['message' => 'User successfully updated.'];
    }

    public function destroy(User $user)
    {
        abort(403);
    }

    public function access(Request $request, User $user)
    {
        $this->authorize('module', $this->module);

        $request->validate(['access_id' => 'required|integer|exists:accesses,id']);

        $access = Access::find($request->get('access_id'));

        $access->users()->save($user);

        $actions = ! $access->action ? Action::whereIn('code', $request->get('actions'))->get() : [];
        $branches = ! $access->branch ? Branch::whereIn('code', $request->get('branches'))->get() : [];
        $modules = ! $access->module ? Module::whereIn('code', $request->get('modules'))->get() : [];

        $user->actions()->sync($actions);
        $user->branches()->sync($branches);
        $user->modules()->sync($modules);

        Log::info($request->user()->name . ' updated a user access: ' . $user->email);

        return ['message' => 'User access successfully updated.'];
    }
}
