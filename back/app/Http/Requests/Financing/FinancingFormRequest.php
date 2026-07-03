<?php

namespace App\Http\Requests\Financing;

use Illuminate\Foundation\Http\FormRequest;

class FinancingFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [1, 2]);
    }

    public function rules(): array
    {
        return [
            'plan' => 'required|in:Diario,Semanal,Quincenal,Mensual',
            'upload-comprobante' => 'nullable|file|image|max:2048',
            'vehiculo' => 'required_if:tipo,vehicle|integer|exists:vehicles,id',
            'cliente' => 'required|integer|exists:users,id',
            'tipo' => 'required|in:vehicle,tax,mantenence',
            'meses' => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:1000',

            'inicial' => 'nullable|string|min:0',
            'precio' => 'nullable|numeric|min:0',

            'lote_id' => 'required|exists:lotes,id',
            'code' => 'required|string|max:50',
            'services' => 'nullable|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.price' => 'nullable|numeric|min:0',

        ];
    }

    public function messages(): array
    {
        return [
            'plan.required' => 'El plan de financiación es obligatorio.',
            'plan.in' => 'El plan debe ser uno de: Diario, Semanal, Quincenal, Mensual.',

            'upload-comprobante.file' => 'La imagen debe ser un archivo.',
            'upload-comprobante.image' => 'El archivo debe ser una imagen válida.',
            'upload-comprobante.max' => 'La imagen no debe superar los 20MB.',

            'vehiculo.required_if' => 'Debe seleccionar un vehículo para este tipo de financiación.',
            'vehiculo.integer' => 'El ID del vehículo debe ser un número.',
            'vehiculo.exists' => 'El vehículo seleccionado no existe.',

            'cliente.required' => 'Debe seleccionar un cliente.',
            'cliente.integer' => 'El ID del cliente debe ser un número.',
            'cliente.exists' => 'El cliente seleccionado no existe.',

            'tipo.required' => 'El tipo de financiación es obligatorio.',
            'tipo.in' => 'El tipo debe ser "vehiculo", "Impuesto" o "mantenimiento".',

            'meses.required' => 'El número de meses es obligatorio.',
            'meses.integer' => 'El número de meses debe ser un número entero.',
            'meses.min' => 'Debe haber al menos 1 mes.',

            'observacion.string' => 'La observación debe ser texto.',
            'observacion.max' => 'La observación no puede superar los 1000 caracteres.',

            'inicial.numeric' => 'El pago inicial debe ser un número.',
            'inicial.min' => 'El pago inicial no puede ser negativo.',

            'precio.numeric' => 'El pago inicial debe ser un número.',
            'precio.min' => 'El pago inicial no puede ser negativo.',

            "lote_id.required" => "El lote del lote es obligatorio.",
            "lote_id.exists" => "El lote seleccionado no es válido o no existe en el sistema.",

            "code.required" => "El código es obligatorio.",
            "code.string" => "El código debe ser una cadena de texto.",
            "code.max" => "El código no debe exceder los 50 caracteres.",
        ];
    }
}