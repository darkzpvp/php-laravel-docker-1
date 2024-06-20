<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Suscripciones;
class SuscripcionMensual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscripcion:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reestablece los prompts mensualmente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener todas las suscripciones
        $suscripciones = Suscripciones::all();

        // Actualizar los prompts_disponibles según el plan de suscripción
        foreach ($suscripciones as $suscripcion) {
            // Obtener el tipo de suscripción y definir prompts según el tipo
            $prompts_disponibles = $this->obtenerPromptsSegunTipo($suscripcion->tipo);

            // Actualizar prompts_disponibles
            $suscripcion->prompts_disponibles = $prompts_disponibles;
            $suscripcion->save();
        }

        $this->info('Las suscripciones mensuales han sido actualizadas.');
    }

    // Función para obtener la cantidad de prompts disponibles según el tipo de suscripción
    private function obtenerPromptsSegunTipo($tipo)
    {
        switch ($tipo) {
            case "basico":
                return 10; // Para el plan básico, prompts_disponibles se establece en 10
            case "estandar":
                return 25; // Para el plan estándar, prompts_disponibles se establece en 25
            case "premium":
                return 9999; // Para el plan premium, prompts_disponibles se establece en 9999
            default:
                return 0; // Valor predeterminado en caso de tipo de suscripción no válido
        }
    }
}
