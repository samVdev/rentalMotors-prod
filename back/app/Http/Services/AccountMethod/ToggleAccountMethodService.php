<?php

namespace App\Http\Services\AccountMethod;

use Illuminate\Http\JsonResponse;
use App\Models\AccountMethod;

class ToggleAccountMethodService
{
    static public function execute(AccountMethod $account_method): JsonResponse
    {
        try {
            $account_method->update([
                'is_active' => !$account_method->is_active,
            ]);

            return response()->json([
                'message' => 'Estado de la cuenta actualizado correctamente',
                'is_active' => $account_method->is_active,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
