<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Group extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            ['name' => 'System Admin', 'action' => '2', 'branch' => '2', 'module' => '2'],
            ['name' => 'Administrator', 'action' => '1', 'branch' => '2', 'module' => '1'],
            ['name' => 'Head Office', 'action' => '1', 'branch' => '1', 'module' => '1'],
            ['name' => 'Branch Office', 'action' => '1', 'branch' => '0', 'module' => '1'],
            ['name' => 'Custom Access', 'action' => '0', 'branch' => '0', 'module' => '0'],
        ]);
    }
}
