<?php

use App\User;
use App\Group;
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
            $groups = Group::where('id', '<>', 1)->get();
            $users = User::pluck('id');

            $company->checks->chunk(2)->each( function($checks, $key) use ($groups, $company, $users) {
                $group = $groups->random();
                $user = $users->random();
                $incharge = $users->random();

                Transmittal::create([
                    'group_id' => $group->id,
                    'branch_id' => $group->branch->id,
                    'company_id' => $company->id,
                    'year' => date('Y'),
                    'series' => substr('0000' . $key, -4),
                    'user_id' => $user,
                    'incharge' => $incharge,
                    'date' => date('Y-m-d'),
                    'due' => date('Y-m-d'),
                    'ref' => $company->code . '-' . $group->branch->code . '-' . date('Y') . '-' . substr('0000' . $key, -4) ,
                ])->checks()->sync($checks);

                $checks->each( function($check) use ($group, $user, $incharge) {
                    $check->update([
                        'status_id' => 2,
                        'received' => 1,
                        'group_id' => $group->id,
                        'branch_id' => $group->branch->id
                    ]); // transmitted

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
