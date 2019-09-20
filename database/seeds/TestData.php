<?php

use Illuminate\Database\Seeder;

class TestData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(TestUserData::class);
        // $this->call(TestBranchData::class);
        // $this->call(TestAccessBranchData::class);
        // $this->call(TestUserBranchData::class);
        // $this->call(TestAccessModuleData::class);
        // $this->call(TestUserModuleData::class);
        // $this->call(TestAccessActionData::class);
        // $this->call(TestUserActionData::class);
        // $this->call(TestCompanyData::class);
        $this->call(TestAccountData::class);
        $this->call(TestPayeeData::class);
        $this->call(TestCheckData::class);
        $this->call(TestTransmittalData::class);
    }
}
