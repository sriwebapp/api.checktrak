<?php

use App\Branch;
use Illuminate\Database\Seeder;

class BranchData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::insert([
            ['code' => 'HO', 'name' => 'Head Office'],
            ['code' => 'CAV', 'name' => 'Cavite'],
            ['code' => 'CAL', 'name' => 'Calamba'],
            ['code' => 'CP', 'name' => 'Clark'],
            ['code' => 'LP', 'name' => 'Lipa, Batangas'],
            ['code' => 'ST', 'name' => 'Sto. Tomas, Batangas'],
        ]);
    }
}
