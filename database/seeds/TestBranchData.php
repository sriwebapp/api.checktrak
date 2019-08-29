<?php

use App\User;
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
            ['code' => 'HO', 'name' => 'Head Office', 'incharge_id' => 1],
            ['code' => 'B1', 'name' => 'Branch01', 'incharge_id' => 6],
            ['code' => 'B2', 'name' => 'Branch02', 'incharge_id' => 7],
            ['code' => 'B3', 'name' => 'Branch03', 'incharge_id' => 8],
            ['code' => 'B4', 'name' => 'Branch04', 'incharge_id' => 9],
            ['code' => 'B5', 'name' => 'Branch05', 'incharge_id' => 10],
        ]);

        User::first()->update([ 'branch_id' => 1 ]);

        User::whereIn('id', [2, 3, 4, 5, 11])->get()->each( function($user) {
            $user->update([ 'branch_id' => 1 ]);
        });

        User::find(6)->update([ 'branch_id' => 2 ]);
        User::find(7)->update([ 'branch_id' => 3 ]);
        User::find(8)->update([ 'branch_id' => 4 ]);
        User::find(9)->update([ 'branch_id' => 5 ]);
        User::find(10)->update([ 'branch_id' => 6 ]);
    }
}
