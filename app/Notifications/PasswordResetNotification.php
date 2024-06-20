<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablecer Contraseña')
            ->greeting('Hola ' . $this->user->name . ',')
            ->line('Recibes este correo electrónico porque solicitaste un cambio de contraseña para tu cuenta.')
            ->action('Cambiar Contraseña', url('https://forstai.ddns.net/reset/'.$this->token))
            ->line('Si no solicitaste este cambio de contraseña, no es necesario realizar ninguna acción.')
            ->salutation('Gracias, ' . config('app.name'));
    }
}
