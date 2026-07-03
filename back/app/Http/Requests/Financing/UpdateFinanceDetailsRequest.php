<?php

namespace App\Http\Requests\Financing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinanceDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [1, 2]);
    }

    public function rules(): array
    {
        return [
            'interes_porcent' => 'nullable|numeric|min:0',
            'financing_price' => 'nullable|numeric|min:0',
            'interes_price' => 'nullable|numeric|min:0',
            'price_diario' => 'nullable|numeric|min:0',
            'price_semanal' => 'nullable|numeric|min:0',
            'price_quincenal' => 'nullable|numeric|min:0',
            'price_mensual' => 'nullable|numeric|min:0',
            'months' => 'nullable|integer|min:1',
            'installments' => 'nullable|integer|min:1',
            'plan' => 'nullable|string|in:Diario,Semanal,Quincenal,Mensual',
            'status' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*.id' => 'required_with:services|integer|exists:services,id',
            'services.*.is_included' => 'required_with:services|boolean',
            'services.*.price' => 'nullable|numeric|min:0',
        ];
    }
}
