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
            ['name' => 'Arnel Forbes', 'email' => 'forbesarnel09@gmail.com', 'password' => $password, 'group_id' => 2],
        ]);

    }
}
