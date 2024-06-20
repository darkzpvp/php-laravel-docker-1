<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformacionPersonal;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InformacionPersonalRequest;

class InformacionPersonalController extends Controller
{
    /**
     * Almacena la información personal en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InformacionPersonalRequest $request)
    {
        // El código de validación y almacenamiento se mueve automáticamente a la clase de solicitud
        // Accede a los datos validados usando el método validated()
    
        // Buscar si ya existe información personal para el usuario actual
        $informacionPersonal = InformacionPersonal::where('user_id', Auth::id())->first();
    
        if ($informacionPersonal) {
            // Si la información personal ya existe, actualizar los datos
            $informacionPersonal->update($request->validated());
        } else {
            // Si la información personal no existe, crear un nuevo registro
            $informacionPersonal = new InformacionPersonal($request->validated() + ['user_id' => Auth::id()]);
            $informacionPersonal->save();
        }
    
        // Devolver una respuesta con un código de estado 201 (Created) y los datos almacenados
        return response()->json(['message' => 'Información personal almacenada correctamente', 'data' => $informacionPersonal], 201);
    }
    public function show(Request $request)
{
    // Obtener la información personal del usuario actual
    $informacionPersonal = InformacionPersonal::where('user_id', Auth::id())->first();

    // Verificar si se encontró la información personal
    if (!$informacionPersonal) {
        return response()->json(['message' => 'No se encontró información personal para este usuario'], 404);
    }

    // Devolver una respuesta con los datos de la información personal
    return response()->json(['data' => $informacionPersonal], 200);
}
}
