<?php
namespace App\Console\Commands;
use App\Models\Suscripciones;
use Illuminate\Console\Command;
class SuscripcionCaducada extends Command
{
    protected $signature = 'suscripcion:caducada';
    protected $description = 'Verificar y eliminar suscripciones caducadas';
    public function handle()
    {
        // Buscar suscripciones caducadas
        $suscripcionesCaducadas = Suscripciones::where('fecha_expiracion', '<', now())->get();

        foreach ($suscripcionesCaducadas as $suscripcion) {
            // Eliminar suscripción caducada
            $suscripcion->delete();
            $this->info('Suscripción caducada eliminada: ' . $suscripcion->id);
        }

        if ($suscripcionesCaducadas->isEmpty()) {
            $this->info('No se encontraron suscripciones caducadas para eliminar.');
        }

        return 0;
    }
}
