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
            // ->with('user')
            // ->with('incharge')
            ->get();
    }

    public function show(Company $company, Transmittal $transmittal)
    {
        return $transmittal->checks;
    }
}
