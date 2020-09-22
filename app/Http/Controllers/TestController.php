<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index($check)
    {
        return \App\CheckBook::withTrashed()->find(8);

        return \App\CheckBook::where('start_series', '<=', $check)->where('end_series', '>=', $check)->whereRaw('length(start_series) = ' . strlen($check))->get();

        // run this to create check->checkbook relationship
        \App\Account::get()->each( function($account) {
            $account->checks->each(function($check) use ($account) {
                $checkbook = $account->checkbooks()->where('start_series', '<=', $check->number)->where('end_series', '>=', $check->number)->whereRaw('length(start_series) = ' . strlen($check->number))->first();

                $check->update(['check_book_id' => ($checkbook ? $checkbook->id: null)]);
            });
        });

        // run this to update checkbooks
        \App\CheckBook::withTrashed()->get()->each( function($checkbook) {
            $checkbook->update(['total' => ($checkbook->end_series - $checkbook->start_series) + 1, 'posted' => $checkbook->postedChecks->count(), 'available' => (($checkbook->end_series - $checkbook->start_series) + 1) -  $checkbook->postedChecks->count()]);
        });
    }
}
