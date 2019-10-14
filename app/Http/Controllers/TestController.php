<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Group;
use App\Action;
use App\Branch;
use App\Module;
use App\Company;
use App\Transmittal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\ChecksTransmittedNotification;

class TestController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'usr')->first();
    }

    public function index(Request $request, Transmittal $transmittal)
    {
        $transmittal->company;
        $transmittal->checks = $transmittal->checks()->with('history')->with('payee')->get();
        $transmittal->user;
        $transmittal->inchargeUser;

        $transmittal->checks->map( function($check) {
            $claimed = $check->history->first( function($h) {
                return $h->action_id === 4;
            });
            $check->claimed = $claimed ? $claimed->date : null;
            return $check;
        });

        // return $transmittal->checks->where('claimed', null)->count();

        // return $transmittal;

        // return view('pdf.return', compact('transmittal'));

        return \PDF::loadView('pdf.return', compact('transmittal'))
            ->setPaper('letter', 'portrait')
            ->save( public_path() . '/pdf/transmittal/' . $transmittal->ref . '-1.pdf')
            ->stream();
    }
}
