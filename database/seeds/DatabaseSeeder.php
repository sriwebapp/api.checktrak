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
        $this->call(Group::class);
        $this->call(StatusData::class);
        $this->call(Action::class);
        $this->call(PayeeGroup::class);
        $this->call(Module::class);
        $this->call(InitialData::class);
        $this->call(TestGroupBranchData::class);
        $this->call(TestGroupModuleData::class);
        $this->call(TestGroupActionData::class);
    }
}
