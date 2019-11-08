<?php

namespace App\Http\Controllers;

use Excel;
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
use App\Exports\TransmittalExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ChecksReceivedNotification;
use App\Notifications\TransmittalDueNotification;
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
        $transmittal = Transmittal::find(8);

        // return $transmittal->checks;

        return Excel::download(new TransmittalExport($transmittal), $transmittal->ref . '.xlsx');

        $report = new TransmittalExport($transmittal);

        return $report->download('invoices.csv', Excel::CSV, 'text/csv');
    }
}
