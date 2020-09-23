<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Access;
use App\Action;
use App\Branch;
use App\Module;
use App\Report;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserRegisteredNotification;

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

        return User::with('branch')->with('access')->orderBy('id', 'desc')->get();
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

        $password = Str::random(8);

        $request['password'] = bcrypt($password);

        $user = User::create($request->all());

        $user->notify(new UserRegisteredNotification($user, $password));

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
        $user->groups;
        $user->reports;

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
        $this->authorize('module', $this->module);

        abort_if($user->history->count(), 400, "Unable to delete: User is involved in check transactions.");
        abort_if($user->createdTransmittals->count(), 400, "Unable to delete: User is involved in check transmittals.");
        abort_if($user->inchargeTransmittals->count(), 400, "Unable to delete: User is incharge in check transmittals.");
        abort_if($user->returnedTransmittals->count(), 400, "Unable to delete: User is incharge in check transmittals.");
        abort_if($user->imports->count(), 400, "Unable to delete: User is incharge in check imports.");

        $user->delete();

        Log::info( Auth::user()->name . ' deleted user: ' . $user->name );

        return ['message' => 'User successfully deleted.'];
    }

    public function access(Request $request, User $user)
    {
        $this->authorize('module', $this->module);

        $request->validate(['access_id' => 'required|integer|exists:accesses,id']);

        $access = Access::find($request->get('access_id'));

        $access->users()->save($user);

        $actions = ! $access->action ? Action::whereIn('code', $request->get('actions'))->get() : [];
        $groups = ! $access->group ? Group::whereIn('id', $request->get('groups'))->get() : [];
        $modules = ! $access->module ? Module::whereIn('code', $request->get('modules'))->get() : [];
        $reports = ! $access->report ? Report::whereIn('code', $request->get('reports'))->get() : [];

        $user->actions()->sync($actions);
        $user->groups()->sync($groups);
        $user->modules()->sync($modules);
        $user->reports()->sync($reports);

        Log::info($request->user()->name . ' updated a user access: ' . $user->email);

        return ['message' => 'User access successfully updated.'];
    }
}
