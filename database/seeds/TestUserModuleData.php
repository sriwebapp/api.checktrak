<?php

use App\User;
use App\Module;
use Illuminate\Database\Seeder;

class TestUserModuleData extends Seeder
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
            return $user->modules()->sync( Module::whereIn('id', [1, 2, 6])->get() );
        });
    }
}
