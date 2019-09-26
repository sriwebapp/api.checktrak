<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Group;
use App\Action;
use App\Branch;
use App\Company;
use App\History;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CheckController extends Controller
{
    // show all for dev
    public function index(Request $request, Company $company)
    {
        $sort = $request->get('sortBy') ? $request->get('sortBy')[0] : 'id';

        $order = $request->get('sortDesc') ?
            ($request->get('sortDesc')[0] ? 'desc' : 'asc') :
            'desc';

        $groups = Auth::user()->getGroups()->pluck('id');

        return $company->checks()
            ->whereIn('group_id', $groups)
            ->with('status')
            ->with('payee')
            ->with('account')
            ->with('group')
            ->with('branch')
            ->with('history')
            ->orderBy($sort, $order)
            ->paginate($request->get('itemsPerPage'));
    }

    public function create(Request $request, Company $company)
    {
        $this->authorize('create', Check::class);

        $request->validate([
            'check_number' => [
                'required',
                'min:6',
                'max:10',
                'unique2NotDeleted:checks,number,account_id,' . $request->get('account_id')
            ],
            'account_id' => ['required', Rule::in($company->accounts()->pluck('id'))],
            'payee_id' => ['required', Rule::in($company->payees()->pluck('id'))],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
        ]);

        $check = Check::create([
            'number' => $request->get('check_number'),
            'status_id' => 1, // created
            'company_id' => $company->id,
            'account_id' => $request->get('account_id'),
            'payee_id' => $request->get('payee_id'),
            'amount' => $request->get('amount'),
            'details' => $request->get('details'),
            'date' => $request->get('date'),
        ]);

        $this->recordLog($check, 'crt', $request->get('date'));

        Log::info($request->user()->name . ' created new check.');

        return ['message' => 'Check successfully created.'];
    }

    public function transmit(Request $request, Company $company)
    {
        // validate
        $request->validate([
            'group_id' => ['required', Rule::in(Group::where('id', '<>', 1)->pluck('id')) ],
            'incharge' => 'required|exists:users,id',
            'date' => 'required|date',
            'ref' => 'required|unique:transmittals,ref',
            'series' => 'required',
            'checks' => 'required|array'
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");
        // check authorization
        $this->authorize('transmit', [Check::class, $company, $checks]);
        // get group
        $group = Group::find($request->get('group_id'));
        // create transmittal
        Transmittal::create([
            'group_id' => $group->id,
            'branch_id' => $group->branch->id,
            'company_id' => $company->id,
            'year' => date('Y'),
            'series' => $request->get('series'),
            'user_id' => $request->user()->id,
            'incharge' => $request->get('incharge'),
            'date' => $request->get('date'),
            'due' => Carbon::create( $request->get('date') )->addDays(30)->format("Y/m/d"),
            'ref' => $company->code . '-' . $group->branch->code . '-' . date('Y') . '-' . $request->get('series'),
        ])->checks()->sync($checks);
        // record history and update status
        $checks->each( function($check) use ($request, $group) {
            $check->update([
                'status_id' => 2,
                'received' => 0,
                'group_id' => $group->id,
                'branch_id' => $group->branch->id
            ]); // transmitted

            $this->recordLog($check, 'trm', $request->get('date'));
        });

        Log::info($request->user()->name . ' transmitted checks.');

        return ['message' => 'Checks successfully transmitted.'];
    }

    public function receive(Request $request, Company $company/*, Transmittal $transmittal*/)
    {
        // $checks = $transmittal->checks()->where('received', 0)->get();

        $request->validate([
            'date' => 'required|date',
            'checks' => 'required|array',
            'remarks' => 'max:191',
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('receive', [Check::class, $company, $checks]);

        // $transmittal->update([ 'received' => 1 ]); // update transmittal

        $checks->each( function($check) use ($request) {
            $check->update(['received' => 1]);

            $this->recordLog($check, 'rcv', $request->get('date'), $request->get('remarks'));
        });

        Log::info($request->user()->name . ' received checks.');

        return ['message' => 'Checks successfully received.'];
    }

    public function claim(Request $request, Company $company)
    {
        $request->validate([
            'date' => 'required|date',
            'checks' => 'required|array',
            'remarks' => 'max:191',
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('claim', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            $check->update(['status_id' => 3]);/*claimed*/

            $this->recordLog($check, 'clm', $request->get('date'), $request->get('remarks'));
        });

        Log::info($request->user()->name . ' claimed checks.');

        return ['message' => 'Checks successfully claimed.'];
    }

    public function clear(Request $request, Company $company)
    {
        $request->validate([
            'check' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric|gt:0',
        ]);

        $check = Check::where('id', $request->get('check'))->first();
        // must be greater than zero
        abort_unless($check, 400, "No check selected!");

        $this->authorize('clear', [$check, $company]);

        $check->update(['status_id' => 6, 'cleared' => $request->get('amount')]); /*cleared*/

        $this->recordLog($check, 'clr', $request->get('date'));

        Log::info($request->user()->name . ' cleared checks.');

        return ['message' => 'Checks successfully cleared.'];
    }

    public function return(Request $request, Company $company)
    {
        $request->validate([
            'date' => 'required|date',
            'transmittal_id' => 'required|exists:transmittals,id'
        ]);

        $transmittal = Transmittal::findOrFail($request->get('transmittal_id'));

        $checks = $transmittal->checks()->where('status_id', 2)->get(); /*transmitted*/
        // must be greater than zero
        abort_unless($checks->count(), 400, "No checks available!");

        $this->authorize('return', [Check::class, $checks]);

        $transmittal->update([ 'returned' => Carbon::now() ]); // update transmittal

        $checks->each( function($check) use ($request) {
            $check->update([
                'status_id' => 4,
                'received' => 0,
                'group_id' => 1,
                'branch_id' => 1
            ]); // returned

            $this->recordLog($check, 'rtn', $request->get('date'));
        });

        Log::info($request->user()->name . ' returned checks.');

        return ['message' => 'Checks successfully returned.'];
    }

    public function cancel(Request $request, Company $company)
    {
        $request->validate([
            'date' => 'required|date',
            'checks' => 'required|array',
            'remarks' => 'required|max:191',
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('cancel', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            $check->update([ 'status_id' => 5]); // cancelled

            $this->recordLog($check, 'cnl', $request->get('date'), $request->get('remarks'));
        });

        Log::info($request->user()->name . ' cancelled checks.');

        return ['message' => 'Checks cancelled.'];
    }

    public function show(Company $company, $id)
    {
        $check = Check::withTrashed()->findOrFail($id);

        $this->authorize('show', [$check, $company]);

        $check->status;
        $check->payee;
        $check->group;
        $check->branch;
        $check->account;
        $check->transmittals;
        $check->history = $check->history()->with('action')->with('user')->get();

        return $check;
    }

    public function edit(Request $request, Company $company, Check $check)
    {
        $request->validate([ 'details' => 'required|max:191' ]);

        $this->authorize('edit', [$check, $company]);

        $check->update([ 'details' => $request->get('details') ]);

        $this->recordLog($check, 'edt', date('Y-m-d'), 'Details: ' . $request->get('details') );

        Log::info($request->user()->name . ' edited checks.');

        return ['message' => 'Check successfully updated.'];
    }

    public function delete(Request $request, Company $company, Check $check)
    {
        $request->validate([ 'remarks' => 'required|max:191' ]);

        $this->authorize('delete', [$check, $company]);

        $check->delete();

        $this->recordLog($check, 'dlt', date('Y-m-d'), $request->get('remarks'));

        Log::info($request->user()->name . ' deleted checks.');

        return ['message' => 'Check successfully deleted.'];
    }
    // record check log
    protected function recordLog(Check $check, $action, $date, $remarks = null)
    {
        History::create([
            'check_id' => $check->id,
            'action_id' => Action::where('code', $action)->first()->id,
            'user_id' => Auth::user()->id,
            'date' => $date,
            'remarks' => $remarks
        ]);
    }
}
