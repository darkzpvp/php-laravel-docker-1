<?php

namespace App\Http\Controllers;

use App\Models\Suscripciones;
use Carbon\Carbon; 
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\InformacionPersonal;
use App\Models\Prompt;


class AdministradorController extends Controller
{
    public function getCosteTotalUltimaSemana()
    {
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();
    
        // Fecha y hora de inicio de la semana actual
        $startOfCurrentWeek = Carbon::now()->startOfWeek();
    
        // Obtener el coste total de suscripciones de la semana pasada
        $totalCosteLastWeek = Suscripciones::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->sum('precio');
    
        // Obtener el coste total de suscripciones de la semana actual
        $totalCosteCurrentWeek = Suscripciones::where('created_at', '>=', $startOfCurrentWeek)->sum('precio');
    
        // Calcular el porcentaje de diferencia
        if ($totalCosteLastWeek == 0) {
            $percentageDifference = $totalCosteCurrentWeek > 0 ? 100 : 0;
        } else {
            $percentageDifference = (($totalCosteCurrentWeek - $totalCosteLastWeek) / $totalCosteLastWeek) * 100;
        }
    
        return response()->json([
            'total_coste_semana_pasada' => $totalCosteLastWeek,
            'total_coste_semana_actual' => $totalCosteCurrentWeek,
            'percentage_difference' => $percentageDifference,
        ], 200);
    }

    public function getCosteTotal()
    {
        // Sumar los precios de todas las suscripciones
        $totalCoste = Suscripciones::sum('precio');
    
        return response()->json(['total_coste' => $totalCoste], 200);
    }

    public function informacionUsuarioPanel(Request $request): JsonResponse
{
    // Obtener todos los usuarios
    $usuarios = User::all();

    // Mapear los datos para la respuesta
    $datos = $usuarios->map(function ($user) {
        // Obtener las suscripciones del usuario
        $suscripciones = Suscripciones::where('user_id', $user->id)->get();

        // Si no hay suscripciones, incluir un array con los datos del usuario y tipo null
        if ($suscripciones->isEmpty()) {
            return [
                [
                    'id' => $user->id,
                    'suscripcion' => null,
                    'imagen' => $user->imagen,
                    'estado' => $user->estado,
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'free_prompts' => $user->free_prompts

                ]
            ];
        }

        // Si hay suscripciones, mapear cada una
        return $suscripciones->map(function ($suscripcion) use ($user) {
            return [
                'id' => $user->id,
                'suscripcion' => $suscripcion->tipo,
                'imagen' => $user->imagen,
                'estado' => $user->estado,
                'nombre' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'free_prompts' => $user->free_prompts

            ];
        });
    })->flatten(1); // Aplanar el array resultante

    return response()->json($datos);
}

public function informacionUserId($id): JsonResponse
{
    // Obtener el usuario por su ID
    $usuario = User::findOrFail($id);
    // Obtener la suscripción del usuario
    $suscripcion = Suscripciones::where('user_id', $usuario->id)->first();
    // Calcular totalPrompts
    $totalPrompts = $suscripcion ? ($usuario->free_prompts + ($suscripcion->prompts_disponibles ?? 0)) : $usuario->free_prompts;
    // Obtener los detalles de facturación del usuario
    $detalles_facturacion = InformacionPersonal::where('user_id', $usuario->id)->first();

    // Obtener los prompts del usuario
    $prompts = Prompt::where('user_id', $usuario->id)->get();

    return response()->json([
        'informacion_personal' => [
            'id' => $usuario->id,
            'nombre' => $usuario->name,
            'imagen' => $usuario->imagen,
            'estado' => $usuario->estado,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'ip_address' => $usuario->ip_address,
            'ultima_sesion' => $usuario->ultima_sesion,
            'total_prompts' => $totalPrompts,
        ],
        'suscripcion' => $suscripcion ? [
            'tipo' => $suscripcion->tipo,
            'fecha_expiracion' => $suscripcion->fecha_expiracion,
        ] : null,
        'detalles_facturacion' => $detalles_facturacion ? [
            'nombre' => $detalles_facturacion->nombre,
            'apellidos' => $detalles_facturacion->apellidos,
            'direccion' => $detalles_facturacion->direccion,
            'cp' => $detalles_facturacion->cp,
            'poblacion' => $detalles_facturacion->poblacion,
            'provincia' => $detalles_facturacion->provincia,
            'numero_telefono' => $detalles_facturacion->numero_telefono,
            'pais' => $detalles_facturacion->pais,
            'nif_nie' => $detalles_facturacion->nif_nie,
        ] : null,
        'prompts' => $prompts->isEmpty() ? null : $prompts->map(function($prompt) {
            return [
                'texto' => $prompt->texto,
                'created_at' => $prompt->created_at
            ];
        })
    ]);
}


public function eliminarCuentasUsuarios(Request $request): JsonResponse
{
    $ids = $request->input('ids', []);

    if (!empty($ids)) {
        User::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'Usuarios eliminados correctamente'], 200);
    }

    return response()->json(['success' => false, 'message' => 'No se proporcionaron IDs de usuarios'], 400);
}

