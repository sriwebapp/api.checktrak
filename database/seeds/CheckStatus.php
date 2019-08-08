<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            ['name' => 'Created'],
            ['name' => 'Transmitted'],
            ['name' => 'Claimed'],
            ['name' => 'Returned'],
            ['name' => 'Cancelled'],
            ['name' => 'Cleared'],
            ['name' => 'Staled'],
        ]);
    }
}
