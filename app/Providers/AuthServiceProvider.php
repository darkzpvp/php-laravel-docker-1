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
            // URL base específica del frontend que deseas utilizar
            $frontendUrl = 'https://forst-ai.vercel.app/';
        
            // Obtener los parámetros de la URL generada por Laravel
            $params = parse_url($url);
        
            // Eliminar la parte /api de la URL
            $path = str_replace('/api', '', $params['path']);
        
            // Eliminar el puerto si está presente
            $path = preg_replace('/:[0-9]+/', '', $path);
        
            // Construir la nueva URL del frontend con el path y los parámetros
            $spaUrl = $frontendUrl . ltrim($path, '/') . '?' . $params['query'];
        
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica la dirección de correo electrónico')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('¡Pincha en el botón y verifica la cuenta!')
                ->action('Verificar email', $spaUrl)
                ->salutation('Gracias, ' . config('app.name'));
        });
    }
}