<?php

use App\Access;
use App\Action;
use Illuminate\Database\Seeder;

class TestAccessActionData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Access::where('id', 2)->first();
        $head = Access::where('id', 3)->first();
        $branch = Access::where('id', 4)->first();

        $admin->sync(Action::where('id', '<>', 5)->get());
        $head->sync(Action::where('id', '<>', 5)->where('id', '<>', 11)->get());
        $branch->actions()->sync(Action::whereIn('id', [3, 4, 5])->get());
    }
}
