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
            ['name' => 'System Admin', 'action' => '2', 'group' => '2', 'module' => '2'],
            ['name' => 'Administrator', 'action' => '2', 'group' => '2', 'module' => '2'],
            ['name' => 'Disbursement Group', 'action' => '1', 'group' => '2', 'module' => '1'],
            ['name' => 'Other Groups', 'action' => '1', 'group' => '0', 'module' => '1'],
            ['name' => 'Custom Users', 'action' => '0', 'group' => '0', 'module' => '0'],
        ]);
    }
}
