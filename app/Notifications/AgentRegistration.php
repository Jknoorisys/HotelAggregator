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
                ->subject(trans('msg.email.agent_credentials.subject'))
                ->line(trans('msg.email.agent_credentials.greeting', ['name' => $this->name]))
                ->line(trans('msg.email.agent_credentials.email', ['email' => $this->email]))
                ->line(trans('msg.email.agent_credentials.password', ['password' => $this->password]))
                ->line(trans('msg.email.agent_credentials.keep_secure'))
                ->action(trans('msg.email.agent_credentials.login'), route('/'))
                ->line(trans('msg.email.agent_credentials.thank_you'));
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
