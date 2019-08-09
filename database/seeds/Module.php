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
            ['name' => 'User'],
            ['name' => 'Company'],
            ['name' => 'Branch'],
            ['name' => 'Account'],
            ['name' => 'Payee'],
            ['name' => 'Access'],
        ]);
    }
}
