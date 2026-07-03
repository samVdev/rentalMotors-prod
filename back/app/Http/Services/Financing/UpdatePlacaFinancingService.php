<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Financing;

class UpdatePlacaFinancingService
{
    static public function execute(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'plate' => 'required|string|max:50',
            ]);

            if ($financing && $financing->status != 'active') {
                return response()->json([
                    'message' => 'La placa no se puede actualizar porque el financiamiento no está activo',
                ], 422);
            }

            $financing->update([
                'plate' => $request->plate,
            ]);

            return response()->json([
                'message' => 'Placa actualizada correctamente',
                'plate' => $financing->plate,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la placa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
