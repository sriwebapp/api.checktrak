<?php

namespace App\Http\Controllers;

use Excel;
use App\Check;
use Carbon\Carbon;
use App\Exports\CheckExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function check(Request $request)
    {
        $ids = json_decode($request->get('checks'));

        $checks = Check::whereIn('id', $ids)->get();

        abort_unless($checks->count(), 400, "No check selected!");

        $title = 'check_masterlist_' . Carbon::now()->format('Y-m-d');

        return Excel::download(new CheckExport($checks, $title), $title . '.xlsx');
    }
}
