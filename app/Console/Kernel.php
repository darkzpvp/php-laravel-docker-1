<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('suscripcion:create')->daily(); //Resetea los prompts de las suscripciones a su valor
        $schedule->command('suscripcion:caducada')->daily(); //Analiza suscripciones caducadas y las borra
        $schedule->command('app:usuario-imagen')->monthly(); //Analiza imagenes en la carpeta y si no existe, desvincula la imagen del usuario en la bbdd
    }                                                          //esto puede ser util por si pierdo las imagenes

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
