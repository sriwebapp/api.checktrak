<?php

use App\Group;
use App\Module;
use Illuminate\Database\Seeder;

class TestGroupModuleData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = Group::whereIn('id', [1, 2])->get();
        $users = Group::where('id', 3)->get();

        $admins->map( function($admin) {
            return $admin->modules()->sync(Module::get());
        });

        $users->map( function($user) {
            return $user->modules()->sync( Module::whereId(6)->get() );
        });
    }
}
