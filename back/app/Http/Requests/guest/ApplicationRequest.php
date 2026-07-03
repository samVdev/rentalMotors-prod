<?php

namespace App\Http\Requests\guest;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullName' => 'required|string|max:255',
            'vehicleType' => 'required|in:bike,car',
            'selectedVehicleId' => 'required|exists:vehicles,id',
            'telefono' => 'required|digits_between:8,20',

            'identity' => 'required|string|max:30',
            'type' => 'required|in:contado,financiacion',
            'cuotas' => 'required_if:type,financiacion|in:Diario,Semanal,Quincenal,Mensual|nullable',
            'tipoDocumento' => 'required|in:C,P,E',
            'direccion' => 'required|string|max:100',

            // Documentos
            'documents.cedula' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.carnet' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.cedulaAmpliada' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.recibo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.refFamiliares' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.refPersonales' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'documents.pep' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [

            'fullName.required' => 'El nombre completo es obligatorio.',
            'fullName.string' => 'El nombre completo debe ser una cadena de texto.',
            'fullName.max' => 'El nombre completo no debe superar los 255 caracteres.',

            'vehicleType.required' => 'El tipo de vehículo es obligatorio.',
            'vehicleType.in' => 'El tipo de vehículo debe ser valido.',

            'selectedVehicleId.required' => 'Debe seleccionar un vehículo.',
            'selectedVehicleId.exists' => 'El vehículo seleccionado no existe.',

            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.digits_between' => 'El número de teléfono debe tener entre 8 y 20 dígitos.',


            'identity.required' => 'El número de documento es obligatorio.',
            'identity.max' => 'El número de documento no debe superar los 30 caracteres.',

            'type.required' => 'Debe seleccionar una forma de pago.',
            'type.in' => 'La forma de pago no es válida.',

            'cuotas.required_if' => 'Debe seleccionar un plan de financiamiento si el tipo de pago es financiación.',
            'cuotas.in' => 'El plan de financiamiento no es válido.',

            'tipoDocumento.required' => 'Debe seleccionar un tipo de documento.',
            'tipoDocumento.in' => 'El tipo de documento no es válido.',

            'direccion.in' => 'La dirección no es válida.',
            'direccion.max' => 'La dirección no debe superar los 30 caracteres.',
            'direccion.required' => 'La dirección es obligatoria.',

            'documents.cedula.required' => 'La cédula de ciudadanía es obligatoria.',
            'documents.cedula.file' => 'Debe subir un archivo válido para la cédula.',
            'documents.cedula.mimes' => 'La cédula debe ser un archivo JPG, PNG o PDF.',
            'documents.cedula.max' => 'La cédula no debe superar los 20MB.',

            'documents.carnet.mimes' => 'La foto tipo carnet debe ser un archivo JPG, PNG o PDF.',
            'documents.carnet.max' => 'La foto tipo carnet no debe superar los 20MB.',

            'documents.cedulaAmpliada.mimes' => 'La cédula ampliada debe ser JPG, PNG o PDF.',
            'documents.cedulaAmpliada.max' => 'La cédula ampliada no debe superar los 20MB.',

            'documents.recibo.mimes' => 'El recibo debe ser un archivo JPG, PNG o PDF.',
            'documents.recibo.max' => 'El recibo no debe superar los 20MB.',

            'documents.refFamiliares.mimes' => 'Las referencias familiares deben ser JPG, PNG o PDF.',
            'documents.refFamiliares.max' => 'Las referencias familiares no deben superar los 20MB.',

            'documents.refPersonales.mimes' => 'Las referencias personales deben ser JPG, PNG o PDF.',
            'documents.refPersonales.max' => 'Las referencias personales no deben superar los 20MB.',

            'documents.pep.mimes' => 'El documento PEP debe ser JPG, PNG o PDF.',
            'documents.pep.max' => 'El documento PEP no debe superar los 20MB.',
        ];
    }
}