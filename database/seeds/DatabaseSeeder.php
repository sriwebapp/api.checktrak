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
        $this->call(UserGroup::class);
        $this->call(CheckStatus::class);
        $this->call(Actions::class);
        $this->call(PayeeGroup::class);
        $this->call(InitialData::class);
    }
}
