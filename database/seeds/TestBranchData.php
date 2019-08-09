<?php

use App\Branch;
use Illuminate\Database\Seeder;

class TestBranchData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::insert([
            ['code' => 'B1', 'name' => 'Branch01', 'incharge' => 6],
            ['code' => 'B2', 'name' => 'Branch02', 'incharge' => 7],
            ['code' => 'B3', 'name' => 'Branch03', 'incharge' => 8],
            ['code' => 'B4', 'name' => 'Branch04', 'incharge' => 9],
            ['code' => 'B5', 'name' => 'Branch05', 'incharge' => 10],
        ]);
    }
}
