<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserRegisteredNotification extends Notification
{
    protected $password;
    protected $user;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Registered User')
                    ->greeting('Hello ' . $this->user->name . '!')
                    ->line('You can now access Check Monitoring as ' . $this->user->access->name . '.')
                    ->line('Username: ' . $this->user->username)
                    ->line('Password: ' . $this->password)
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
