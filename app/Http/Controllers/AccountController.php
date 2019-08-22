<?php

namespace App\Http\Controllers;

use App\Module;
use App\Account;
use App\Company;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'acc')->first();;
    }

    public function index(Company $company)
    {
        $this->authorize('module', $this->module);

        return $company->accounts;
    }

    public function store(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'bank' => 'required|min:2|max:5',
            'number' => 'required|integer|min:10',
            'address' => 'required|string|max:191',
            'tel' => 'required|max:50',
            'email' => 'required|email',
            'contact_person' => 'required|string|max:191',
            'designation' => 'required|string|max:191',
            'fax' => 'required|string|max:50',
            'purpose' => 'required|string',
        ]);

        $bank = strtoupper($request->get('bank'));

        Account::create([
            'company_id' => $company->id,
            'bank' => $bank,
            'code' => $bank . '-' . substr($request->get('number'), -2),
            'number' => $request->get('number'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'email' => $request->get('email'),
            'contact_person' => $request->get('contact_person'),
            'designation' => $request->get('designation'),
            'fax' => $request->get('fax'),
            'purpose' => $request->get('purpose'),
        ]);

        return ['message' => 'Bank Account successfully recorded.'];
    }

    public function show(Company $company, Account $account)
    {
        $this->authorize('module', $this->module);

        abort_unless($account->company_id === $company->id, 404, 'Not Found');

        return $account;
    }

    public function update(Request $request, Company $company, Account $account)
    {
        $this->authorize('module', $this->module);

        abort_unless($account->company_id === $company->id, 403, 'Unauthorized');

        $request->validate([
            'bank' => 'required|min:2|max:5',
            'number' => 'required|integer|min:10',
            'address' => 'required|string|max:191',
            'tel' => 'required|max:50',
            'email' => 'required|email',
            'contact_person' => 'required|string|max:191',
            'designation' => 'required|string|max:191',
            'fax' => 'required|string|max:50',
            'purpose' => 'required|string',
        ]);

        $bank = strtoupper($request->get('bank'));

        $account->update([
            'bank' => $bank,
            'code' => $bank . '-' . substr($request->get('number'), -2),
            'number' => $request->get('number'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'email' => $request->get('email'),
            'contact_person' => $request->get('contact_person'),
            'designation' => $request->get('designation'),
            'fax' => $request->get('fax'),
            'purpose' => $request->get('purpose'),
        ]);

        return ['message' => 'Bank Account successfully updated.'];
    }

    public function destroy($id)
    {
        abort(403);
    }
}
