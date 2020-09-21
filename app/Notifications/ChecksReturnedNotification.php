<?php

namespace App\Notifications;

use App\Transmittal;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;

class ChecksReturnedNotification extends Notification
{
    protected $transmittal;
    protected $checks;

    public function __construct(Transmittal $transmittal, Collection $checks)
    {
        $this->transmittal = $transmittal;
        $this->checks = $checks;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $transmittal = $this->transmittal;
        $claimed = $transmittal->checks->filter( function($check) {
            return $check->history->first( function($h) {
                return $h->action_id === 4 && $h->active === 1;;
            });
        });

        return (new MailMessage)
                    ->subject('Checks Returned')
                    ->greeting('Hi Disbursement Group!')
                    ->line('You will receive returned ' . $transmittal->ref . ' with the following details:')
                    ->line('Date Transmitted : ' . \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->date)->format('M d, Y'))
                    ->line('Date Returned  : ' . \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->returned)->format('M d, Y'))
                    ->line('Total No of Checks Transmitted : ' . $transmittal->checks->count() )
                    ->line('Total No of Checks Claimed : ' . $claimed->count() )
                    ->line('Total No of Checks Returned : ' . $this->checks->count() )
                    ->action('Go To App', url(config('app.ui_url') . '/transmittal/' . $transmittal->id))
                    ->line('Please see attached for your reference.')
                    ->attach(public_path('pdf/transmittal/' . $transmittal->ref . '-1.pdf'));

    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
