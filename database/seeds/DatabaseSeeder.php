<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AccessData::class);
        $this->call(StatusData::class);
        $this->call(ActionData::class);
        $this->call(PayeeGroup::class);
        $this->call(ModuleData::class);
        $this->call(BranchData::class);
        $this->call(GroupData::class);
        $this->call(UserData::class);
        $this->call(CompanyData::class);
        // $this->call(TestAccessBranchData::class);
        $this->call(TestAccessModuleData::class);
        $this->call(TestAccessActionData::class);
    }
}
