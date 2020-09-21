<?php

namespace App\Http\Controllers;

use App\Company;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransmittalController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $sort = $request->get('sortBy') ? $request->get('sortBy')[0] : 'id';

        $order = $request->get('sortDesc') ?
            ($request->get('sortDesc')[0] ? 'desc' : 'asc') :
            'desc';

        $groups = Auth::user()->getGroups()->pluck('id');

        $transmittals = $company->transmittals()
            ->whereIn('group_id', $groups)
            ->where( function ($query) use ($request) {
                if ((boolean) $request->get('search'))
                    $query->where('ref', 'like', $request->get('search') . '%');
            })
            ->with('branch')
            ->with('group')
            ->with('inchargeUser')
            ->orderBy($sort, $order)
            ->paginate($request->get('itemsPerPage'));

        $transmittals->transform( function($transmittal) {
            $transmittal->checks = $transmittal->checks()->with('history')->get();

            return $transmittal;
        });

        return $transmittals;
    }

    public function show(Company $company, Transmittal $transmittal)
    {
        abort_unless($transmittal->company_id === $company->id, 403, "Not Allowed.");

        $transmittal->user;
        $transmittal->inchargeUser;
        $transmittal->checks = $transmittal->checks()
            ->with('payee')
            ->with('history')
            ->with('status')
            ->get();

        return $transmittal;
    }
}
