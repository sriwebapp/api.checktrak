<?php

namespace App\Http\Controllers;

use App\Module;
use App\Company;
use App\CheckBook;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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

        $data = $company->checkbooks()
            ->select('id')
            ->orderBy($sort, $order)
            ->paginate($request->get('itemsPerPage'));

        $checkbooks = CheckBook::select(
                DB::raw(
                    'check_books.id,
                    check_books.account_id,
                    accounts.code AS account,
                    check_books.start_series,
                    check_books.end_series,
                    check_books.end_series - (check_books.start_series - 1) AS total_checks,
                    count(checks.id) AS posted_checks,
                    check_books.end_series - (check_books.start_series - 1) - count(checks.id) AS available_checks'
                )
            )
            ->leftJoin('accounts', 'accounts.id', '=', 'check_books.account_id')
            ->leftJoin('checks', function($join) {
                $join->on('checks.account_id', '=', 'accounts.id');
                $join->on('checks.number', '>=', 'check_books.start_series');
                $join->on('checks.number', '<=', 'check_books.end_series');
                $join->on(DB::raw('length(checks.number)'), DB::raw('length(check_books.start_series)'));
            })
            ->whereIn('check_books.id', $data->getCollection()->pluck('id'))
            ->groupBy('check_books.id')
            ->orderBy($sort, $order)
            ->get();

        return $data->setCollection($checkbooks);
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
        $checkBook->availableChecks = $checkBook->totalChecks - $checkBook->postedChecks;
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

        abort_if($checkBook->postedChecks(), 400, "Unable to delete: Checkbook has posted checks.");

        $checkBook->delete();

        Log::info( Auth::user()->name . ' deleted checkbook');

        return ['message' => 'Check Book successfully deleted.'];
    }
}
