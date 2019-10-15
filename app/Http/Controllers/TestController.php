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
use App\Transmittal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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

    public function index(Request $request, Transmittal $transmittal)
    {
        return Access::find(2)->users;
        $recipients = $transmittal->group->incharge;
        $recipients->push(User::find(4));
        return $recipients->merge(User::where('id', 1)->get());
        return $recipients;
        return User::find(4);
        $recipients = User::whereIn('id', [1, 2, 1])->get();
        return $recipients->merge(User::whereIn('id', [3, 4, 3])->get());

        return $merged;
    }
}
