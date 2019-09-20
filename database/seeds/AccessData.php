<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accesses')->insert([
            ['name' => 'System Admin', 'action' => '2', 'branch' => '2', 'module' => '2'],
            ['name' => 'Administrator', 'action' => '2', 'branch' => '2', 'module' => '2'],
            ['name' => 'Disbursement Group', 'action' => '1', 'branch' => '2', 'module' => '1'],
            ['name' => 'Other Group', 'action' => '1', 'branch' => '0', 'module' => '1'],
            ['name' => 'Custom Users', 'action' => '0', 'branch' => '0', 'module' => '0'],
        ]);
    }
}
