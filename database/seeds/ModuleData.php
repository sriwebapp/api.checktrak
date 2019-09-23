<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            ['name' => 'User', 'code' => 'usr'],
            ['name' => 'Access', 'code' => 'acs'],
            ['name' => 'Company', 'code' => 'cmp'],
            ['name' => 'Branch', 'code' => 'bra'],
            ['name' => 'Group', 'code' => 'grp'],
            ['name' => 'Account', 'code' => 'acc'],
            ['name' => 'Payee', 'code' => 'pye'],
        ]);
    }
}
