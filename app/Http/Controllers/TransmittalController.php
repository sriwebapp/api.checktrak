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

        return $company->transmittals()
            ->whereIn('group_id', $groups)
            ->with('branch')
            ->with('group')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function show(Company $company, Transmittal $transmittal)
    {
        abort_unless($transmittal->company_id === $company->id, 403, "Not Allowed.");

        $transmittal->company;
        $transmittal->checks = $transmittal->checks()->with('payee')->with('history')->get();
        $transmittal->user;
        $transmittal->inchargeUser;

        return $transmittal;
    }
}
