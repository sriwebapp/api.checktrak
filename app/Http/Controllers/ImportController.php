<?php

namespace App\Http\Controllers;

use App\Check;
use App\Import;
use App\Module;
use App\Account;
use App\Company;
use App\Imports\CheckImport;
use App\Imports\PayeeImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\ClearCheckImport;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function check(Request $request, Company $company)
    {
        $this->authorize('import', Check::class);

        ini_set('memory_limit', -1);

        Log::info($request->user()->name . ' started importing checks.');

        $request->validate(['checks_file' => 'required|max:10000|mimes:csv,txt']);

        $checks = \Excel::toCollection(new CheckImport(), $request->file('checks_file'))->first();
        // check if columns are complete
        $completeColumns = $checks->first()->has([
            'bank_no', 'account', 'posting_date', 'cheque_no', 'bp_code', 'bp_name', 'journal_remarks', 'payment_amt'
        ]);

        abort_unless($completeColumns, 400, 'Importing failed: Some columns are missing.');
        abort_if($checks->count() > 1000, 400, 'Importing failed: Importing limit of 1000 exceeded.');

        \Excel::import($import = new CheckImport($company), $request->file('checks_file')); //import

        Log::info($request->user()->name . ' imported checks.');

        return $import->response();
    }

    public function clearCheck(Request $request, Company $company)
    {
        $this->authorize('import', Check::class);

        ini_set('memory_limit', -1);

        Log::info($request->user()->name . ' started importing cleared checks.');

        $request->validate([
            'clear_checks_file' => 'required|max:10000|mimes:csv,txt',
            'account_id' => ['required', Rule::in($company->accounts->pluck('id'))]
        ]);

        $account = Account::find($request->get('account_id'));

        $clearChecks = \Excel::toCollection(new ClearCheckImport(), $request->file('clear_checks_file'))->first();
        // check if columns are complete
        $completeColumns = $clearChecks->first()->has([
            'check_number', 'amount_cleared', 'date_cleared'
        ]);

        abort_unless($completeColumns, 400, 'Importing failed: Some columns are missing.');
        abort_if($clearChecks->count() > 1000, 400, 'Importing failed: Importing limit of 1000 exceeded.');

        \Excel::import($import = new ClearCheckImport($account), $request->file('clear_checks_file')); //import

        Log::info($request->user()->name . ' imported cleared checks.');

        return $import->response();
    }

    public function payee(Request $request, Company $company)
    {
        $this->authorize('module', Module::where('code', 'pye')->first());

        ini_set('memory_limit', -1);

        Log::info($request->user()->name . ' started importing payees.');

        $request->validate(['payees_file' => 'required|max:10000|mimes:csv,txt']);

        $payees = \Excel::toCollection(new PayeeImport(), $request->file('payees_file'))->first();
        // check if columns are complete
        $completeColumns = $payees->first()->has([
            'bp_code', 'bp_name', 'group_code'
        ]);

        abort_unless($completeColumns, 400, 'Importing failed: Some columns are missing.');
        abort_if($payees->count() > 10000, 400, 'Importing failed: Importing limit of 10000 exceeded.');

        \Excel::import($import = new PayeeImport($company), $request->file('payees_file')); //import

        Log::info($request->user()->name . ' imported payees.');

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
