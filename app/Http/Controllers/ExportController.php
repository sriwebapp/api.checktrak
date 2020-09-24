<?php

namespace App\Http\Controllers;

use Excel;
use App\Check;
use App\Company;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Http\Request;
use App\Exports\MasterlistReport;
use App\Exports\TransmittalExport;

class ExportController extends Controller
{
    public function check(Request $request, Company $company)
    {
        abort_unless($ids = json_decode($request->get('checks')), 403, 'Invalid Data.');

        $checks = Check::whereIn('id', $ids)->get();

        $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new MasterlistReport($checks, $timestamp, collect(['Company:' => $company->name])), 'check_masterlist_' . $timestamp . '.xlsx');
    }

    public function transmittal(Request $request)
    {
        $transmittal = Transmittal::findOrFAil($request->get('id'));

        $title = str_replace('-', '_', $transmittal->ref) . '_' . Carbon::now()->format('Y_m_d_His');

        return Excel::download(new TransmittalExport($transmittal), $title . '.xlsx');
    }
}
