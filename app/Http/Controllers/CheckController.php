<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Group;
use App\Access;
use App\Action;
use App\Branch;
use App\Account;
use App\Company;
use App\History;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ChecksReceivedNotification;
use App\Notifications\ChecksReturnedNotification;
use App\Notifications\ChecksTransmittedNotification;

class CheckController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $filter = $request->get('filter');

        if ( $transmittal = $this->checkFilter($filter, 'transmittal_id') ) {
            // filter by transmittal
            $checks = Transmittal::find( $transmittal )->checks();
        } else {
            $checks = $company->checks();
        }

        $groups = Auth::user()->getGroups()->pluck('id');
        $sort = $request->get('sortBy') ? $request->get('sortBy')[0] : 'id';
        $order = $request->get('sortDesc') ?
            ($request->get('sortDesc')[0] ? 'desc' : 'asc') :
            'desc';

        return $checks
            ->where( function($q) use ($filter) {
                // filter by account_id
                if ( $account = $this->checkFilter($filter, 'account_id') ) {
                    $q->where('account_id', $account );
                }
                // filter by payee_id
                if ( $payee = $this->checkFilter($filter, 'payee_id') ) {
                    $q->where('payee_id', $payee );
                }
                // filter by check number
                if ( $number = $this->checkFilter($filter, 'number') ) {
                    $from = $number['from'] < $number['to'] ? $number['from'] : $number['to'];
                    $to = $number['from'] > $number['to'] ? $number['from'] : $number['to'];

                    $q->whereBetween('number', [$from, $to]);
                }
                // filter by posting date
                if ( $date = $this->checkFilter($filter, 'date') ) {
                    $from = $date['from'] < $date['to'] ? $date['from'] : $date['to'];
                    $to = $date['from'] > $date['to'] ? $date['from'] : $date['to'];

                    $q->whereBetween('date', [$from, $to]);
                }
                // filter by details
                if ( $detail = $this->checkFilter($filter, 'detail') ) {
                    $q->where('details', 'like', '%' . $detail . '%');
                }
                // filter by status
                if ( $status = $this->checkFilter($filter, 'status') ) {
                    $q->whereIn('status_id', $status['statuses'])
                        ->where('received', $status['received']);
                }
            })
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
                'regex:/^[\d]*$/i',
                'unique2NotDeleted:checks,number,account_id,' . $request->get('account_id')
            ],
            'account_id' => ['required', Rule::in($company->accounts()->pluck('id'))],
            'payee_id' => ['required', Rule::in($company->payees()->pluck('id'))],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'details' => 'max:50'
        ]);

        $checkbook = Account::find(request('account_id'))->checkbooks()
            ->where('start_series', '<=', request('check_number'))
            ->where('end_series', '>=', request('check_number'))
            ->whereRaw('length(start_series) = ' . strlen(request('check_number')))
            ->first();

        if (! $checkbook)
            return response()->json([
                    'message' => __('validation.no_check_book'),
                    'errors' => [ 'check_number' => [__('validation.no_check_book')] ]
                ], 422);

        $check = Check::create([
            'number' => $request->get('check_number'),
            'company_id' => $company->id,
            'account_id' => $request->get('account_id'),
            'payee_id' => $request->get('payee_id'),
            'amount' => $request->get('amount'),
            'details' => $request->get('details'),
            'date' => $request->get('date'),
            'status_id' => 1, // created
            'received' => 1, // received
            'branch_id' => 1, // head office
            'group_id' => 1, // disbursement
            'check_book_id' => $checkbook->id
        ]);
        // update checkbook
        $checkbook->update([
            'posted' => $postedChecks = $checkbook->postedChecks()->count(),
            'available' => $checkbook->total - $postedChecks,
        ]);
        // check if post dated
        $date = new Carbon($request->get('date')) > new Carbon(date('Y-m-d')) ? date('Y-m-d') : $request->get('date');

        $this->recordLog($check, 'crt', $date);

        Log::info($request->user()->name . ' created new check.');

        return ['message' => 'Check successfully created.'];
    }

    public function transmit(Request $request, Company $company)
    {
        ini_set('memory_limit', '2048M');

        $request->validate([ 'checks' => 'array|nullable' ]);
        // query checks
        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");
        // must be less than or equal 500
        abort_unless($checks->count() <= 500, 400, "Check limit of 500 exceeded.");
        // get last action
        $lastAction = History::whereIn('check_id', $checks->pluck('id'))
            ->orderBy('date', 'desc')
            ->where('active', 1)
            ->whereNotIn('action_id', [8, 11])/*edit, undo*/
            ->first();
        // validate
        $request->validate([
            'group_id' => ['required', Rule::in(Group::where('id', '<>', 1)->pluck('id')) ],
            'incharge' => 'required|exists:users,id',
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . $lastAction->date,
            ],
            'ref' => 'required|unique:transmittals,ref',
            'series' => 'required',

        ]);
        // check authorization
        $this->authorize('transmit', [Check::class, $company, $checks]);
        // get group
        $group = Group::find($request->get('group_id'));
        // create transmittals
        $transmittal = Transmittal::create([
            'group_id' => $group->id,
            'branch_id' => $group->branch->id,
            'company_id' => $company->id,
            'year' => date('Y'),
            'series' => $request->get('series'),
            'user_id' => $request->user()->id,
            'incharge' => $request->get('incharge'),
            'date' => $request->get('date'),
            'due' => Carbon::create( $request->get('date') )->addDays(30)->format("Y-m-d"),
            'ref' => $company->code . '-' . $group->branch->code . '-' . date('Y') . '-' . $request->get('series'),
            'sent_checks' => $checks->count(),
        ]);
        // sync checks
        $transmittal->checks()->sync($checks);
        // record history and update status
        $checks->each( function($check) use ($request, $group) {
            $check->update([
                'status_id' => 2,
                'received' => 0,
                'branch_id' => $group->branch->id
            ]); // transmitted

            $this->recordLog($check, 'trm', $request->get('date'));
        });

        \PDF::loadView('pdf.transmittal', compact('transmittal'))
            ->setPaper('letter', 'portrait')
            ->setWarnings(false)
            ->save( public_path() . '/pdf/transmittal/' . $transmittal->ref . '.pdf');

        Log::info($request->user()->name . ' transmitted checks.');

        $incharges = $transmittal->group->incharge;
        $incharges->push($request->user());
        $recipients = $incharges->merge(Access::find(2)->users); // administrators

        Notification::send($recipients, new ChecksTransmittedNotification($transmittal));

        return [
            'message' => 'Checks successfully transmitted.',
            'transmittal' => $transmittal->id,
        ];
    }

    public function receive(Request $request, Company $company)
    {
        ini_set('memory_limit', '2048M');

        $request->validate([
            'transmittal_id' => [ 'required', Rule::in(Transmittal::whereColumn('received_checks', '<>', 'sent_checks')->pluck('id')) ],
            'selectChecks' => 'required',
            'selectedChecks' => 'array',
            'remarks' => 'max:50',
        ]);

        $transmittal = Transmittal::findOrFail($request->get('transmittal_id'));

        $unreceivedChecks = $transmittal->checks()->where('received', 0)->get();

        $checks = $request->get('selectChecks') ?
            Check::whereIn('id', $request->get('selectedChecks'))->get():
            $transmittal->checks()->where('received', 0)->get();
        // return transmittals even all are claimed
        abort_if($request->get('selectChecks') && !$checks->count(), 400, "No check selected!");
        // get last action
        $lastAction = History::whereIn('check_id', $checks->pluck('id'))
            ->orderBy('date', 'desc')
            ->where('active', 1)
            ->whereNotIn('action_id', [8, 11])/*edit, undo*/
            ->first();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . $lastAction->date,
            ],
        ]);

        $this->authorize('receive', [Check::class, $company, $checks]);

        $transmittal->update([ 'received_checks' => $transmittal->received_checks + $checks->count() ]); // update transmittal

        $checks->each( function($check) use ($request, $transmittal) {
            $group = $transmittal->returned ? Group::first() : $transmittal->group;

            $check->update(['received' => 1, 'group_id' => $group->id]);

            $this->recordLog($check, 'rcv', $request->get('date'), $request->get('remarks'));
        });

        $recipients = Access::find(2)->users; // administrators
        $sender = ! $transmittal->returned ? $transmittal->user : $transmittal->returnedBy;
        $recipients->push($sender);

        Notification::send($recipients, new ChecksReceivedNotification($transmittal, $checks, $unreceivedChecks, $request->user()));

        Log::info($request->user()->name . ' received checks.');

        return ['message' => 'Checks successfully received.'];
    }

    public function claim(Request $request, Company $company)
    {
        $request->validate([ 'checks' => 'array|nullable' ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");
        // get last action
        $lastAction = History::whereIn('check_id', $checks->pluck('id'))
            ->orderBy('date', 'desc')
            ->where('active', 1)
            ->whereNotIn('action_id', [8, 11])/*edit, undo*/
            ->first();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . $lastAction->date,
            ],
            'remarks' => 'max:50',
        ]);

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
        $request->validate([ 'check' => 'required' ]);

        $check = Check::where('id', $request->get('check'))->first();
        // must be greater than zero
        abort_unless($check, 400, "No check selected!");

        $lastAction = $check->history()
            ->orderBy('date', 'desc')
            ->where('active', 1)
            ->whereNotIn('action_id', [8, 11])/*edit, undo*/
            ->first();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . $lastAction->date,
            ],
            'amount' => 'required|numeric|gt:0',
        ]);

        $this->authorize('clear', [$check, $company]);

        $check->update(['status_id' => 6, 'cleared' => $request->get('amount')]); /*cleared*/

        $this->recordLog($check, 'clr', $request->get('date'));

        Log::info($request->user()->name . ' cleared checks.');

        return ['message' => 'Checks successfully cleared.'];
    }

    public function return(Request $request, Company $company)
    {
        ini_set('memory_limit', '2048M');

        $transmittals = Transmittal::where( function($q) {
                $q->where( function($x) {
                    $x->whereColumn('received_checks', 'sent_checks')
                        ->where('returned', null);
                })->orWhere( function($x) {
                    $x->where('returned_all', 0)
                        ->where('returned', '<>', null);
                });
            })->pluck('id');

        $request->validate([
            'transmittal_id' => [ 'required', Rule::in($transmittals) ],
            'remarks' => 'max:50',
            'selectChecks' => 'required',
            'selectedChecks' => 'array',
        ]);

        $transmittal = Transmittal::findOrFail($request->get('transmittal_id'));

        $checks = $request->get('selectChecks') ?
            Check::whereIn('id', $request->get('selectedChecks'))->get():
            $transmittal->checks()->where('status_id', 2)->get(); /*transmitted*/
        // return transmittals even all are claimed
        abort_if($request->get('selectChecks') && !$checks->count(), 400, "No check selected!");
        // get last action
        $lastAction = History::whereIn('check_id', $checks->pluck('id'))
            ->orderBy('date', 'desc')
            ->where('active', 1)
            ->whereNotIn('action_id', [8, 11])/*edit, undo*/
            ->first();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . ($lastAction ? $lastAction->date : $transmittal->date),
            ]
        ]);

        $this->authorize('return', [Check::class, $checks]);

        $returned_all = $checks->count() === $transmittal->checks()->where('status_id', 2)->count();

        $transmittal->update([
            'returnedBy_id' => $request->user()->id,
            'returned' => $request->get('date'),
            'sent_checks' => $transmittal->sent_checks - $transmittal->received_checks + $checks->count(),
            'received_checks' => 0,
            'returned_all' => $returned_all,
        ]); // update transmittal

        $checks->each( function($check) use ($request) {
            $check->update([
                'status_id' => 4,
                'received' => 0,
                'branch_id' => 1
            ]); // returned

            $this->recordLog($check, 'rtn', $request->get('date'), $request->get('remarks'));
        });

        \PDF::loadView('pdf.return', compact('transmittal'))
            ->setPaper('letter', 'portrait')
            ->setWarnings(false)
            ->save( public_path() . '/pdf/transmittal/' . $transmittal->ref . '-1.pdf');

        $incharges = Group::first()->incharge;
        $incharges->push($request->user());
        $recipients = $incharges->merge(Access::find(2)->users); // administrators

        Notification::send($recipients, new ChecksReturnedNotification($transmittal, $checks));

        Log::info($request->user()->name . ' returned checks.');

        return ['message' => 'Checks successfully returned.'];
    }

    public function cancel(Request $request, Company $company)
    {
        $request->validate([ 'checks' => 'array|nullable' ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");
        // get last action
        $lastAction = History::whereIn('check_id', $checks->pluck('id'))->orderBy('date', 'desc')->first();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                'after_or_equal:' . $lastAction->date,
            ],
            'remarks' => 'required|max:50',
        ]);

        $this->authorize('cancel', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            $check->update([ 'status_id' => 5]); // cancelled

            $this->recordLog($check, 'cnl', $request->get('date'), $request->get('remarks'));
        });

        Log::info($request->user()->name . ' cancelled checks.');

        return ['message' => 'Checks cancelled.'];
    }

    public function stale(Request $request, Company $company)
    {
        $request->validate([
            'date' => 'required|date',
            'checks' => 'array|nullable',
            'remarks' => 'max:50',
        ]);

        $checks = Check::whereIn('id', $request->get('checks'))->get();
        // must be greater than zero
        abort_unless($checks->count(), 400, "No check selected!");

        $this->authorize('stale', [Check::class, $company, $checks]);

        $checks->each( function($check) use ($request) {
            if (! $check->received) {
                $transmittal = $check->transmittals()->orderBy('id', 'desc')->first(); /*get transmittal*/
                $transmittal->update([
                    'sent_checks' => $transmittal->sent_checks ? $transmittal->sent_checks - 1 : 0,
                ]);
            }

            $check->update([ 'status_id' => 7, 'received' => 1]); // staled

            $this->recordLog($check, 'stl', $request->get('date'), $request->get('remarks'));
        });

        Log::info($request->user()->name . ' staled checks.');

        return ['message' => 'Checks marked staled.'];
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
        $check->transmittal = $check->transmittals()->latest()->first();
        $check->history = $check->history()->with('action')->orderBy('id')->with('user')->get();

        return $check;
    }

    public function edit(Request $request, Company $company, Check $check)
    {
        $request->validate([ 'details' => 'required|max:50' ]);

        $this->authorize('edit', [$check, $company]);

        $check->update([ 'details' => $request->get('details') ]);

        $this->recordLog($check, 'edt', date('Y-m-d'), 'Details: ' . $request->get('details') );

        Log::info($request->user()->name . ' edited check.');

        return ['message' => 'Check successfully updated.'];
    }

    public function delete(Request $request, Company $company, Check $check)
    {
        $request->validate([ 'remarks' => 'required|max:50' ]);

        $this->authorize('delete', [$check, $company]);

        $checkbook = $check->checkbook;

        $check->delete();
        // update checkbook
        $checkbook->update([
            'posted' => $postedChecks = $checkbook->postedChecks()->count(),
            'available' => $checkbook->total - $postedChecks,
        ]);

        $this->recordLog($check, 'dlt', date('Y-m-d'), $request->get('remarks'));

        Log::info($request->user()->name . ' deleted check.');

        return ['message' => 'Check successfully deleted.'];
    }

    public function undo(Request $request, Company $company)
    {
        $request->validate([
            'check' => 'required',
            'remarks' => 'required|max:50'
        ]);
        // get check
        $check = Check::where('id', $request->get('check'))->firstOrFail();
        // authorization
        $this->authorize('undo', [$check, $company]);
        // check if already received
        $received = $check->received;
        // get history
        $history = $check->history()->orderBy('id', 'desc')->get();
        // get state to be restored
        $restoration_state = json_decode($history[1]->state, true)['received'] ? $history[1]: $history[2];
        // update check
        $check->update(json_decode($restoration_state->state, true));
        // get last action type
        $last_action = $check->history()->orderBy('id', 'desc')->where('action_id', '<>', 3)->first();
        // set last action as inactive
        $last_action->update(['active' => 0]);
        // action base on last action
        if ($last_action->action_id === 2/*transmitted*/) {
            $transmittal = $check->transmittals()->orderBy('id', 'desc')->first(); /*get transmittal*/
            // query transmittal checks except restored check
            $checks = $transmittal->checks->reject( function($c) use ($check) {
                return $c->id === $check->id;
            });
            // resync
            $transmittal->checks()->sync($checks);
            // update base on check received status
            if ($received) {
                $transmittal->update(['sent_checks' => $checks->count(), 'received_checks' => $transmittal->received_checks - 1]);
            } else {
                $transmittal->update(['sent_checks' => $checks->count()]);
            }
        } elseif ($last_action->action_id === 5/*return*/) {
            $transmittal = $check->transmittals()->orderBy('id', 'desc')->first(); /*get transmittal*/
            // update base on check received status
            if ($received) {
                $transmittal->update([
                    'sent_checks' => $transmittal->sent_checks - 1,
                    'received_checks' => $transmittal->received_checks - 1,
                    'returned_all' => 0,
                ]);
            } else {
                $transmittal->update(['sent_checks' => $transmittal->sent_checks - 1, 'returned_all' => 0,]);
            }
        } elseif ($last_action->action_id === 4/*claim*/) {
            if ($check->group_id !== 1 /*disburesement group*/) {
                if ( $transmittal = $check->transmittals()->orderBy('id', 'desc')->first() /*get transmittal*/) {
                    $transmittal->update(['returned_all' => 0]);
                }
            }
        }

        $this->recordLog($check, 'und', date('Y-m-d'), $request->get('remarks'));

        Log::info($request->user()->name . ' undo check actions.');

        return ['message' => 'Check successfully restored to previous state.'];
    }

    // record check log
    protected function recordLog(Check $check, $action, $date, $remarks = null)
    {
        History::create([
            'check_id' => $check->id,
            'action_id' => Action::where('code', $action)->first()->id,
            'user_id' => Auth::user()->id,
            'date' => $date,
            'remarks' => $remarks,
            'state' => json_encode($check->only(['group_id', 'branch_id', 'status_id', 'received', 'details', 'deleted_at']))
        ]);
    }

    protected function checkFilter($array, $index)
    {
        return array_key_exists($index, $array) && $array[$index] ?
            $array[$index]:
            null;
    }
}
