<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index($check)
    {
        return 'This is a test route';

        // run this in production
        // php artisan migrate
        // php artisan db:seed --class=ReportData
    }
}
