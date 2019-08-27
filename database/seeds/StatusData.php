<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
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
