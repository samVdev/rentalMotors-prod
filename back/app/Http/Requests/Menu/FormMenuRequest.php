<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class FormMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize(): bool
    {
        return in_array(auth()->user()->role->id, [1, 2]);
    }

    public function messages()
    {
        return [
            'title.required' => 'El nombre del menu es obligatorio.',
            'title.string' => 'El nombre del menu debe ser un texto.',
            'title.max' => 'El nombre del menu no puede tener más de 255 caracteres.',

            'path.required' => 'El nombre de la url es obligatorio.',
            'path.string' => 'El nombre de la url  debe ser un texto.',
            'path.max' => 'El nombre de la url  no puede tener más de 255 caracteres.',

            'icon.required' => 'El nombre del icono es obligatorio.',
            'icon.string' => 'El nombre del icono debe ser un texto.',
            'icon.max' => 'El nombre del icono no puede tener más de 255 caracteres.',

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
            'title' =>['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:255'],
        ];
    }
}
