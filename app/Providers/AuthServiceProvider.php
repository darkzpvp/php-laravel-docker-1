<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            // Obtener la URL base del frontend desde la configuración
            $frontendUrl = config('app.frontend_url');

            // Reemplazar la parte /api de la URL y eliminar el puerto si está presente
            $spaUrl = str_replace('/api', '', $url);
            $spaUrl = preg_replace('/:[0-9]+/', '', $spaUrl);

            // Construir la URL completa para el frontend
            $frontendVerificationUrl = rtrim($frontendUrl, '/') . $spaUrl;

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica la dirección de correo electrónico')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('¡Pincha en el botón y verifica la cuenta!')
                ->action('Verificar email', $frontendVerificationUrl)
                ->salutation('Gracias, ' . config('app.name'));
        });
    }
}
