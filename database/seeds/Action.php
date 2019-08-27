<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Action extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('actions')->insert([
            ['name' => 'Create', 'code' => 'crt'],
            ['name' => 'Transmit', 'code' => 'trm'],
            ['name' => 'Receive', 'code' => 'rcv'],
            ['name' => 'Claim', 'code' => 'clm'],
            ['name' => 'Return', 'code' => 'rtn'],
            ['name' => 'Cancel', 'code' => 'cnl'],
            ['name' => 'Clear', 'code' => 'clr'],
            ['name' => 'Edit', 'code' => 'edt'],
            ['name' => 'Delete', 'code' => 'dlt'],
        ]);
    }
}
