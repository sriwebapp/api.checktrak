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

    public function index(Company $company)
    {
        return Action::where('id', '<>', 5)->where('id', '<>', 11)->get();
    }
}
