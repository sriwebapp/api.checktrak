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
            ['code' => 'HO', 'name' => 'Head Office'],
            ['code' => 'B1', 'name' => 'Branch01'],
            ['code' => 'B2', 'name' => 'Branch02'],
            ['code' => 'B3', 'name' => 'Branch03'],
            ['code' => 'B4', 'name' => 'Branch04'],
            ['code' => 'B5', 'name' => 'Branch05'],
        ]);

        User::first()->update([ 'branch_id' => 1 ]);

        // User::whereIn('id', [2, 3, 4, 5, 11])->get()->each( function($user) {
        //     $user->update([ 'branch_id' => 1 ]);
        // });

        // User::find(6)->update([ 'branch_id' => 2 ]);
        // User::find(7)->update([ 'branch_id' => 3 ]);
        // User::find(8)->update([ 'branch_id' => 4 ]);
        // User::find(9)->update([ 'branch_id' => 5 ]);
        // User::find(10)->update([ 'branch_id' => 6 ]);
    }
}
