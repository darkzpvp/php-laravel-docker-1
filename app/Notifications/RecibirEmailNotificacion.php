<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecibirEmailNotificacion extends Notification
{
    use Queueable;
    public $emailData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo correo electrónico recibido')
            ->greeting('Hola, ')
            ->line('Has recibido un nuevo correo electrónico.')
            ->line('Nombre: ' . $this->emailData['name'])
            ->line('Email: ' . $this->emailData['email'])
            ->line('Mensaje: ' . $this->emailData['message'])
            ->salutation('ForstAI');

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
