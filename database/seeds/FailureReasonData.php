<?php

use App\FailureReason;
use Illuminate\Database\Seeder;

class FailureReasonData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FailureReason::insert([
            ['desc' => 'Invalid Data'],
            ['desc' => 'Existing Data'],
            ['desc' => 'Not Existing Payee'],
            ['desc' => 'Not Existing Account'],
            ['desc' => 'Not Existing Check'],
            ['desc' => 'Already Cleared'],
            ['desc' => 'Not Yet Claimed'],
            ['desc' => 'Not Existing Group'],
            ['desc' => 'Not Existing Checkbook'],
        ]);
    }
}
