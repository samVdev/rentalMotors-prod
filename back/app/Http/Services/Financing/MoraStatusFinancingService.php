<?php

namespace App\Http\Services\Financing;

use App\Models\Financing;
use Illuminate\Http\JsonResponse;

class MoraStatusFinancingService
{
    public static function execute(string $id): JsonResponse
    {
        try {
            $financing = Financing::where('id', $id)->first();
            if (!$financing) return response()->json(['message' => 'Financiamiento no encontrado'], 404);
            $financing->moraStatus = !$financing->moraStatus;
            $financing->save();

            return response()->json([
                'message' => 'Mora actualizada correctamente',
                'status' => $financing->moraStatus
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al editar el financiamiento',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
