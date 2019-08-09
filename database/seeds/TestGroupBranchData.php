<?php

use App\Group;
use App\Branch;
use Illuminate\Database\Seeder;

class TestGroupBranchData extends Seeder
{
    public function run()
    {
        $admins = Group::whereIn('id', [1, 2, 3])->get();

        $branches = Branch::get();

        $admins->map( function($admin) use ($branches) {
            return $admin->branches()->sync($branches);
        });
    }
}
