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
            ['name' => 'Create'],
            ['name' => 'Transmit'],
            ['name' => 'Receive'],
            ['name' => 'Claim'],
            ['name' => 'Return'],
            ['name' => 'Cancel'],
        ]);
    }
}
