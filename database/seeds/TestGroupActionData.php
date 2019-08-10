<?php

use App\Group;
use App\Action;
use Illuminate\Database\Seeder;

class TestGroupActionData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Group::where('id', 2)->first();
        $head = Group::where('id', 3)->first();
        $branch = Group::where('id', 4)->first();

        $admin->actions()->sync(Action::all());
        $head->actions()->sync(Action::where('id', '<>', 5)->get());
        $branch->actions()->sync(Action::whereIn('id', [3, 4, 5])->get());
    }
}
