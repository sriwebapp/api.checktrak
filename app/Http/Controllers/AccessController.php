<?php

namespace App\Http\Controllers;

use App\Group;
use App\Access;
use App\Action;
use App\Module;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'acs')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return Access::get();
    }

    public function store(Request $request)
    {
        abort(403);
    }

    public function show(Access $access)
    {
        $this->authorize('module', $this->module);

        $access->actions;
        $access->groups;
        $access->modules;
        $access->reports;

        return $access;
    }

    public function update(Request $request, Access $access)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'action' => 'required|integer',
            'group' => 'required|integer',
            'module' => 'required|integer',
            'report' => 'required|integer',
        ]);

        $access->update($request->only(['action', 'group', 'module', 'report']));

        $actions = $request->get('action') === 1 ? Action::whereIn('code', $request->get('actions'))->get() : [];
        $groups = $request->get('group') === 1 ? Group::whereIn('id', $request->get('groups'))->get() : [];
        $modules = $request->get('module') === 1 ? Module::whereIn('code', $request->get('modules'))->get() : [];
        $reports = $request->get('report') === 1 ? Report::whereIn('code', $request->get('reports'))->get() : [];

        $access->actions()->sync($actions);
        $access->groups()->sync($groups);
        $access->modules()->sync($modules);
        $access->reports()->sync($reports);

        Log::info($request->user()->name . ' updated accessibility: ' . $access->name);

        return ['message' => 'Accessibility successfully updated.'];
    }

    public function destroy(Access $access)
    {
        abort(403);
    }
}
