<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class FormDocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role->id, [1, 2]);
    }

    public function rules(): array
    {
        return [
            'plan' => 'required|in:Diario,Semanal,Quincenal,Mensual',
            'meses' => 'required|numeric|min:1',
            'interes' => 'required|numeric|min:0',
            'inicial' => 'required|string|min:1',
            'upload-comprobante' => 'required|image|mimes:jpg,png|max:4096',
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
            'meses.required' => 'La cantidad de meses es obligatoria.',
            'meses.numeric' => 'La cantidad de meses debe ser un número.',
            'meses.min' => 'La cantidad de meses debe ser mayor que cero.',

            'interes.required' => 'La tasa de interes es obligatoria.',
            'interes.numeric' => 'La tasa de interes debe ser un número.',
            'interes.min' => 'La tasa de interes debe ser mayor o igual que cero.',

            'inicial.required' => 'El inicial del pago es obligatorio.',
            'inicial.string' => 'El inicial del pago debe ser un texto.',
            'inicial.min' => 'El inicial del pago debe ser mayor que cero.',

            'upload-comprobante.required' => 'Debes subir el comprobante de pago.',
            'upload-comprobante.image' => 'El archivo debe ser una imagen válida.',
            'upload-comprobante.mimes' => 'Formato no permitido. Solo se aceptan JPG o PNG.',
            'upload-comprobante.max' => 'El tamaño máximo permitido es de 4 MB.',

            "lote_id.required" => "El lote es obligatorio.",
            "lote_id.exists" => "El lote seleccionado no es válido o no existe en el sistema.",

            "code.required" => "El código es obligatorio.",
            "code.string" => "El código debe ser una cadena de texto.",
            "code.max" => "El código no debe exceder los 50 caracteres.",
        ];
    }
}