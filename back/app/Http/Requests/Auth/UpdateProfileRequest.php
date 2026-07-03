<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userId = $this->route('id');
        $user = User::find($userId); 
    
        if (!auth()->check()) {
            abort(403, 'No tienes permiso para realizar esta acción. Inicia sesión.');
        }
    
        if (!$user) {
            abort(404, 'Usuario no encontrado.');
        }
    
        if (auth()->id() !== $user->id) {
            abort(403, 'No tienes permiso para modificar este usuario.');
        }

        return true;
    }
    
    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tel'   => 'required|string|digits_between:7,15',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'name.string'    => 'El nombre debe ser una cadena de texto.',
            'name.max'       => 'El nombre no puede exceder los 255 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo electrónico debe ser válido.',
            'email.max'      => 'El correo electrónico no puede exceder los 255 caracteres.',

            'tel.required'   => 'El número de teléfono es obligatorio.',
            'tel.string'     => 'El número de teléfono debe ser una cadena de texto.',
            'tel.digits_between' => 'El número de teléfono debe tener entre 7 y 15 dígitos.',
        ];
    }
}