public function usuariosUltimaSemana()
{
    // Fecha y hora de hace 7 días (semana actual)
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek = Carbon::now()->endOfWeek();

    // Fecha y hora de hace 14 días a 7 días atrás (semana pasada)
    $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
    $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

    // Obtener el número de usuarios registrados en la semana actual
    $currentWeekUserCount = User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();

    // Obtener el número de usuarios registrados en la semana pasada
    $lastWeekUserCount = User::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

    // Calcular el porcentaje de diferencia
    if ($lastWeekUserCount == 0) {
        $percentageDifference = $currentWeekUserCount > 0 ? 100 : 0;
    } else {
        $percentageDifference = (($currentWeekUserCount - $lastWeekUserCount) / $lastWeekUserCount) * 100;
    }

    return response()->json([
        'current_week_count' => $currentWeekUserCount,
        'last_week_count' => $lastWeekUserCount,
        'percentage_difference' => $percentageDifference,
    ]);
}



public function informacionUserIdActualizar($id, Request $request): JsonResponse {
    $usuario = User::findOrFail($id);

    // Verificar si hay datos en el request para actualizar el usuario
    if ($request->has('nombre')) {
        $usuario->name = $request->nombre;
    }

    if ($request->has('email')) {
        $usuario->email = $request->email;
    }

    if ($request->has('rol')) {
        $usuario->rol = $request->rol;
    }

    if ($request->has('estado')) {
        $usuario->estado = $request->estado;
    }

    if ($request->has('imagen')) {
        $usuario->imagen = $request->imagen;
    }

    if ($request->has('free_prompts')) {
        $usuario->free_prompts = $request->free_prompts;
    }
    // Verificar y actualizar la contraseña
    if ($request->has('password') && $request->has('password_repeat')) {
        $password = $request->input('password');
        $passwordRepeat = $request->input('password_repeat');

        if ($password === $passwordRepeat) {
            $usuario->password = bcrypt($password);
        } else {
            return response()->json(['error' => ['Las contraseñas no coinciden']], 400);
        }
    }

    // Verificar y actualizar la suscripción
    if ($request->has('tipo')) {
        $suscripcion = $request->input('tipo');

        // Buscar la suscripción existente del usuario
        $suscripcionUsuario = Suscripciones::where('user_id', $usuario->id)->first();

        // Si el tipo es "0", eliminar completamente la suscripción
        if ($suscripcion === "0") {
            if ($suscripcionUsuario) {
                $suscripcionUsuario->delete();
            }
        } else {
            // Si no existe una suscripción para este usuario, crear una nueva
            if (!$suscripcionUsuario) {
                // Obtener el tipo de suscripción y sus detalles según la ID recibida
                list($tipo_suscripcion, $prompts_disponibles, $precio) = (new SuscripcionesController)->obtenerTipoSuscripcion($suscripcion);            
                // Crear una nueva suscripción
                Suscripciones::create([
                    'user_id' => $usuario->id,
                    'tipo' => $tipo_suscripcion,
                    'prompts_disponibles' => $prompts_disponibles,
                    'precio' => $precio
                ]);
            } else {
                // Si la suscripción existe, actualizarla
                $suscripcionUsuario->tipo = $suscripcion;
                $suscripcionUsuario->save();
            }
        }
    }

    // Guardar los cambios en el usuario
    $usuario->save();

    // Construir la respuesta
    $datos = [
        'id' => $usuario->id,
        'suscripcion' => $suscripcionUsuario ? $suscripcionUsuario->tipo : null,
        'imagen' => $usuario->imagen,
        'estado' => $usuario->estado,
        'nombre' => $usuario->name,
        'email' => $usuario->email,
        'rol' => $usuario->rol,
        'free_prompts' => $usuario->free_prompts
    ];

    return response()->json($datos);
}
}
