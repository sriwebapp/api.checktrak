<?php

use App\Group;
use App\Branch;
use Illuminate\Database\Seeder;

class TestGroupBranchData extends Seeder
{
    public function run()
    {
        $admin = Group::where('id', 3)->first();

        $admin->branches()->sync(Branch::get());
    }
}
