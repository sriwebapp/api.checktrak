<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index($check)
    {
        return \App\CheckBook::where('start_series', '<=', $check)->where('end_series', '>=', $check)->whereRaw('length(start_series) = ' . strlen($check))->get();

        $account = \App\Account::find(13);

        $account->checks->each(function($check) use ($account) {
            $checkbook = $account->checkbooks()->where('start_series', '<=', $check->number)->where('end_series', '>=', $check->number)->whereRaw('length(start_series) = ' . strlen($check->number))->first();

            $check->update(['check_book_id' => ($checkbook ? $checkbook->id: null)]);
        });
    }
}
