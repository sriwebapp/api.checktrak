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
        // $this->call(TestGroupBranchData::class);
        // $this->call(TestUserBranchData::class);
        // $this->call(TestGroupModuleData::class);
        // $this->call(TestUserModuleData::class);
        // $this->call(TestGroupActionData::class);
        // $this->call(TestUserActionData::class);
        // $this->call(TestCompanyData::class);
        $this->call(TestAccountData::class);
        $this->call(TestPayeeData::class);
        $this->call(TestCheckData::class);
    }
}
