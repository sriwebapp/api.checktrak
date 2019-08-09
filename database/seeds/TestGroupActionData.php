<?php

use App\Group;
use App\Action;
use Illuminate\Database\Seeder;

class TestGroupActionData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = Group::whereIn('id', [1, 2])->get();
        $head = Group::where('id', 3)->get();
        $branch = Group::where('id', 4)->get();

        $admins->map( function($admin) {
            return $admin->actions()->sync( Action::all() );
        });

        $head->map( function($user) {
            return $user->actions()->sync( Action::where('id', '<>', 5)->get() );
        });

        $branch->map( function($user) {
            return $user->actions()->sync( Action::whereIn('id', [3, 4, 5])->get() );
        });
    }
}
