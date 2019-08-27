<?php

use App\Check;
use App\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestCheckData extends Seeder
{
    public function run()
    {
        Company::get()->each( function($company) {
            $accounts = $company->accounts()->pluck('id');
            $payees = $company->payees->pluck('id');

            for ($i=0; $i < 20; $i++) {
                Check::insert([
                    'status_id' => 1, /*created*/
                    'company_id' => $company->id,
                    'account_id' => $accounts->random(),
                    'payee_id' => $accounts->random(),
                    'amount' => rand(1000 ,100000),
                    'details' => 'Test Check Data',
                    'date' => date("Y/m/d"),
                ]);
            }
        });
    }
}
