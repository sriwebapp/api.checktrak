<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class TestUserData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password

        User::insert([
                ['name' => 'Admin 01', 'email' => 'admin01@example.com', 'password' => $password, 'group_id' => 2],
                ['name' => 'Admin 02', 'email' => 'admin02@example.com', 'password' => $password, 'group_id' => 2],
                ['name' => 'HO 01', 'email' => 'ho01@example.com', 'password' => $password, 'group_id' => 3],
                ['name' => 'HO 02', 'email' => 'ho02@example.com', 'password' => $password, 'group_id' => 3],
                ['name' => 'BO 01', 'email' => 'bo01@example.com', 'password' => $password, 'group_id' => 4],
                ['name' => 'BO 02', 'email' => 'bo02@example.com', 'password' => $password, 'group_id' => 4],
                ['name' => 'BO 03', 'email' => 'bo03@example.com', 'password' => $password, 'group_id' => 4],
                ['name' => 'BO 04', 'email' => 'bo04@example.com', 'password' => $password, 'group_id' => 4],
                ['name' => 'BO 05', 'email' => 'bo05@example.com', 'password' => $password, 'group_id' => 4],
                ['name' => 'Custom User', 'email' => 'custom@example.com', 'password' => $password, 'group_id' => 5],
        ]);

    }
}
