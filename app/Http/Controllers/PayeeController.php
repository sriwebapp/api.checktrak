<?php

namespace App\Http\Controllers;

use App\Payee;
use App\Module;
use App\Company;
use Illuminate\Http\Request;

class PayeeController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'pye')->first();
    }

    public function index(Company $company)
    {
        $this->authorize('module', $this->module);

        return $company->payees()->with('group')->get();
    }

    public function store(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|max:191',
            'code' => 'required|max:20|unique2:payees,code,company_id,' . $company->id,
            'desc' => 'required|max:191',
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        Payee::create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'desc' => $request->get('desc'),
            'company_id' => $company->id,
            'payee_group_id' => $request->get('payee_group_id'),
        ]);

        return ['message' => 'Payee successfully recorded.'];
    }

    public function show(Company $company, Payee $payee)
    {
        $this->authorize('module', $this->module);

        abort_unless($payee->company_id === $company->id, 404, 'Not Found');

        $payee->group;

        return $payee;
    }

    public function update(Request $request, Company $company, Payee $payee)
    {
        $this->authorize('module', $this->module);

        abort_unless($payee->company_id === $company->id, 403, 'Unauthorized');

        $request->validate([
            'name' => 'required|max:191',
            'code' => 'required|max:20',
            'desc' => 'required|max:191',
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        $payee->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'desc' => $request->get('desc'),
            'payee_group_id' => $request->get('payee_group_id'),
        ]);

        return ['message' => 'Payee successfully updated .'];
    }

    public function destroy($id)
    {
        abort(403);
    }
}
