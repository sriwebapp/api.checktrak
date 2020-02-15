<?php

use App\Company;
use App\CheckBook;
use Illuminate\Database\Seeder;

class TestCheckBook extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '2048M');

        $company = Company::first();
        $account = $company->accounts()->first();

        for ($i=0; $i < 50000; $i++) {
                CheckBook::insert([
                    'company_id' => $company->id,
                    'account_id' => $account->id,
                    'start_series' => 10000001 + ($i * 100),
                    'end_series' => 10000100 + ($i * 100)
                ]);
            }
    }
}
