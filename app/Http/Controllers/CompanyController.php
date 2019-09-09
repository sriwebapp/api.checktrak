<?php

namespace App\Http\Controllers;

use App\Module;
use App\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'cmp')->first();
    }

    public function index()
    {
        $this->authorize('module', $this->module);

        return Company::get();
    }

    public function store(Request $request)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'code' => 'required|string|min:3|max:10|unique:companies',
            'name' => 'required|string|min:5|max:191|unique:companies',
            'address' => 'required|string|min:10|max:191',
            'tel' => 'required|min:7|max:50',
            'tin' => /*required*/ 'max:50',
            'sss' => /*required*/ 'max:50',
            'hdmf' => /*required*/ 'max:50',
            'phic' => /*required*/ 'max:50',
        ]);

        Company::create([
            'code' => strtoupper($request->get('code')),
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'tin' => $request->get('tin'),
            'sss' => $request->get('sss'),
            'hdmf' => $request->get('hdmf'),
            'phic' => $request->get('phic'),
        ]);

        return ['message' => 'Company successfully recorded.'];
    }

    public function show(Company $company)
    {
        $this->authorize('module', $this->module);

        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('module', $this->module);

        $request->validate([
            'code' => 'required|string|min:3|max:10|unique:companies,code,' . $company->id,
            'name' => 'required|string|min:5|max:191|unique:companies,name,' . $company->id,
            'address' => 'required|string|min:10|max:191',
            'tel' => 'required|min:7|max:50',
            'tin' => /*required*/ 'max:50',
            'sss' => /*required*/ 'max:50',
            'hdmf' => /*required*/ 'max:50',
            'phic' => /*required*/ 'max:50',
        ]);

        $company->update([
            'code' => strtoupper($request->get('code')),
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'tel' => $request->get('tel'),
            'tin' => $request->get('tin'),
            'sss' => $request->get('sss'),
            'hdmf' => $request->get('hdmf'),
            'phic' => $request->get('phic'),
        ]);

        return [
            'company' => $company,
            'message' => 'Company successfully updated.'
        ];
    }

    public function destroy(Company $company)
    {
        abort(403);
    }
}
