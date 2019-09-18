<?php

namespace App\Http\Controllers;

use App\User;
use App\Branch;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'bra')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return Branch::with('incharge')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'code' => 'required|string|min:2|max:10|unique:branches',
            'name' => 'required|string|min:3|max:191|unique:branches',
            'incharge_id' => [
                /*'required',*/
                'integer',
                'nullable',
                Rule::in(User::where('active', 1)->pluck('id'))
            ],
        ]);

        Branch::create([
            'code' => strtoupper($request->get('code')),
            'name' => $request->get('name'),
            'incharge_id' => $request->get('incharge_id'),
        ]);

        Log::info($request->user()->name . ' created new branch.');

        return ['message' => 'Branch successfully recorded.'];
    }

    public function show(Branch $branch)
    {
        $this->authorize('module', $this->module);

        $branch->incharge;

        return $branch;
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'code' => 'required|string|min:2|max:10|unique:branches,code,' . $branch->id,
            'name' => 'required|string|min:3|max:191|unique:branches,name,' . $branch->id,
            'incharge_id' => [
                /*'required',*/
                'integer',
                'nullable',
                Rule::in(User::where('active', 1)->pluck('id'))
            ],
        ]);

        $branch->update([
            'code' => strtoupper($request->get('code')),
            'name' => $request->get('name'),
            'incharge_id' => $request->get('incharge_id'),
        ]);

        Log::info($request->user()->name . ' updated a branch: ' . $branch->code);

        return ['message' => 'Branch successfully updated.'];
    }

    public function destroy($id)
    {
        abort(403);
    }
}
