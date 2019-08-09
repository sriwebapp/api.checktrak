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
        $this->call(TestUserData::class);
        $this->call(TestBranchData::class);
    }
}
