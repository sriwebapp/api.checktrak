<?php

namespace App\Http\Controllers;

use Excel;
use App\Check;
use Carbon\Carbon;
use App\Transmittal;
use App\Exports\CheckExport;
use Illuminate\Http\Request;
use App\Exports\TransmittalExport;

class ExportController extends Controller
{
    public function check(Request $request)
    {
        $ids = json_decode($request->get('checks'));

        $checks = Check::whereIn('id', $ids)->get();

        abort_unless($checks->count(), 400, "No check selected!");

        $timestamp = Carbon::now()->format('Y_m_d_His');

        return Excel::download(new CheckExport($checks, $timestamp), 'check_masterlist_' . $timestamp . '.xlsx');
    }

    public function transmittal(Request $request)
    {
        $transmittal = Transmittal::findOrFAil($request->get('id'));

        $title = str_replace('-', '_', $transmittal->ref) . '_' . Carbon::now()->format('Y_m_d_His');

        return Excel::download(new TransmittalExport($transmittal), $title . '.xlsx');
    }
}
