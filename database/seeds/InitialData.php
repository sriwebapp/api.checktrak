<?php

use App\User;
use Illuminate\Database\Seeder;

class InitialData extends Seeder
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
            'email' => 'sriwebapp@gmail.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'group_id' => 1, // system administrator
        ]);

        User::create([
            'name' => 'Michelle Villa',
            'email' => 'michelle.villa@serviceresourcesinc.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'group_id' => 2, // administrator
            'branch_id' => 1, // head office
        ]);
    }
}
