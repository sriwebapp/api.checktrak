<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reports')->insert([
            ['name' => 'Check Masterlist', 'code' => 'chk_mstr'],
        ]);
    }
}
