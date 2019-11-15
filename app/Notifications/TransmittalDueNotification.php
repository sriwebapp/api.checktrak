<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TransmittalDueNotification extends Notification
{
    protected $transmittal;

    public function __construct($transmittal)
    {
        //
        $this->transmittal = $transmittal;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $transmittal = $this->transmittal;
        $due = new Carbon($transmittal->due);

        return (new MailMessage)
                    ->subject('Transmittal Due for Return')
                    ->greeting('Hello ' . $transmittal->inchargeUser->name . '!')
                    ->line($transmittal->ref . ' is due for return tomorrow: ' . $due->format('m/d/Y') . '.')
                    ->action('Go to App', url(config('app.ui_url')))
                    ->line('Kindly check!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
