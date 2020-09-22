<?php

namespace App\Http\Controllers;

use App\Module;
use App\Account;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        return $company->accounts()->orderBy('id', 'desc')->get()->each(function($account) {
            $account->available_checkbook = $account->availableCheckBooks()->count();
            $account->need_reorder = $account->needReorder();
            $account->last_check = $account->latestCheck();
        });
    }

    public function store(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $bank = strtoupper($request->get('bank'));

        $request->validate([
            'bank' => 'required|min:2|max:5',
            'number' => 'required|min:10|regex:/^[\d-]*$/i|unique2:accounts,number,bank,' . $bank,
            'address' => 'max:191',
            'tel' => /*required*/ 'max:50',
            'email' => /*required*/ 'email|nullable',
            'contact_person' => /*required|string*/ 'max:191',
            'designation' => /*required|string*/ 'max:191',
            'fax' => /*required|string*/ 'max:50',
            'purpose' => 'required|string',
            'reorder_point' => 'required|integer|min:0|max:100',
        ]);

        Account::create([
            'company_id' => $company->id,
            'bank' => $bank,
            'code' => $bank . '-' . substr(str_replace('-', '', $request->get('number')), -2),
            'number' => $request->get('number'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'email' => $request->get('email'),
            'contact_person' => $request->get('contact_person'),
            'designation' => $request->get('designation'),
            'fax' => $request->get('fax'),
            'purpose' => $request->get('purpose'),
            'reorder_point' => $request->get('reorder_point'),
        ]);

        Log::info($request->user()->name . ' created new account.');

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
            'number' => 'required|regex:/^[\d -]*$/i|min:10',
            'address' => 'max:191',
            'tel' => /*required*/ 'max:50',
            'email' => /*required*/ 'email|nullable',
            'contact_person' => /*required|string*/ 'max:191',
            'designation' => /*required|string*/ 'max:191',
            'fax' => /*required|string*/ 'max:50',
            'purpose' => 'required|string',
            'reorder_point' => 'required|integer|min:0|max:100',
        ]);

        $bank = strtoupper($request->get('bank'));

        $account->update([
            'bank' => $bank,
            'code' => $bank . '-' . substr(str_replace('-', '', $request->get('number')), -2),
            'number' => $request->get('number'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'email' => $request->get('email'),
            'contact_person' => $request->get('contact_person'),
            'designation' => $request->get('designation'),
            'fax' => $request->get('fax'),
            'purpose' => $request->get('purpose'),
            'active' => $request->get('active'),
            'reorder_point' => $request->get('reorder_point'),
        ]);

        Log::info($request->user()->name . ' updated an account: ' . $account->code);

        return ['message' => 'Bank Account successfully updated.'];
    }

    public function destroy(Company $company, Account $account)
    {
        $this->authorize('module', $this->module);

        abort_unless($account->company_id === $company->id, 403, 'Unauthorized');

        abort_if($account->checks->count(), 400, "Unable to delete: Bank Account is involved in check transactions.");

        $account->delete();

        Log::info( Auth::user()->name . ' deleted account: ' . $account->code );

        return ['message' => 'Account successfully deleted.'];
    }
}
