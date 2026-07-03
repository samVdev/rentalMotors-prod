<?php

namespace App\Http\Services\Maintenance;

use App\Models\Application;
use App\Models\Financing;
use Illuminate\Http\JsonResponse;

# VERIFICAR BIEN

class MaintenanceCheckTypeService
{
    /**
     * @param int    $type   1 = Financiado | 2 = Contado
     * @param string $cedula
     */
    public static function execute(int $type, string $cedula): JsonResponse
    {

        if (!in_array($type, [1, 2], true)) {
            return response()->json([
                'message' => 'Tipo de mantenimiento no válido'
            ], 422);
        }

        $type_chart = $type == 1 ? 'financing' : 'cash';

        if ($type === 1) {
            // Buscando en financiamiento

            $financings = Financing::query()
                ->select(['id', 'code', 'application_id', 'vehicle_id'])
                ->whereHas('application', function ($q) use ($cedula, $type_chart) {
                    $q->where('cedula', 'ilike', "%{$cedula}%")
                        ->where('type', $type_chart);
                })
                ->whereIn('status', ['active', 'finished'])
                ->with([
                    'user.persona:id,fullName',
                    'vehicle:id,image,brand'
                ])
                ->get();

            if ($financings->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron financiaciones para este cliente'
                ], 404);
            }

            return response()->json([
                'data' => $financings->map(fn($f) => [
                    'id'            => $f->id,
                    'img'  => $f->vehicle?->image,
                    'vehicle_label' => $f->vehicle?->brand,
                    'codigo'        => $f->code
                ])
            ]);
        } else if ($type === 2) {

            // Buscando en application (contado)

            $applications = Application::query()
                ->select(['id', 'user_id', 'vehicle_id'])
                ->where('cedula', 'ilike', "%{$cedula}%")
                ->where('type', $type_chart)
                ->with([
                    'user:id',
                    'user.persona:id,fullName',
                    'vehicle:id,image,brand'
                ])
                ->get();

            if ($applications->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontro información para este cliente'
                ], 404);
            }

            return response()->json([
                'data' => $applications->map(fn($a) => [
                    'id'            => $a->id,
                    'cliente'       => $a->user?->persona->fullName ?? 'Sin nombre',
                    'vehicle_label' => $a->vehicle?->brand,
                    'img' => $a->vehicle?->image
                ])
            ]);
        }

        return response()->json([
            'message' => 'Tipo de mantenimiento no válido'
        ], 422);
    }
}
