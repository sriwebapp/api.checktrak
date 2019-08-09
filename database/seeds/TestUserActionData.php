<?php

use App\User;
use App\Action;
use Illuminate\Database\Seeder;

class TestUserActionData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('group_id', 5)->get();

        $users->map( function($user) {
            return $user->actions()->sync( Action::whereIn('id', [1, 5, 6])->get() );
        });
    }
}
