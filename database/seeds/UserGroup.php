<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_groups')->insert([
            ['name' => 'System Admin'],
            ['name' => 'Administrator'],
            ['name' => 'Head Office'],
            ['name' => 'Branch Office'],
            ['name' => 'Custom Access'],
        ]);
    }
}
