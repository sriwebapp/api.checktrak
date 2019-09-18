<?php

namespace App\Http\Controllers;

use App\Company;
use App\Transmittal;
use Illuminate\Http\Request;

class TransmittalController extends Controller
{
    public function index(Company $company)
    {
        return Transmittal::where('ref' , 'like', $company->code . '%')
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
