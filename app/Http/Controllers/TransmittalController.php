<?php

namespace App\Http\Controllers;

use App\Company;
use App\Transmittal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransmittalController extends Controller
{
    public function index(Company $company)
    {
        $groups = Auth::user()->getGroups()->pluck('id');

        $transmittals = $company->transmittals()
            ->whereIn('group_id', $groups)
            ->with('branch')
            ->orderBy('id', 'desc')
            ->get();

        return $transmittals->map( function($transmittal) {
            $transmittal->checks = $transmittal->checks()->with('history')->get();

            return $transmittal;
        });
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
