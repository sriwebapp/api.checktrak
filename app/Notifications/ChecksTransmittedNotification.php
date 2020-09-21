<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ChecksTransmittedNotification extends Notification
{
    protected $transmittal;

    public function __construct($transmittal)
    {
        $this->transmittal = $transmittal;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $transmittal = $this->transmittal;

        return (new MailMessage)
                    ->subject('Checks Transmitted')
                    ->greeting('Hello ' . $transmittal->inchargeUser->name . '!')
                    ->line(
                        'You will receive ' . $transmittal->ref . ' with ' . $transmittal->checks->count() . ' checks ' .
                        ' and total amount of Php ' . number_format($transmittal->checks->sum('amount'), 2, '.', ',') . '. ' .
                        'Please click here for your reference.'
                    )
                    ->action('Go To App', url(config('app.ui_url') . '/transmittal/' . $transmittal->id))
                    ->line('Kindly check once received.')
                    ->attach(public_path('pdf/transmittal/' . $transmittal->ref . '.pdf'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
