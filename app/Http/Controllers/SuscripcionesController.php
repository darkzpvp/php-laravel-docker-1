<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Suscripciones;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class SuscripcionesController extends Controller
{
    public function comprar(Request $request)
{
    // Obtener la suscripción del usuario autenticado
    $suscripcion = Suscripciones::where('user_id', Auth::id())->first();

    // Validar la solicitud

    // Obtener la ID de la suscripción del request
    $id_suscripcion = $request->input('id_suscripcion');

    // Si la ID de suscripción es 0, eliminar la suscripción existente (si la hay)
    if ($id_suscripcion === "0") {
        // Eliminar la suscripción existente (si la hay)
        if ($suscripcion) {
            $suscripcion->delete();
        }
    }

    // Obtener el tipo de suscripción y definir prompts y precio según el tipo
    list($tipo_suscripcion, $prompts_disponibles, $precio) = $this->obtenerTipoSuscripcion($id_suscripcion);

    // Crear o actualizar la suscripción del usuario
    if ($suscripcion) {
        // Si existe una suscripción, actualizarla
        $suscripcion->update([
            'tipo' => $tipo_suscripcion,
            'prompts_disponibles' => $prompts_disponibles,
            'precio' => $precio
        ]);
    } else {
        // Si no existe una suscripción para este usuario, crear una nueva
        Suscripciones::create([
            'user_id' => Auth::id(),
            'tipo' => $tipo_suscripcion,
            'prompts_disponibles' => $prompts_disponibles,
            'precio' => $precio
        ]);
    }

    // Respuesta de éxito
    return response()->json(['message' => 'Suscripción comprada con éxito'], 200);
}

    // Función para obtener el tipo de suscripción y sus detalles según la ID recibida
    public function obtenerTipoSuscripcion($id)
    {
        switch ($id) {
            case "1":
                return ['basico', 10, 9];
            case "2":
                return ['estandar', 25, 19];
            case "3":
                return ['premium', 9999, 25];
            default:
                return [null, null, null];
        }
    }
    public function eliminar() {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            // Encontrar las suscripciones del usuario actual
            $suscripciones = Suscripciones::where('user_id', Auth::id())->first();
    
            // Verificar si se encontraron suscripciones
            if ($suscripciones) {
                // Eliminar las suscripciones encontradas
                $suscripciones->delete();
                return "Suscripciones eliminadas correctamente";
            } else {
                return "No se encontraron suscripciones para este usuario";
            }
        } else {
            return "El usuario no está autenticado";
        }
    }
    public function getAll()
    {
        // Obtener la suscripción del usuario autenticado
        $suscripcion = Suscripciones::where('user_id', Auth::id())->first();
        
        if (is_null($suscripcion)) {
            // No hay suscripción, devuelve el valor de free_prompts del usuario autenticado
            $user = Auth::user();
            return response()->json(['free_prompts' => $user->free_prompts], 200);
        }
    
        // Construir los datos de la suscripción
        $totalPrompts = $suscripcion->user->free_prompts + $suscripcion->prompts_disponibles;
        $datos = [
            'prompts' => $totalPrompts,
            'tipo' => $suscripcion->tipo,
            'precio' => $suscripcion->precio,
            'fecha_expiracion' => $suscripcion->fecha_expiracion,
            'comprado' => $suscripcion->created_at
        ];
        
        return response()->json($datos);
    }
   

}
