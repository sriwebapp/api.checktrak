<?php

use App\Payee;
use Illuminate\Database\Seeder;

class TestPayeeData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Payee::class, 100)->create();
    }
}
