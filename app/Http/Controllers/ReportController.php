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
    protected $headers = [];

    public function masterlist(Request $request, Company $company)
    {
        $this->company = $company;

        $filter = $this->checkRequest();

        $this->addHeader('Company', $company->name);

        if ( $transmittal_id = $this->checkFilter($filter, 'transmittal_id') ) {
            // filter by transmittal
            abort_unless($transmittal = $company->transmittals()->find( $transmittal_id ), 403, 'Invalid Transmittal.');

            $this->addHeader('Transmittal', $transmittal->ref);

            $checks = $transmittal->checks();
        } else {
            $checks = $company->checks();
        }

        $checks = $checks
            ->where( function($q) use ($filter) {
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

        $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new MasterlistReport($checks, $timestamp, collect($this->headers)), 'masterlist_report_' . $timestamp . '.xlsx');
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
