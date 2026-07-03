<?php

namespace App\Http\Services\Maintenance;

use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Http\Services\Utils\WhatsAppService;
use App\Models\Maintenance;
use App\Models\Financing;
use App\Models\Application;
use Illuminate\Http\JsonResponse;

class MaintenanceStoreService
{
    public static function execute(StoreMaintenanceRequest $request): JsonResponse
    {
        try {
            $financing_id = null;
            $application_id = null;
            $relatedModel = null;

            if ($request->type == 1) {
                $financing_id = $request->id_for_mant;
                $relatedModel = Financing::with('user.persona')->find($financing_id);
            } elseif ($request->type == 2) {
                $application_id = $request->id_for_mant;
                $relatedModel = Application::with('user.persona')->find($application_id);
            }

            Maintenance::create([
                'financing_id'   => $financing_id,
                'application_id' => $application_id,
                'total'          => 0,
                'responsable_id' => $request->persona_id,
                'status'         => $request->status ?? 'pending',
                'descripcion'    => $request->descripcion,
                'date'           => $request->fecha,
                'type'           => $request->type,
            ]);

            if ($relatedModel && $relatedModel->user && $relatedModel->user->persona) {
                $phone = $relatedModel->user->persona->phone;

                if ($phone) {
                    $wa = app(WhatsAppService::class);
                    $wa->sendTemplate(
                        $phone,
                        "new_maintenance",
                        [],
                        'es_CO'
                    );
                }
            }

            return response()->json([
                'message' => 'Mantenimiento creado correctamente',
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el mantenimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}