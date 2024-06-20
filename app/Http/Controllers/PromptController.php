<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripciones;
use App\Models\Prompt;
class PromptController extends Controller
{
    public function enviarFormulario(Request $request)
    {
        // Obtener el usuario autenticado usando Laravel Sanctum
        $user = auth()->user();
    
        // Verificar si el usuario existe
        if (!$user) {
            return response([
                'errors' => ['Usuario no autenticado']
            ], 401);
        }
    
        // Obtener el texto del formulario
        $texto = $request->input('texto');
    
        // Verificar si el usuario tiene prompts disponibles en su suscripción
        if ($user->free_prompts > 0) {
            // Guardar el prompt
            Prompt::create([
                'user_id' => $user->id,
                'texto' => $texto
            ]);
    
            // Decrementar el número de free_prompts
            $user->free_prompts -= 1;
            $user->save();
        } else {
            // Verificar si el usuario tiene una suscripción activa
            $suscripcion = Suscripciones::where('user_id', $user->id)->first(); 
    
            if ($suscripcion && $suscripcion->prompts_disponibles > 0) {
                // Guardar el prompt
                Prompt::create([
                    'user_id' => $user->id,
                    'texto' => $texto
                ]);
    
                // Decrementar el número de prompts disponibles en la suscripción
                $suscripcion->prompts_disponibles -= 1;
                $suscripcion->save();
            } else {
                // Devolver un error si no hay prompts disponibles en la suscripción
                return response([
                    'errors' => 'Suscríbete para seguir lanzando prompts.'
                ], 422);
            }
        }
    
        // Aquí puedes devolver una respuesta de éxito si lo necesitas
        return response(['message' => 'Prompt enviado con éxito'], 200);
    }
    

    public function conseguirPrompts(Request $request)
    {
        // Obtener el usuario autenticado usando Laravel Sanctum
        $user = auth()->user();
    
        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['errors' => ['Usuario no autenticado']], 401);
        }
    
        // Buscar las suscripciones del usuario
        $suscripciones = Suscripciones::where('user_id', $user->id)->first();
    
        // Inicializar la variable para almacenar la cantidad total de prompts
        $totalPrompts = $user->free_prompts;
    
        // Si el usuario tiene suscripciones, sumar los prompts disponibles
        if ($suscripciones) {
            $totalPrompts += $suscripciones->prompts_disponibles;
        }
    
        // Verificar si hay prompts disponibles
        if ($totalPrompts <= 0) {
            return response()->json(['errors' => '¡No tienes prompts disponibles!'], 400);
        }
    
        // Devolver la respuesta JSON con la cantidad total de prompts
        return response()->json(['prompts' => $totalPrompts]);
    }


        public function todosLosPrompts(Request $request)
        {
            // Obtener el usuario autenticado usando Laravel Sanctum
            $user = auth()->user();
            
            // Verificar si el usuario existe
            if (!$user) {
                return response([
                    'errors' => ['Usuario no autenticado']
                ], 401);
            }
    
            // Obtener todos los prompts del usuario autenticado
            $prompts = Prompt::where('user_id', $user->id)->get();
    
            // Devolver la lista de prompts
            return response()->json($prompts);
        }
    }
