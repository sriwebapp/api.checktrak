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
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'branch_id' => 1, // head office
            'access_id' => 1, // system administrator
        ]);

        User::create([
            'name' => 'Michelle Villa',
            'username' => 'mich',
            'email' => 'michelle.villa@serviceresourcesinc.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'access_id' => 2, // administrator
            'branch_id' => 1, // head office
        ]);

        User::create([
            'name' => 'Charmaine Carillo',
            'username' => 'charm',
            'email' => 'charmaine.carillo@csic.ph',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'access_id' => 3, // administrator
            'branch_id' => 1, // head office
        ]);
    }
}
