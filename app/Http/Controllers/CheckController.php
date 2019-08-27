<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Action;
use App\Company;
use App\History;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckController extends Controller
{
    // show all for dev
    public function index(Company $company)
    {
        return Check::whereIn('account_id', $company->accounts()->pluck('id'))->get();
    }

    public function create(Request $request, Company $company)
    {
        $this->authorize('create', Check::class);

        $request->validate([
            'account_id' => ['required', Rule::in($company->accounts()->pluck('id'))],
            'payee_id' => ['required', Rule::in($company->payees()->pluck('id'))],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
        ]);

        $check = Check::create([
            'status_id' => 1, // created
            'account_id' => $request->get('account_id'),
            'payee_id' => $request->get('payee_id'),
            'amount' => $request->get('amount'),
            'details' => $request->get('details'),
            'date' => $request->get('date'),
        ]);

        $this->recordLog($check, 'crt', $request->user());

        return ['message' => 'Check successfully recorded.'];
    }

    public function show(Company $company, Check $check)
    {
        $check->history;

        return $check;
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    protected function recordLog(Check $check, $action, User $user)
    {
        History::create([
            'check_id' => $check->id,
            'action_id' => Action::where('code', $action)->first()->id,
            'user_id' => $user->id
        ]);
    }
}
