<?php

namespace App\Http\Services\Financing;

use App\Http\Requests\Financing\FinancingFormEditRequest;
use App\Models\Financing;
use Illuminate\Http\JsonResponse;

class EditFinancingService
{
    public static function execute(FinancingFormEditRequest $request, string $id): JsonResponse
    {
        try {
            $financing = Financing::where('id', $id)->first();

            if (!$financing)
                return response()->json(['message' => 'Financiamiento no encontrado'], 404);


            $financing->observation = $request->observacion;
            $financing->lote_id = $request->lote_name;

            $financing->save();

            return response()->json([
                'message' => 'Financiamiento editado correctamente',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al editar el financiamiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
