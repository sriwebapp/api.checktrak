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
            ['name' => 'Created', 'color' => 'indigo'],
            ['name' => 'Transmitted', 'color' => 'blue'],
            ['name' => 'Claimed', 'color' => 'purple'],
            ['name' => 'Returned', 'color' => 'teal'],
            ['name' => 'Cancelled', 'color' => 'red'],
            ['name' => 'Cleared', 'color' => 'teal'],
            ['name' => 'Staled', 'color' => 'orange'],
        ]);
    }
}
