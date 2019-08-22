<?php

use App\Status;
use Illuminate\Database\Seeder;

class StatusData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::insert([
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
