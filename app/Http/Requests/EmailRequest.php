<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email'],
            'message' => ['required', 'string', 'min:10'], 
        ];
        
    }
    public function messages(){
        return[
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos :min caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'message.required' => 'El mensaje es obligatorio',
            'message.min' => 'El mensaje debe tener al menos :min caracteres',
            'email.min' => 'El email debe tener al menos :min caracteres'
        ];
    }
}
