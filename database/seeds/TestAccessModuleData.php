<?php

use App\Access;
use App\Module;
use Illuminate\Database\Seeder;

class TestAccessModuleData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $admins = Access::where('id', 2)->get();
        $users = Access::where('id', 3)->get();

        // $admins->map( function($admin) {
        //     return $admin->modules()->sync(Module::get());
        // });

        $users->map( function($user) {
            return $user->modules()->sync( Module::whereId(6)->get() );
        });
    }
}
