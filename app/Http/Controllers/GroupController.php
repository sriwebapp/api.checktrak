<?php

namespace App\Http\Controllers;

use App\Group;
use App\Action;
use App\Branch;
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

        return Group::get();
    }

    public function store(Request $request)
    {
        abort(403);
    }

    public function show(Group $group)
    {
        $this->authorize('module', $this->module);

        $group->actions;
        $group->branches;
        $group->modules;

        return $group;
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'action' => 'required|integer',
            'branch' => 'required|integer',
            'module' => 'required|integer',
        ]);

        $group->update($request->only(['action', 'branch', 'module']));

        $actions = $request->get('action') === 1 ? Action::whereIn('code', $request->get('actions'))->get() : [];
        $branches = $request->get('branch') === 1 ? Branch::whereIn('code', $request->get('branches'))->get() : [];
        $modules = $request->get('module') === 1 ? Module::whereIn('code', $request->get('modules'))->get() : [];

        $group->actions()->sync($actions);
        $group->branches()->sync($branches);
        $group->modules()->sync($modules);

        Log::info($request->user()->name . ' updated group access: ' . $group->name);

        return ['message' => 'Group Access successfully updated.'];
    }

    public function destroy(Group $group)
    {
        abort(403);
    }
}
