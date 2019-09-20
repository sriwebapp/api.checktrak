<?php

namespace App\Http\Controllers;

use App\Access;
use App\Action;
use App\Branch;
use App\Module;
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
        $access->branches;
        $access->modules;

        return $access;
    }

    public function update(Request $request, Access $access)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'action' => 'required|integer',
            'branch' => 'required|integer',
            'module' => 'required|integer',
        ]);

        $access->update($request->only(['action', 'branch', 'module']));

        $actions = $request->get('action') === 1 ? Action::whereIn('code', $request->get('actions'))->get() : [];
        $branches = $request->get('branch') === 1 ? Branch::whereIn('code', $request->get('branches'))->get() : [];
        $modules = $request->get('module') === 1 ? Module::whereIn('code', $request->get('modules'))->get() : [];

        $access->actions()->sync($actions);
        $access->branches()->sync($branches);
        $access->modules()->sync($modules);

        Log::info($request->user()->name . ' updated accessibility: ' . $access->name);

        return ['message' => 'Accessibility successfully updated.'];
    }

    public function destroy(Access $access)
    {
        abort(403);
    }
}
