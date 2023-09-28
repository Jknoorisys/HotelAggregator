<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentRegistration extends Notification
{
    use Queueable;
    public $name;
    public $email;
    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($name, $email, $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject(trans('msg.email.registration-subject'))
                    ->greeting(trans('msg.email.salutation').' ' . $this->name)
                    ->line(trans('msg.email.email').': ' . $this->email)
                    ->line(trans('msg.email.password') .': ' . $this->password)
                    ->line(trans('msg.email.registration-msg'))
                    ->action(trans('msg.email.Login'), route('/'))
                    ->line(trans('msg.email.registration-thanks'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
