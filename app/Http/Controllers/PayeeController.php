<?php

namespace App\Http\Controllers;

use App\Payee;
use App\Module;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PayeeController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'pye')->first();
    }

    public function index(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $sort = $request->get('sortBy') ? $request->get('sortBy')[0] : 'updated_at';

        $order = $request->get('sortDesc') ?
            ($request->get('sortDesc')[0] ? 'desc' : 'asc') :
            'desc';

        return $company->payees()->with('group')
            ->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('search') . '%');
            })
            ->orderBy($sort, $order)
            ->orderBy('id', 'desc')
            ->paginate($request->get('itemsPerPage'));
    }

    public function store(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'name' => 'required|max:191',
            'code' => 'required|max:20|unique2:payees,code,company_id,' . $company->id,
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        Payee::create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'company_id' => $company->id,
            'payee_group_id' => $request->get('payee_group_id'),
        ]);

        Log::info($request->user()->name . ' created new payee.');

        return ['message' => 'Payee successfully recorded.'];
    }

    public function show(Company $company, Payee $payee)
    {
        $this->authorize('module', $this->module);

        abort_unless($payee->company_id === $company->id, 404, 'Not Found');

        $payee->group;

        return $payee;
    }

    public function update(Request $request, Company $company, Payee $payee)
    {
        $this->authorize('module', $this->module);

        abort_unless($payee->company_id === $company->id, 403, 'Unauthorized');

        $request->validate([
            'name' => 'required|max:191',
            'code' => 'required|max:20',
            'payee_group_id' => 'required|integer|exists:payee_groups,id',
        ]);

        $payee->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'payee_group_id' => $request->get('payee_group_id'),
            'active' => $request->get('active'),
        ]);

        Log::info($request->user()->name . ' updated a payee: ' . $payee->code);

        return ['message' => 'Payee successfully updated .'];
    }

    public function destroy(Company $company, Payee $payee)
    {
        $this->authorize('module', $this->module);

        abort_unless($payee->company_id === $company->id, 403, 'Unauthorized');

        abort_if($payee->checks->count(), 400, "Unable to delete: Checks belong to this Payee.");

        $payee->delete();

        Log::info( Auth::user()->name . ' deleted payee: ' . $payee->name );

        return ['message' => 'Payee successfully deleted.'];
    }
}
