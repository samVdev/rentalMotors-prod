<?php

namespace App\Http\Requests\Financing;

use Illuminate\Foundation\Http\FormRequest;

class FinancingFormEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [1, 2]);
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('No tiene acceso autorizado, consulte con un administrador.');
    }

    public function rules(): array
    {
        return [
            'observacion' => 'nullable|string|max:1000',
            'lote_name' => 'required|exists:lotes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'observacion.string' => 'La observación debe ser texto.',
            'observacion.max' => 'La observación no puede superar los 1000 caracteres.',

            "lote_name.required" => "El lote es obligatorio.",
            "lote_name.exists" => "El lote seleccionado no es válido o no existe en el sistema.",
        ];
    }
}
