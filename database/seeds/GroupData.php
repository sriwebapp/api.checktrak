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
            ['name' => 'Disbursement', 'branch_id' => 1]
        ]);
    }
}
