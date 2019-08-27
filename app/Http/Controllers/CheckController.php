<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Action;
use App\Company;
use App\History;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CheckController extends Controller
{
    // show all for dev
    public function index(Company $company)
    {
        return $company->checks;
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
            'company_id' => $company->id,
            'account_id' => $request->get('account_id'),
            'payee_id' => $request->get('payee_id'),
            'amount' => $request->get('amount'),
            'details' => $request->get('details'),
            'date' => $request->get('date'),
        ]);

        $this->recordLog($check, 'crt');

        return ['message' => 'Check successfully created.'];
    }

    public function transmit(Request $request, Company $company)
    {
        // validate
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'incharge' => 'required|exists:users,id',
            'date' => 'required|date',
            'ref' => 'required|unique:transmittals,ref',
            'series' => 'required|integer',
            'checks' => 'required|array'
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");
        // check authorization
        $this->authorize('transmit', [Check::class, $company, $checks]);
        // create transmittal
        Transmittal::create([
            'branch_id' => $request->get('branch_id'),
            'user_id' => $request->user()->id,
            'incharge' => $request->get('incharge'),
            'date' => $request->get('date'),
            'due' => Carbon::create( $request->get('date') )->addDays(30)->format("Y/m/d"),
            'ref' => $request->get('ref'),
            'series' => $request->get('series'),
        ])->checks()->sync($checks);
        // record history and update status
        $checks->each( function($check) {
            $check->update([ 'status_id' => 2, 'received' => 0 ]); // transmitted

            $this->recordLog($check, 'trm');
        });

        return ['message' => 'Checks successfully transmitted.'];
    }

    public function receive(Request $request, Company $company)
    {
        $request->validate(['checks' => 'required|array']);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('receive', [Check::class, $company, $checks]);

        $checks->each( function($check) {
            $check->update(['received' => 1]);

            $this->recordLog($check, 'rcv');
        });

        return ['message' => 'Checks successfully received.'];
    }

    public function claim(Request $request, Company $company)
    {
        $request->validate([
            'checks' => 'required|array',
            'remarks' => 'max:191',
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('claim', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            $check->update(['status_id' => 3]);/*claimed*/

            $this->recordLog($check, 'clm', $request->get('remarks'));
        });

        return ['message' => 'Checks successfully received.'];
    }

    public function clear(Request $request, Company $company)
    {
        $request->validate(['checks' => 'required|array']);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('clear', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            $check->update(['status_id' => 6]); /*cleared*/

            $this->recordLog($check, 'clr');
        });

        return ['message' => 'Checks successfully cleared.'];
    }

    public function show(Company $company, Check $check)
    {
        abort_unless($check->company_id === $company->id, 404, 'Not Found');

        $check->history;
        // $check->transmittals;
        return $check;
    }
    // record check log
    protected function recordLog(Check $check, $action, $remarks = null)
    {
        History::create([
            'check_id' => $check->id,
            'action_id' => Action::where('code', $action)->first()->id,
            'user_id' => Auth::user()->id,
            'remarks' => $remarks
        ]);
    }
}
