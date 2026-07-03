<?php

namespace App\Http\Services\Financing;

use App\Models\Financing;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Financing\UpdateFinanceDetailsRequest;

class UpdateFinanceDetailsService
{
    public static function execute(UpdateFinanceDetailsRequest $request, string $id): JsonResponse
    {
        try {
            $financing = Financing::findOrFail($id);

            $fields = [
                'interes_porcent',
                'financing_price',
                'interes_price',
                'price_diario',
                'price_semanal',
                'price_quincenal',
                'price_mensual',
                'months',
                'installments',
                'plan'
            ];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $financing->{$field} = $request->{$field};
                }
            }

            // Manejar cambio de status con lógica especial para 'finished'
            if ($request->has('status')) {
                $oldStatus = $financing->status;
                $newStatus = $request->status;

                if ($newStatus === 'finished' && $oldStatus !== 'finished') {
                    $financing->status = 'finished';
                    if (strpos($financing->code, '(F)') === false) {
                        $financing->code = $financing->code . ' ' . date('m/Y') . ' (F)';
                    }
                } else {
                    $financing->status = $newStatus;
                    $financing->code = preg_replace('/\s\d{2}\/\d{4}.*$/', '', $financing->code);
                }
            }

            if ($request->has('financing_price') || $request->has('interes_price')) {
                $financing_price = $financing->financing_price ?? 0;
                $interes_price = $financing->interes_price ?? 0;

                if ($request->has('final_price')) {
                    $financing->final_price = $request->final_price;
                }
            }

            if ($request->has('final_price')) {
                $financing->final_price = $request->final_price;
            }

            $financing->save();

            // Sincronizar servicios si vienen en la petición
            if ($request->has('services')) {
                $syncData = [];
                foreach ($request->services as $service) {
                    if ($service['is_included']) {
                        $syncData[$service['id']] = ['price' => $service['price'] ?? 0];
                    }
                }
                $financing->services()->sync($syncData);
            }

            // Recalcular final_price incluyendo servicios adicionales
            $servicesTotal = $financing->services()->sum('financing_service.price');
            $financing->final_price = ($financing->financing_price ?? 0) + ($financing->interes_price ?? 0) + $servicesTotal;
            $financing->save();

            return response()->json([
                'message' => 'Detalles financieros actualizados correctamente.',
                'financing' => $financing->load('services') // Cargamos los servicios actualizados
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar los detalles financieros.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
