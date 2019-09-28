<?php

namespace App\Http\Controllers;

use App\Check;
use App\Import;
use App\Company;
use App\Imports\CheckImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function check(Request $request, Company $company)
    {
        $this->authorize('import', Check::class);

        ini_set('memory_limit','2048M');

        $request->validate(['checks_file' => 'required|max:10000|mimes:csv,txt']);

        $checks = \Excel::toCollection(new CheckImport(), $request->file('checks_file'))->first();
        // check if columns are complete
        $completeColumns = $checks->first()->has([
            'bank_no', 'account', 'posting_date', 'cheque_no', 'bp_code', 'bp_name', 'journal_remarks', 'payment_amt'
        ]);

        abort_unless($completeColumns, 400, 'Importing failed: Some columns are missing.');

        \Excel::import($import = new CheckImport($company), $request->file('checks_file')); //import

        Log::info($request->user()->name . ' imported checks.');

        return $import->response();
    }

    public function index(Company $company)
    {
        $this->authorize('import', Check::class);

        return $company->imports()->with('user')->get();
    }

    public function store()
    {
        abort(403);
    }

    public function show(Company $company, Import $import)
    {
        $this->authorize('import', Check::class);

        if ($import->subject === 'Checks') {
            $import->items = $import->tempChecks()->with('reason')->get();
        }

        return $import;
    }

    public function update()
    {
        abort(403);
    }

    public function destroy()
    {
        abort(403);
    }
}
