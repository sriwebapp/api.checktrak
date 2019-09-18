<?php

use App\User;
use App\Branch;
use App\Company;
use App\History;
use App\Transmittal;
use Illuminate\Database\Seeder;

class TestTransmittalData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::get()->each( function($company) {
            $branches = Branch::where('id', '<>', 1)->get();
            $users = User::pluck('id');

            $company->checks->chunk(10)->each( function($checks, $key) use ($branches, $company, $users) {
                $branch = $branches->random();
                $user = $users->random();
                $incharge = $users->random();

                Transmittal::create([
                    'branch_id' => $branch->id,
                    'user_id' => $user,
                    'incharge' => $incharge,
                    'date' => date('Y-m-d'),
                    'due' => date('Y-m-d'),
                    'ref' => $company->code . '-' . $branch->code . '-' . date('Y') . '-' . substr('0000' . $key, -4) ,
                ])->checks()->sync($checks);

                $checks->each( function($check) use ($branch, $user, $incharge) {
                    $check->update([ 'status_id' => 2, 'received' => 1, 'branch_id' => $branch->id]); // transmitted

                    History::insert([
                        [
                            'check_id' => $check->id,
                            'action_id' => 2,
                            'user_id' => $user,
                            'date' => date('Y-m-d'),
                            'remarks' => 'test transmit'
                        ],
                        [
                            'check_id' => $check->id,
                            'action_id' => 3,
                            'user_id' => $incharge,
                            'date' => date('Y-m-d'),
                            'remarks' => 'test receive'
                        ]
                    ]);
                });
            });
        });
    }
}
