<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [1, 2]);;
    }
    public function rules(): array
    {
        return [
            'type'           => 'required|in:1,2',
            'persona_id'     => 'required|exists:users,id',
            'descripcion'    => 'required|string|min:5|max:1000',
            'fecha'          => 'required|date',
            'status'         => 'nullable|string|in:pending,completed,cancelled',
            
            'id_for_mant'    => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $tabla = ($this->type == 1) ? 'financings' : 'applications';
                    $existe = \DB::table($tabla)->where('id', $value)->exists();

                    if (!$existe) {
                        $fail("El ID de referencia no existe en la tabla de " . ($this->type == 1 ? 'financiamientos' : 'solicitudes') . ".");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'         => 'El tipo de mantenimiento es obligatorio.',
            'type.in'               => 'El tipo seleccionado no es válido.',
            'persona_id.required'   => 'Debe asignar un responsable.',
            'persona_id.exists'     => 'El responsable seleccionado no existe en nuestros registros.',
            'descripcion.required'  => 'La descripción es obligatoria.',
            'descripcion.min'       => 'La descripción debe tener al menos 5 caracteres.',
            'fecha.required'        => 'La fecha es obligatoria.',
            'fecha.date'            => 'El formato de fecha no es válido.',
            'status.in'             => 'El estado seleccionado no es válido.',
            'id_for_mant.required'  => 'El vehiculo para el mantenimiento es obligatorio.',
            'id_for_mant.integer'   => 'El vehiculo para el mantenimiento no se encuentra.',
        ];
    }
}