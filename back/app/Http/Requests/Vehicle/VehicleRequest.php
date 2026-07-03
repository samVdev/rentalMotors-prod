<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [1, 2]);
    }

    public function rules(): array
    {
        return [
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'image' => 'nullable|file|image|max:2048',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'cc' => 'required|string|max:10',
            'color' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'kilometraje' => 'required|integer|min:0',
            'type' => 'required|in:bike,car',
        ];
    }

    public function messages(): array
    {
        return [
            'marca.required' => 'La marca es obligatoria.',
            'marca.string' => 'La marca debe ser texto.',
            'marca.max' => 'La marca no puede superar los 255 caracteres.',

            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.string' => 'El modelo debe ser texto.',
            'modelo.max' => 'El modelo no puede superar los 255 caracteres.',

            'image.file' => 'La imagen debe ser un archivo.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.max' => 'La imagen no debe superar los 20MB.',

            'year.required' => 'El año es obligatorio.',
            'year.digits' => 'El año debe tener 4 dígitos.',
            'year.integer' => 'El año debe ser un número.',
            'year.min' => 'El año debe ser mayor o igual a 1900.',
            'year.max' => 'El año no puede ser mayor al actual.',

            'cc.required' => 'El cilindraje es obligatorio.',
            'cc.string' => 'El cilindraje debe ser texto.',
            'cc.max' => 'El cilindraje no puede superar los 10 caracteres.',

            'color.required' => 'El color es obligatorio.',
            'color.string' => 'El color debe ser texto.',
            'color.max' => 'El color no puede superar los 100 caracteres.',

            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',

            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.integer' => 'El kilometraje debe ser un número entero.',
            'kilometraje.min' => 'El kilometraje no puede ser negativo.',

            'type.required' => 'El tipo de vehículo es obligatorio.',
            'type.in' => 'El tipo debe ser "bike" o "car".',
        ];
    }
}