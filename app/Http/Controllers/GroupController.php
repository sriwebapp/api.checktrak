<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'grp')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return Group::with('branch')->with('incharge')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|min:3|max:191|unique:groups',
            'branch_id' => 'required|integer|exists:branches,id',
            'incharge' => 'array'
        ]);

        $users = User::whereIn('id', $request->get('incharge'))->get();

        Group::create([
            'name' => $request->get('name'),
            'branch_id' => $request->get('branch_id'),
        ])->incharge()->sync($users);

        Log::info($request->user()->name . ' created new group.');

        return ['message' => 'Group successfully recorded.'];
    }

    public function show(Group $group)
    {
        $this->authorize('module', $this->module);

        $group->incharge;

        return $group;
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|string|min:3|max:191|unique:groups,name,' . $group->id,
            'branch_id' => 'required|integer|exists:branches,id',
            'incharge' => 'array'
        ]);

        $users = User::whereIn('id', $request->get('incharge'))->get();

        $group->update([
            'name' => $request->get('name'),
            'branch_id' => $request->get('branch_id'),
        ]);

        $group->incharge()->sync($users);

        Log::info($request->user()->name . ' updated a group: ' . $group->name);

        return ['message' => 'Group successfully updated.'];
    }

    public function destroy($id)
    {
        abort(403);
    }
}
