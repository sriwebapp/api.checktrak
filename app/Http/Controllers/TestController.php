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
        // date and number filter [4, 5]
        return $company->checks()
            // ->whereBetween('date', ['2019-08-29', '2019-08-30'])
            // ->whereBetween('number', ['1787021', '1880318'])
            ->count();
            // ->get();

        // status filter [7]
        return $company->checks()
            ->whereIn('status_id', [1, 2, 3])
            ->where('received', false)
            ->count();
            // ->get();

        // details filter [6]
        return $company->checks()
            ->where('details', 'like', '%' . 'a' . '%')
            ->count();
            // ->get();


        // account and payee filter [1, 2]
        return $company->checks()
            // ->where('account_id', 1)
            // ->where('payee_id', 47)
            // ->count();
            ->get();

        // transmittal filter [3]
        // return Transmittal::find(28)->checks;
    }
}
