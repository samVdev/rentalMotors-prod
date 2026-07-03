<?php

namespace App\Http\Requests\guest;

use Illuminate\Foundation\Http\FormRequest;

class FormPayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role->id, [3]);
    }

    public function rules(): array
    {
        return [
            'total' => 'required|numeric|min:1',
            'upload-comprobante' => 'required|image|mimes:jpg,png|max:4096',
            'account_method_id' => 'required|exists:account_methods,id',
        ];
    }

    public function messages(): array
    {
        return [
            'total.required' => 'El total del pago es obligatorio.',
            'total.numeric' => 'El total del pago debe ser un número.',
            'total.min' => 'El total del pago debe ser mayor que cero.',

            'upload-comprobante.required' => 'Debes subir el comprobante de pago.',
            'upload-comprobante.image' => 'El archivo debe ser una imagen válida.',
            'upload-comprobante.mimes' => 'Formato no permitido. Solo se aceptan JPG o PNG.',
            'upload-comprobante.max' => 'El tamaño máximo permitido es de 4 MB.',

            'account_method_id.required' => 'Debes seleccionar un método de pago válido.',
            'account_method_id.exists' => 'El método de pago seleccionado no es válido o no existe.',
        ];
    }
}