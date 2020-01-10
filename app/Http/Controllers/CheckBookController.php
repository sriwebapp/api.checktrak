<?php

namespace App\Http\Controllers;

use App\Module;
use App\Company;
use App\CheckBook;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckBookController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'cbk')->first();;
    }

    public function index(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $sort = $request->get('sortBy') ? $request->get('sortBy')[0] : 'id';

        $order = $request->get('sortDesc') ?
            ($request->get('sortDesc')[0] ? 'desc' : 'asc') :
            'desc';

        $checkBooks = $company->checkBooks()
            ->with('account')
            ->orderBy($sort, $order)
            ->orderBy('id', 'desc')
            ->paginate($request->get('itemsPerPage'));

        $checkBooks->transform( function($checkBook) {
            $checkBook->totalChecks = $checkBook->totalChecks();
            $checkBook->postedChecks = $checkBook->postedChecks();
            $checkBook->availableChecks = $checkBook->totalChecks() - $checkBook->postedChecks();

            return $checkBook;
        });

        return $checkBooks;
    }

    public function store(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'account_id' => ['required', Rule::in($company->accounts()->pluck('id'))],
            'start_series' => [
                'required',
                'min:6',
                'max:10',
                'regex:/^[\d]*$/i',
                'mod:50,1',
                'unique2NotDeleted:check_books,start_series,account_id,' . $request->get('account_id')
            ],
            'end_series' => [
                'required',
                'regex:/^[\d]*$/i',
                'digits:' . strlen($request->get('start_series')),
                Rule::in([intval($request->get('start_series')) + 49, intval($request->get('start_series')) + 99]),
            ],
        ]);

        CheckBook::create([
            'company_id' => $company->id,
            'account_id' => $request->get('account_id'),
            'start_series' => $request->get('start_series'),
            'end_series' => $request->get('end_series'),
        ]);

        Log::info($request->user()->name . ' created new check book.');

        return ['message' => 'Check Book successfully recorded.'];
    }

    public function show(Company $company, CheckBook $checkBook)
    {
        $this->authorize('module', $this->module);

        $checkBook->postedChecks = $checkBook->postedChecks();
        $checkBook->totalChecks = $checkBook->totalChecks();
        $checkBook->availableChecks = $checkBook->totalChecks() - $checkBook->postedChecks();
        $checkBook->checks = $checkBook->checks();
        $checkBook->account;

        return $checkBook;
    }

    public function update(Request $request, Company $company, CheckBook $checkBook)
    {
        abort(403);
    }

    public function destroy(Company $company, CheckBook $checkBook)
    {
        $this->authorize('module', $this->module);

        $checkBook->delete();

        Log::info( Auth::user()->name . ' deleted checkbook');

        return ['message' => 'Check Book successfully deleted.'];
    }
}
