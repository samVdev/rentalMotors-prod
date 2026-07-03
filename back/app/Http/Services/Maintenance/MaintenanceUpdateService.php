<?php

namespace App\Http\Services\Maintenance;

use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceUpdateService
{
    public static function execute(StoreMaintenanceRequest $request, int $id): JsonResponse
    {
        try {
            $maintenance = Maintenance::find($id);

            if (!$maintenance) {
                return response()->json([
                    'message' => 'El mantenimiento que intenta editar no existe.'
                ], 404);
            }

            $financing_id = null;
            $application_id = null;

            if ($request->type == 1) {
                $financing_id = $request->id_for_mant;
            } elseif ($request->type == 2) {
                $application_id = $request->id_for_mant;
            }

            $maintenance->update([
                'financing_id'   => $financing_id,
                'application_id' => $application_id,
                'responsable_id' => $request->persona_id,
                'total'          => 0,
                'descripcion'    => $request->descripcion,
                'date'           => $request->fecha,
                'type'           => $request->type,
            ]);

            return response()->json([
                'message' => 'Mantenimiento actualizado correctamente',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el mantenimiento'
            ], 500);
        }
    }

    public static function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $maintenance = Maintenance::find($id);

            if (!$maintenance) {
                return response()->json([
                    'message' => 'El mantenimiento que intenta actualizar no existe.'
                ], 404);
            }

            $maintenance->status = $maintenance->status == "checking" ? "completed" : "checking";
            $maintenance->total = $request->has('price') ? $request->price : 0;
            $maintenance->save();

            return response()->json([
                'message' => 'Estado del mantenimiento actualizado correctamente',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estado del mantenimiento'
            ], 500);
        }
    }
}