<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Group;
use App\Branch;
use App\Report;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\MasterlistReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $company;
    protected $limit = 5000;
    protected $headers = [];

    public function countMasterlist(Request $request, Company $company)
    {
        $check = $this->queryMasterlist($company, $request->get('filter'));

        abort_unless($check->count(), 403, 'No checks available for this filter.');

        return ['checks' => $check->count(), 'limit' => $this->limit ];
    }

    public function generateMasterlist(Request $request, Company $company)
    {
        $filter = $this->checkRequest();

        $checks = $this->queryMasterlist($company, $filter)
            ->splice($this->limit * ($request->get('batch') - 1), $this->limit);

        $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new MasterlistReport($checks, $timestamp, collect($this->headers)), 'masterlist_report_' . $timestamp . '.xlsx');
    }

    protected function queryMasterlist(Company $company, $filter)
    {
        $this->company = $company;

        $this->addHeader('Company', $company->name);

        return $company->checks()
                ->where( function($q) use ($filter) {
                // filter by transmittal branch
                if ( $branch_id = $this->checkFilter($filter, 'branch_id') ) {
                    abort_unless($branch = Branch::find( $branch_id ), 403, 'Invalid Branch.');

                    $this->addHeader('Branch', $branch->name);

                    $check_ids = $branch->transmittals()
                        ->where('company_id', $this->company->id)
                        ->with('checks')->get()
                        ->pluck('checks')->unique()
                        ->flatten()->pluck('id');

                        // dd($check_ids);

                    $q->whereIn('id', $check_ids);
                }
                // filter by transmittal group
                if ( $group_id = $this->checkFilter($filter, 'group_id') ) {
                    abort_unless($group = Group::find( $group_id ), 403, 'Invalid Group.');

                    $this->addHeader('Branch', $group->name);

                    $check_ids = $group->transmittals()
                        ->where('company_id', $this->company->id)
                        ->with('checks')->get()
                        ->pluck('checks')->unique()
                        ->flatten()->pluck('id');

                    $q->whereIn('id', $check_ids);
                }
                // filter by transmittal incharge
                if ( $incharge_id = $this->checkFilter($filter, 'incharge_id') ) {
                    abort_unless($incharge = User::find( $incharge_id ), 403, 'Invalid incharge.');

                    $this->addHeader('Incharge', $incharge->name);

                    $check_ids = $incharge->inchargeTransmittals()
                        ->where('company_id', $this->company->id)
                        ->with('checks')->get()
                        ->pluck('checks')->unique()
                        ->flatten()->pluck('id');

                    $q->whereIn('id', $check_ids);
                }
                // filter by transmittal
                if ( $transmittal_id = $this->checkFilter($filter, 'transmittal_id') ) {
                    abort_unless($transmittal = $this->company->transmittals()->find( $transmittal_id ), 403, 'Invalid Transmittal.');

                    $this->addHeader('Transmittal', $transmittal->ref);

                    $check_ids = $transmittal->checks()->pluck('checks.id');

                    $q->whereIn('id', $check_ids);
                }
                // filter by account_id
                if ( $account_id = $this->checkFilter($filter, 'account_id') ) {
                    abort_unless($account = $this->company->accounts()->find($account_id), 403, 'Invalid Bank Account.');

                    $this->addHeader('Account', $account->code);

                    $q->where('account_id', $account_id );
                }
                // filter by payee_id
                if ( $payee_id = $this->checkFilter($filter, 'payee_id') ) {
                    abort_unless($payee = $this->company->payees()->find($payee_id), 403, 'Invalid Payee.');

                    $this->addHeader('Payee', $payee->name);

                    $q->where('payee_id', $payee_id );
                }
                // filter by check number
                if ( $number = $this->checkFilter($filter, 'number') ) {
                    $from = $number['from'] < $number['to'] ? $number['from'] : $number['to'];
                    $to = $number['from'] > $number['to'] ? $number['from'] : $number['to'];

                    $this->addHeader('Number', $from . ' - ' . $to);

                    $q->whereBetween('number', [$from, $to]);
                }
                // filter by posting date
                if ( $date = $this->checkFilter($filter, 'date') ) {
                    $from = $date['from'] < $date['to'] ? $date['from'] : $date['to'];
                    $to = $date['from'] > $date['to'] ? $date['from'] : $date['to'];

                    $this->addHeader('Date', $from . ' - ' . $to);

                    $q->whereBetween('date', [$from, $to]);
                }
                // filter by status
                if ( $status = $this->checkFilter($filter, 'status') ) {
                    $q->whereIn('status_id', $status['statuses']);
                }
            })
            ->get();
    }

    protected function checkFilter($array, $index)
    {
        return array_key_exists($index, $array) && $array[$index] ?
            $array[$index]:
            null;
    }

    protected function checkRequest()
    {
        abort_unless($user = User::find(request('user')), 403, 'Sorry! You are unauthorized to do this action.');
        abort_unless($user->getReports()->where('code', 'chk_mstr')->count(), 403, 'Sorry! You are unauthorized to do this action.');
        abort_unless($filter = json_decode(request('filter'), true), 403, 'Invalid Data.');

        return $filter;
    }

    protected function addHeader($label, $value)
    {
        $this->headers[$label . ':'] = $value;
    }
}
