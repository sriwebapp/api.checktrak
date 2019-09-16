<?php

use App\Company;
use Illuminate\Database\Seeder;

class CompanyData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::insert([
            ['code' => 'SRI', 'name' => 'Service Resources Inc.', 'address' => 'First Capitol Place First Cor. Philam Sts. Kapitolyo, Pasig City', 'tel' => '6370635'],
            ['code' => 'CLSC', 'name' => 'Central Labor Service Cooperative', 'address' => 'First Capitol Place First Cor. Philam Sts. Kapitolyo, Pasig City', 'tel' => '6370635'],
            ['code' => 'JTCI', 'name' => 'Job Skills Training Center Inc', 'address' => 'First Capitol Place First Cor. Philam Sts. Kapitolyo, Pasig City', 'tel' => '6370635'],
            ['code' => 'MNG', 'name' => 'MNGoodHealth Inc.', 'address' => 'MNGoodHealth Inc.', 'tel' => '6370635'],
            ['code' => 'PSSI', 'name' => 'People Link Staffing Solutions Inc', 'address' => 'First Capitol Place First Cor. Philam Sts. Kapitolyo, Pasig City', 'tel' => '6370635'],
            ['code' => 'SOFI', 'name' => 'Serbisyo Outsourcing Facilities Inc', 'address' => 'First Capitol Place  First cor. Philam Sts.  Kapitolyo, Pasig', 'tel' => '6370635'],
        ]);
    }
}
