<?php

namespace App\Http\Services\Statics;

use Illuminate\Http\JsonResponse;
use App\Models\Financing;

class IndexFinancingDashService
{
    static public function execute(): JsonResponse
    {
        try {

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $financings = Financing::query()
                ->select('id', 'user_id', 'vehicle_id', 'installments', 'created_at', 'type', 'final_price')
                ->with([
                    'user.persona:id,fullName',
                    'payments:id,financing_id,status'
                ])
                ->whereIn('lote_id', $lotesIds)
                ->where('financings.status', 'active')
                ->whereNotNull('user_id')
                ->get();

            $mappedData = $financings->map(function ($f) {
                $total = $f->installments ?? 0;
                $approvedCount = $f->payments->where('status', 'approved')->count();
                
                $restantes = $total - $approvedCount;
                $progreso = $total > 0 ? round(($approvedCount / $total) * 100) : 0;

                return [
                    'monto' => '$' . number_format($f->final_price ?? 0, 0, ',', '.'),
                    'progreso' => $progreso,
                    'actual' => $approvedCount,
                    'total' => $total,
                    'restantes' => $restantes,
                    'cliente' => $f->user?->persona?->fullName ?? 'N/A',
                    'fecha_inicio' => optional($f->created_at)->format('d/m/Y') ?? 'Sin data',
                    'tipo' => $f->type ?? 'Sin data',
                ];
            });

            $finishing = $mappedData
                ->filter(fn($item) => $item['restantes'] < 4 && $item['restantes'] > 0)
                ->sortBy('restantes') 
                ->take(5)
                ->values();

            return response()->json([
                'actives' => $mappedData,
                'finishing' => $finishing
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al listar los financiamientos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}