<?php

use App\User;
use App\Branch;
use Illuminate\Database\Seeder;

class TestUserBranchData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereNotIn('access_id', [1, 2, 3])->get();

        $users->map( function ( $user ) {
            $branches = Branch::where('incharge_id', $user->id)->get();

            if ( ! $branches->count() ) {
                $branches = Branch::whereIn('id', [2, 4, 5])->get();
            }

            return $user->branches()->sync($branches);
        });
    }
}
