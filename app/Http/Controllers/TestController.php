<?php

namespace App\Http\Controllers;

use App\User;
use App\Check;
use App\Group;
use App\Access;
use App\Action;
use App\Branch;
use App\Module;
use App\Company;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TransmittalDueNotification;
use App\Notifications\ChecksReceivedNotification;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\ChecksTransmittedNotification;

class TestController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('code', 'usr')->first();
    }

    public function index()
    {
        $transmittals = Transmittal::where('returned_all', 0)
            ->whereDate('due', Carbon::now()->addDays(1))
            ->first();

        $transmittals->each( function($transmittal) {
            Notification::send($transmittal->inchargeUser, new TransmittalDueNotification($transmittal));
        });


        return $transmittals;
    }
}
