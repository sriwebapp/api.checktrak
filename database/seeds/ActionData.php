<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('actions')->insert([
            ['name' => 'Create', 'code' => 'crt', 'color' => 'indigo'],
            ['name' => 'Transmit', 'code' => 'trm', 'color' => 'blue'],
            ['name' => 'Receive', 'code' => 'rcv', 'color' => 'green'],
            ['name' => 'Claim', 'code' => 'clm', 'color' => 'purple'],
            ['name' => 'Return', 'code' => 'rtn', 'color' => 'blue-grey'],
            ['name' => 'Cancel', 'code' => 'cnl', 'color' => 'red'],
            ['name' => 'Clear', 'code' => 'clr', 'color' => 'teal'],
            ['name' => 'Edit', 'code' => 'edt', 'color' => 'orange'],
            ['name' => 'Delete', 'code' => 'dlt', 'color' => 'red'],
            ['name' => 'Import', 'code' => 'imt', 'color' => 'indigo'],
            ['name' => 'Undo', 'code' => 'imt', 'color' => 'red'],
        ]);
    }
}
