<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyRegistrationEmail extends Notification
{
    use Queueable;

    public function __construct(
        protected string $email,
        protected string $code,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Urbanist verification code')
            ->view('emails.registration-code', [
                'email' => $this->email,
                'code' => $this->code,
                'expiresInMinutes' => 60,
            ]);
    }

    public function verificationCode(): string
    {
        return $this->code;
    }
}
