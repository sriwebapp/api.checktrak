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
        $this->call(Status::class);
        $this->call(Action::class);
        $this->call(PayeeGroup::class);
        $this->call(Module::class);
        $this->call(InitialData::class);
    }
}
