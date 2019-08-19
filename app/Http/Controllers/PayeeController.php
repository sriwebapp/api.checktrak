<?php

namespace App\Http\Controllers;

use App\Payee;
use App\Module;
use Illuminate\Http\Request;

class PayeeController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'pye')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return Payee::with('group')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|max:191',
            'desc' => 'required|max:191',
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        Payee::create($request->only(['name', 'desc', 'payee_group_id']));

        return ['message' => 'Payee successfully recorded.'];
    }

    public function show(Payee $payee)
    {
        $this->authorize('module', $this->module);

        $payee->group;

        return $payee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payee $payee)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|max:191',
            'desc' => 'required|max:191',
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        $payee->update($request->only(['name', 'desc', 'payee_group_id']));

        return ['message' => 'Payee successfully updated .'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
