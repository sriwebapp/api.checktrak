<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Report;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\MasterlistReport;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $company;
    protected $activeFilter = [];

    public function masterlist(Request $request, Company $company)
    {
        abort_unless($user = User::find(request('user')), 403, 'Unauthorized User');
        abort_unless($user->getReports()->where('code', 'chk_mstr')->count(), 403, 'Unauthorized User');
        $this->company = $company;
        $this->activeFilter['Company:'] = $company->name;

        $request->replace(json_decode($request->get('filter'), true));

        $filter = $request->all();
        $groups = $user->getGroups()->pluck('id');

        if ( $transmittal_id = $this->checkFilter($filter, 'transmittal_id') ) {
            // filter by transmittal
            if ($transmittal = $company->transmittals()->find( $transmittal_id )) {
                $this->activeFilter['Transmittal:'] = $transmittal->ref;
            } else {
                abort(400, "Invalid Transmittal!");
            }

            $checks = $transmittal->checks();
        } else {
            $checks = $company->checks();
        }

        $filteredChecks = $checks
            ->where( function($q) use ($filter) {
                // filter by account_id
                if ( $account_id = $this->checkFilter($filter, 'account_id') ) {
                    if ($account = $this->company->accounts()->find($account_id)) {
                        $this->activeFilter['Account:'] = $account->code;
                    } else {
                        abort(400, "Invalid Bank Account!");
                    }
                }

                    $q->where('account_id', $account_id );
                // filter by payee_id
                if ( $payee_id = $this->checkFilter($filter, 'payee_id') ) {
                    if ($payee = $this->company->payees()->find($payee_id)) {
                        $this->activeFilter['Payee:'] = $payee->name;
                    } else {
                        abort(400, "Invalid Bank Account!");
                    }

                    $q->where('payee_id', $payee_id );
                }
                // filter by check number
                if ( $number = $this->checkFilter($filter, 'number') ) {
                    $from = $number['from'] < $number['to'] ? $number['from'] : $number['to'];
                    $to = $number['from'] > $number['to'] ? $number['from'] : $number['to'];

                    $this->activeFilter['Number:'] = $from . ' - ' . $to;

                    $q->whereBetween('number', [$from, $to]);
                }
                // filter by posting date
                if ( $date = $this->checkFilter($filter, 'date') ) {
                    $from = $date['from'] < $date['to'] ? $date['from'] : $date['to'];
                    $to = $date['from'] > $date['to'] ? $date['from'] : $date['to'];

                    $this->activeFilter['Date:'] = $from . ' - ' . $to;

                    $q->whereBetween('date', [$from, $to]);
                }
                // filter by status
                if ( $status = $this->checkFilter($filter, 'status') ) {
                    $q->whereIn('status_id', $status['statuses'])
                        ->where('received', $status['received']);
                }
            })
            ->whereIn('group_id', $groups)
            ->get();

        abort_unless($checks->count(), 400, "No checks to be exported!");

        $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new MasterlistReport($filteredChecks, $timestamp, collect($this->activeFilter)), 'masterlist_report_' . $timestamp . '.xlsx');
    }

    protected function checkFilter($array, $index)
    {
        return array_key_exists($index, $array) && $array[$index] ?
            $array[$index]:
            null;
    }
}
