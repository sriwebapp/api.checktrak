<?php

use App\Account;
use Illuminate\Database\Seeder;

class TestAccountData extends Seeder
{
    public function run()
    {
        factory(Account::class, 20)->create();
    }
}
