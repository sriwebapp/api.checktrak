<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayeeGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payee_groups')->insert([
            ['name' => 'Admin'],
            ['name' => 'Deployed'],
            ['name' => 'Supplier'],
            ['name' => 'Professional'],
            ['name' => 'Rental'],
        ]);
    }
}
