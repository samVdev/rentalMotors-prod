<?php

namespace App\Http\Requests\Client;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array(auth()->user()->role->id, [1, 2]);
    }

    public function messages()
    {
        return [
            "name.required" => "El nombre es obligatorio.",
            "name.max" => "El nombre no debe exceder los 255 caracteres.",

            "usuario.required" => "El nombre de usuario es obligatorio.",
            "usuario.email" => "El nombre de usuario debe ser válido.",
            "usuario.max" => "El nombre de usuario no debe exceder los 255 caracteres.",
            "usuario.unique" => "Este nombre de usuario ya está registrado.",
            
            "email.email" => "El correo electrónico debe ser válido.",
            "email.max" => "El correo electrónico no debe exceder los 255 caracteres.",
            "email.unique" => "Este correo electrónico ya está registrado.",
            
            "phone.required" => "El número de teléfono es obligatorio.",
            'phone.regex' => 'El número de teléfono no es válido. Por favor ingrese un número entre 8 y 15 dígitos.',

            "cedula.required" => "La cédula es obligatoria.",
            "cedula.unique" => "Esta cédula ya está registrada.",
            "cedula.regex" => "La cédula debe contener solo números y entre 6 y 12 dígitos.",

            "dateN.required" => "La fecha de nacimiento es obligatoria.",
            "dateN.date" => "La fecha de nacimiento no es válida.",
            "dateN.before" => "La fecha de nacimiento debe ser anterior a hoy.",

            "earnings.regex" => "El ingreso mensual debe contener solo números.",

            "dir.max" => "La dirección no debe exceder los 500 caracteres.",
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {    
        return [
            'name' => ['required', 'string', 'max:255'],

            "usuario" => [
                "required", "max:55", "string",
                Rule::unique('users', 'username')
                ->whereNull('deleted_at')
                ->ignore(
                    User::where('id', $this->route('id'))->first()->id, // Ignora el usuario con el id correspondiente
                    'id'
                )
            ],
            "email" => [
                "nullable", "max:255", "email",
                Rule::unique('users')
                ->whereNull('deleted_at')
                ->ignore(
                    User::where('id', $this->route('id'))->first()->id, // Ignora el usuario con el id correspondiente
                    'id'
                )
            ],
            'phone' => ['required',  'regex:/^\+?[0-9][0-9]{7,14}$/'],
            'suspend' => ['nullable', 'boolean'],
            
            'cedula' => [
                'required',
                'string',
                'regex:/^[0-9]{6,12}$/',
                Rule::unique('personas', 'cedula')->ignore(
                    optional(User::where('id', $this->route('id'))->first()?->persona)->id,
                    'id'
                )
            ],
            'dateN'    => ['required', 'date', 'before:today'],

            'earnings'    => ['nullable', 'regex:/^[0-9]+$/'],
            'dir'      => ['nullable', 'string', 'max:500'],
        ];
    }
}
