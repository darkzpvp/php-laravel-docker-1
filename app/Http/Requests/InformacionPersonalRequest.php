<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InformacionPersonalRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tus reglas de autorización
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'numero_telefono' => 'required|string|max:20',
            'pais' => 'required|string|max:50',
            'poblacion' => 'required|string|max:50',
            'provincia' => 'required|string|max:50',
            'nif_nie' => 'required|string|max:50',
            'direccion' => 'required|string|max:50',
            'cp' => 'required|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Todos los campos son obligatorios',
            '*.string' => 'El campo :attribute debe ser una cadena de texto.',
            '*.max' => 'El campo :attribute no debe superar los :max caracteres.',
        ];
    }
}
