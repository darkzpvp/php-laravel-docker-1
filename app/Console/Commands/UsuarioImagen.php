<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Asegúrate de usar el modelo correcto de tu aplicación

class UsuarioImagen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:usuario-imagen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica las imágenes de los usuarios y establece el valor a null si no existen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener todos los usuarios
        $usuarios = User::all();

        foreach ($usuarios as $usuario) {
            // Verificar si la imagen existe en el almacenamiento
            if ($usuario->imagen && !Storage::disk('public')->exists($usuario->imagen)) {
                // Si la imagen no existe, establecer el valor de 'imagen' como null y guardar
                $usuario->imagen = null;
                $usuario->save();

                $this->info("Imagen del usuario {$usuario->id} no encontrada. Campo 'imagen' actualizado a null.");
            }
        }

        $this->info('Verificación de imágenes completada.');
    }
}
