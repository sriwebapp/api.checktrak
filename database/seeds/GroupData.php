<?php

use App\Group;
use App\Branch;
use Illuminate\Database\Seeder;

class GroupData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::insert([
            ['name' => 'Disbursement', 'branch_id' => 1],
            ['name' => 'Admin', 'branch_id' => 1],
            ['name' => 'JAE', 'branch_id' => 2],
            ['name' => 'Sanno', 'branch_id' => 2],
            ['name' => 'SDA', 'branch_id' => 2],
            ['name' => 'Pricon', 'branch_id' => 3],
            ['name' => 'Nyk', 'branch_id' => 3],
            ['name' => 'EMD', 'branch_id' => 3],
            ['name' => 'Nanox', 'branch_id' => 4],
            ['name' => 'Bandai', 'branch_id' => 5],
            ['name' => 'Brother', 'branch_id' => 6],
            ['name' => 'Fuji', 'branch_id' => 6],
            ['name' => 'IMD', 'branch_id' => 6],
        ]);
    }
}
