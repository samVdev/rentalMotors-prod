<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            
            "email.required" => "El correo electrónico es obligatorio.",
            "email.email" => "El correo electrónico debe ser válido.",
            "email.max" => "El correo electrónico no debe exceder los 255 caracteres.",
            "email.unique" => "Este correo electrónico ya está registrado.",
            
            "password.required" => "La contraseña es obligatoria.",
            
            "role_id.required" => "El rol es obligatorio.",
            "role_id.exists" => "El rol seleccionado no es válido.",
            
            "phone.required" => "El número de teléfono es obligatorio.",
            'phone.regex' => 'El número de teléfono no es válido. Por favor ingrese un número entre 8 y 15 dígitos.',


            "cedula.required" => "La cédula es obligatoria.",
            "cedula.unique" => "Esta cédula ya está registrada.",
            "cedula.regex" => "La cédula debe contener solo números y entre 6 y 12 dígitos.",

            "dateN.required" => "La fecha de nacimiento es obligatoria.",
            "dateN.date" => "La fecha de nacimiento no es válida.",
            "dateN.before" => "La fecha de nacimiento debe ser anterior a hoy.",

            "dir.max" => "La dirección no debe exceder los 500 caracteres.",

            "usuario.required" => "El nombre de usuario es obligatorio.",
            "usuario.max" => "El nombre de usuario no debe exceder los 255 caracteres.",


            "lotes.required" => "Debe seleccionar al menos un lote.",
            "lotes.array"    => "El formato de los lotes no es válido.",
            "lotes.*.exists" => "Uno de los lotes seleccionados no existe.",
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
            "email" => [
                "required", "max:255", "email",
                Rule::unique('users')
                ->whereNull('deleted_at') // Si usas soft deletes
                ->ignore(
                    User::where('uuid', $this->route('uuid'))->first()->id, // Ignora el usuario con el uuid correspondiente
                    'id'
                )
            ],
            "password" => ["nullable"],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'phone' => ['required',  'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'suspend' => ['nullable', 'boolean'],
            
            'cedula' => [
                'required',
                'string',
                'regex:/^[0-9]{6,12}$/',
                Rule::unique('personas', 'cedula')->ignore(
                    optional(User::where('uuid', $this->route('uuid'))->first()?->persona)->id,
                    'id'
                )
            ],
            'dateN'    => ['required', 'date', 'before:today'],
            'dir'      => ['nullable', 'string', 'max:500'],

            'usuario'     => ['required', 'string', 'max:255'],

            'lotes'    => ['required', 'array', 'min:1'],
            'lotes.*'  => ['integer', 'exists:lotes,id'],
        ];
    }
}
