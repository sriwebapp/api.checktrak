<?php

namespace App\Http\Controllers;

use App\Check;
use App\Company;
use App\Imports\CheckImport;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function check(Request $request, Company $company)
    {
        $this->authorize('create', Check::class);

        ini_set('memory_limit','2048M');

        $request->validate(['checks_file' => 'required|max:10000|mimes:csv,txt']);

        $checks = \Excel::toCollection(new CheckImport(), $request->file('checks_file'))->first();
        // check if columns are complete
        $completeColumns = $checks->first()->has([
            'bank_no', 'account', 'posting_date', 'cheque_no', 'bp_code', 'bp_name', 'journal_remarks', 'payment_amt'
        ]);

        abort_unless($completeColumns, 400, 'Importing failed: Some columns are missing.');

        \Excel::import($import = new CheckImport($company), $request->file('checks_file')); //import

        return $import->response();
    }
}
