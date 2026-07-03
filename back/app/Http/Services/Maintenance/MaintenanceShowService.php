<?php

namespace App\Http\Services\Maintenance;

use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;

class MaintenanceShowService
{
    public static function execute(int $id): JsonResponse
    {
        try {
            $maintenance = Maintenance::find($id);

            if (!$maintenance) {
                return response()->json([
                    'message' => 'Mantenimiento no encontrado'
                ], 404);
            }

            $id_for_mant = 0;

            if ($maintenance->type == 1) {
                $id_for_mant = $maintenance->financing_id;
            } elseif ($maintenance->type == 2) {
                $id_for_mant = $maintenance->application_id;
            }

            return response()->json([
                "id"=> $maintenance->id,
                "type"=> $maintenance->type == '1' ? 'financed' : 'cash',
                "id_for_mant"=> $id_for_mant,
                "total"=> $maintenance->total,
                "fecha" => $maintenance->date,
                "descripcion"=> $maintenance->descripcion,
                "persona_id"=> $maintenance->responsable_id,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener el mantenimiento'
            ], 500);
        }
    }
}
