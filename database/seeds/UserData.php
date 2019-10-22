<?php

use App\User;
use Illuminate\Database\Seeder;

class UserData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'System Administrator',
            'username' => 'developer',
            'email' => 'sriwebapp@gmail.com',
            'password' => bcrypt(config('app.default_pass')), // password
            'branch_id' => 1, // head office
            'access_id' => 1, // system administrator
        ]);

        User::create([
            'name' => 'Michelle Villa',
            'username' => 'mich',
            'email' => 'michelle.villa@serviceresourcesinc.com',
            'password' => bcrypt(config('app.default_pass')), // password
            'access_id' => 2, // administrator
            'branch_id' => 1, // head office
        ]);
    }
}
