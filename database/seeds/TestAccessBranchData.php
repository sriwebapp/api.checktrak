<?php

use App\Access;
use App\Branch;
use Illuminate\Database\Seeder;

class TestAccessBranchData extends Seeder
{
    public function run()
    {
        $admin = Access::where('id', 3)->first();

        $admin->branches()->sync(Branch::get());
    }
}
