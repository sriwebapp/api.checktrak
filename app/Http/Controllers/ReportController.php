<?php

namespace App\Http\Controllers;

use Excel;
use Carbon\Carbon;
use App\Exports\CheckExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function masterlist(Request $request)
    {
        $request->replace(json_decode($request->get('filter'), true));

        abort_unless($account = \App\Account::find(request('account_id')), 400, "No Account Selected!");

        $checks = $account->checks;

        abort_unless($checks->count(), 400, "No checks to be exported!");

        return $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new CheckExport($checks, $timestamp), 'check_masterlist_' . $timestamp . '.xlsx');
    }
}
