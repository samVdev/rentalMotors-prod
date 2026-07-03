<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use App\Models\Financing;

class getFinancingService
{
    static public function execute(int $id): JsonResponse
    {
        try {
            $financing = Financing::query()
                ->select(
                    'id',
                    'plan',
                    'lote_id',
                    'plate'
                )->where('id', $id)->first();

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            if (!$financing || !in_array($financing->lote_id, $lotesIds)) {

                return response()->json([
                    'message' => 'Financiación no encontrada',
                ], 404);
            }

            return response()->json([
                'id' => $financing->id,
                'plan' => $financing->plan,
                'observacion' => $financing->observation,
                'lote_name' => $financing->lote->id ?? 'N/A',
                'placa' => $financing->plate ?? 'N/A',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener la financiación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
