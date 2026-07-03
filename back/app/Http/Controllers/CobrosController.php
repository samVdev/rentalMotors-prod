<?php

namespace App\Http\Controllers;

use App\Http\Services\Statics\CobrosStaticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\Utils\WhatsAppService;

class CobrosController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        try {
            $summary = CobrosStaticsService::getSummary($request);
            return response()->json($summary);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener el resumen de cobros',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pending(Request $request): JsonResponse
    {
        try {
            $pending = CobrosStaticsService::getPendingPayments($request);
            return response()->json($pending);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener los pagos pendientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleGPS(Request $request, $id): JsonResponse
    {
        try {
            $status = $request->input('status');
            // Logic to interact with GPS provider would go here
            return response()->json([
                'success' => true,
                'message' => 'GPS ' . ($status ? 'activado' : 'desactivado') . ' correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al cambiar estado del GPS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleMoto(Request $request, $id): JsonResponse
    {
        try {
            $status = $request->input('status'); // true = ON, false = OFF
            $financing = \App\Models\Financing::findOrFail($id);
            
            if ($status) {
                $financing->turned_off_at = null;
            } else {
                $financing->turned_off_at = now();
            }
            
            $financing->save();

            return response()->json([
                'success' => true,
                'message' => 'Moto ' . ($status ? 'encendida' : 'apagada') . ' correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al cambiar estado de la moto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function notifyWhatsApp(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $type = $request->input('type'); // 'warning' | 'success'
            
            $financing = \App\Models\Financing::with('user.persona')->findOrFail($id);
            $clientName = $financing->user?->persona?->fullName ?? 'Cliente';
            $phone = $financing->user?->persona?->phone;
            
            if (!$phone) {
                return response()->json(['message' => 'El cliente no tiene un teléfono registrado'], 422);
            }

            $typeMSG = '';
            if ($type === 'warning') {
                $typeMSG = "new_warning";
            } else if ($type === 'success') {
                $typeMSG = "new_congratulations";

            } else {
                return response()->json(['message' => 'Tipo de notificación no válido'], 422);
            }

               $wa = app(WhatsAppService::class);
                    $wa->sendTemplate(
                        $phone,
                        $typeMSG,
                        [$clientName],
                        'es_CO'
                    );

            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al enviar notificación de WhatsApp',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completed(Request $request): JsonResponse
    {
        try {
            $completed = CobrosStaticsService::getCompletedPayments($request);
            return response()->json($completed);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener los pagos completados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeMora(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'financing_id' => 'required|exists:financings,id'
            ]);

            $financing = \App\Models\Financing::findOrFail($request->financing_id);
            $installmentValue = $financing->getCurrentInstallmentPrice();

            \App\Http\Services\Financing\MoraCalculationService::createMoraRecord($financing->id, $installmentValue);

            return response()->json([
                'success' => true,
                'message' => 'Mora generada correctamente para ' . ($financing->user?->persona?->fullName ?? 'el financiamiento'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar la mora',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}