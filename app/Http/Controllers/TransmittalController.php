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
        $branches = Auth::user()->getBranches()->pluck('id');

        return Transmittal::where('ref' , 'like', $company->code . '%')
            ->whereIn('branch_id', $branches)
            ->with('branch')
            // ->with('user')
            // ->with('incharge')
            ->get();
    }

    public function show(Company $company, Transmittal $transmittal)
    {
        return $transmittal->checks;
    }
}
