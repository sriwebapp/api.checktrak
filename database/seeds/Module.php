<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Module extends Seeder
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
            ['name' => 'Company', 'code' => 'cmp'],
            ['name' => 'Branch', 'code' => 'bra'],
            ['name' => 'Account', 'code' => 'acc'],
            ['name' => 'Payee', 'code' => 'pye'],
            ['name' => 'Access', 'code' => 'acs'],
        ]);
    }
}
