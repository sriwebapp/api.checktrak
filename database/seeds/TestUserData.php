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
        factory(User::class, 3000)->create();
    }
}
