<?php

namespace App\Http\Services\Maintenance;

use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;

class MaintenanceDeleteService
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

            $maintenance->delete();

            return response()->json([
                'message' => 'Mantenimiento eliminado correctamente'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el mantenimiento'
            ], 500);
        }
    }
}
