<?php

use App\Check;
use App\Company;
use App\History;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestCheckData extends Seeder
{
    public function run()
    {
        Company::get()->each( function($company) {
            $accounts = $company->accounts()->pluck('id');
            $payees = $company->payees->pluck('id');

            for ($i=0; $i < 1000; $i++) {
                Check::insert([
                    'number' => rand(10000000 ,9999999999),
                    'status_id' => 1, /*created*/
                    'company_id' => $company->id,
                    'account_id' => $accounts->random(),
                    'payee_id' => $accounts->random(),
                    'amount' => rand(1000 ,100000),
                    'details' => 'Test Check Data Test Check Data Test Check Data Test Check Data Test Check Data',
                    'date' => date("Y/m/d"),
                ]);
            }
        });

        // $company = Company::first();
        // $accounts = $company->accounts()->pluck('id');
        // $payees = $company->payees->pluck('id');

        // for ($i=0; $i < 5000; $i++) {
        //     Check::insert([
        //         'number' => rand(10000000 ,9999999999),
        //         'status_id' => 1, /*created*/
        //         'company_id' => $company->id,
        //         'account_id' => $accounts->random(),
        //         'payee_id' => $accounts->random(),
        //         'amount' => rand(1000 ,100000),
        //         'details' => 'Test Check Data Test Check Data Test Check Data Test Check Data Test Check Data',
        //         'date' => date("Y/m/d"),
        //     ]);
        // }

        Check::get()->each( function($check) {
            History::create([
                'check_id' => $check->id,
                'action_id' => 1,
                'user_id' => 1,
                'date' => date('Y-m-d'),
                'remarks' => 'test create'
            ]);
        });
    }
}
