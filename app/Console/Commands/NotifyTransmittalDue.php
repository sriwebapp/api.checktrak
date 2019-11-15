<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use App\Transmittal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TransmittalDueNotification;

class NotifyTransmittalDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:trans-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify incharge about transmittal due.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transmittals = Transmittal::where('returned_all', 0)
            ->whereDate('due', Carbon::now()->addDays(1))
            ->get();

        $transmittals->each( function($transmittal) {
            Notification::send($transmittal->inchargeUser, new TransmittalDueNotification($transmittal));

            Log::info('Transmittal due notification sent to ' . $transmittal->inchargeUser->email . '.');
        });
    }
}
