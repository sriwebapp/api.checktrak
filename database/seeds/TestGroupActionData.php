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
        $head = Group::where('id', 3)->first();
        $branch = Group::where('id', 4)->first();

        $head->actions()->sync(Action::where('id', '<>', 5)->get());
        $branch->actions()->sync(Action::whereIn('id', [3, 4, 5])->get());
    }
}
