<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via(object $notifiable): array
    {
        // we only need email
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Two-Factor Verification Code')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('Use the following verification code to complete your login:')
            ->line('**'.$this->code.'**')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not try to log in, you can safely ignore this email.');
    }
}
