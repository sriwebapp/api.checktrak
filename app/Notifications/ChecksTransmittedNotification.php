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
        return (new MailMessage)
                    ->subject('Checks Transmitted')
                    ->greeting('Hello ' . $this->transmittal->inchargeUser->name . '!')
                    ->line(
                        'You will receive ' . $this->transmittal->checks->count() . ' checks, ' .
                        ' with total amount of Php ' . $this->transmittal->checks->sum('amount') . '.'
                    )
                    ->action('Go to App', url(config('app.ui_url')))
                   ->line('Thank you for using our application!');
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
