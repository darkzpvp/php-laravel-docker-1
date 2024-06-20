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
            // Eliminar la parte /api de la URL
            $spaUrl = str_replace('/api', '', $url);
            
            // Eliminar el puerto si está presente
            $spaUrl = preg_replace('/:[0-9]+/', '', $spaUrl);
        
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica la dirección de correo electrónico')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('¡Pincha en el botón y verifica la cuenta!')
                ->action('Verificar email', $spaUrl)
                ->salutation('Gracias, ' . config('app.name'));
        });
    }
}