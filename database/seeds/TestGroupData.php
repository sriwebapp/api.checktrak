<?php

use App\Group;
use App\Branch;
use Illuminate\Database\Seeder;

class TestGroupData extends Seeder
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
        ]);

        Branch::where('id', '<>', 1)->each(function ($branch) {
            for ($i=0; $i < 5; $i++) {
                Group::insert([
                    'name' => $branch->code . '-' . $i,
                    'branch_id' => $branch->id,
                ]);
            }
        });
    }
}
