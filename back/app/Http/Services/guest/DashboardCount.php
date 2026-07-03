<?php

namespace App\Http\Services\guest;

use Illuminate\Http\JsonResponse;
use App\Models\Financing;
use App\Models\Maintenance;
use App\Models\MoraRecord;
use App\Http\Services\Financing\MoraCalculationService;
use Carbon\Carbon;

class DashboardCount
{
    public static function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            $financings = Financing::where('user_id', $user->id)
                ->where('status', 'active')
                ->with([
                    'payments' => function ($q) {
                        $q->orderBy('installment_number');
                    }
                ], 'vehicle')
                ->get();

            if ($financings->isEmpty()) {
                return response()->json([
                    'financings' => [],
                ], 200);
            }

            $resultFinancings = [];
            foreach ($financings as $financing) {
                $data = self::processFinancing($financing);
                if ($data)
                    $resultFinancings[] = $data;
            }

            $resultMaintenances = self::processMaintenances($user->id);
            $resultMoras = self::processMoras($user->id);

            return response()->json([
                'financings' => $resultFinancings,
                'mantenimientos' => $resultMaintenances,
                'moras' => $resultMoras
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los datos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private static function processFinancing($financing)
    {
        $payments = $financing->payments->where('installment_number', '>', 0);

        $approvedPayments = $payments->where('status', 'approved');
        $approvedCount = $approvedPayments->count();
        $totalInstallments = $financing->getTotalInstallmentsCount();

        if (!$financing->hasPendingBalance()) {
             return null;
        }

        $nextInstallmentNumber = $approvedCount + 1;

        $installmentValue = $financing->getInstallmentPrice($nextInstallmentNumber);

        $baseDate = $financing->start_date
            ? Carbon::parse($financing->start_date)
            : Carbon::now();

        $offset = $nextInstallmentNumber - 1;

        $dueDate = $financing->getNextCalendarDueDate();

        $paymentAmount = $installmentValue;
        $moraCalculada = 0;
        $hasPending = $payments->where('status', 'pending')->isNotEmpty();

        $type = match ($financing->type) {
            'tax' => "Impuesto",
            'vehicle' => "Vehiculo",
            'maintenance' => "Mantenimiento",
            default => "Vehiculo",
        };

        $description = match ($type) {
            'Impuesto' => "Impuesto ({$financing->descripcion})",
            'Vehiculo' => "Vehiculo ({$financing->vehicle->brand} {$financing->vehicle->model})",
            'Mantenimiento' => "Mantenimiento ({$financing->descripcion})",
            default => "Vehiculo ({$financing->vehicle->brand} {$financing->vehicle->model})",
        };

        return [
            'financing_id' => $financing->id,
            'fecha_financiacion' => $baseDate->format('d/m/Y'),
            'saldo_pendiente' => $financing->getRemainingBalance(),
            'fecha' => $dueDate->format('d/m/Y'),
            'codigo' => $financing->code,
            'mora_porcentaje' => (float) $financing->mora ?? 0,
            'type' => $type,
            'monto_cuota' => round($installmentValue, 2),
            'monto_mora' => round($moraCalculada, 2),
            'total' => round($paymentAmount, 2),
            'plan' => $financing->plan,
            'cuota_actual' => $nextInstallmentNumber,
            'total_cuotas' => $totalInstallments,
            'total_deuda' => round((float) $financing->final_price ?? 0, 2),
            'total_pagado' => round((float) $approvedPayments->sum('total') ?? 0, 2),
            'pending' => $hasPending,
            'approvedCount' => $approvedCount,
            'descripcion' => "Es una financiacion de un {$description} ",
        ];
    }

    private static function processMaintenances(int $userId): array
    {
        return Maintenance::query()
            ->select(['id', 'descripcion', 'date'])
            ->where('status', 'pending')
            ->where(function ($query) use ($userId) {
                $query->whereHas('financing', fn($q) => $q->where('user_id', $userId))
                    ->orWhereHas('application', fn($q) => $q->where('user_id', $userId));
            })
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'descripcion' => $m->descripcion,
                'fecha' => $m->date ? Carbon::parse($m->date)->format('d/m/Y') : 'Sin fecha',
            ])
            ->toArray();
    }

    private static function processMoras(int $userId): array
    {
        return MoraRecord::whereHas('financing', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('status', 'pending')
            ->with('payments')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'financing_id' => $m->financing_id,
                'codigo' => $m->financing->code,
                'total' => round($m->total_amount, 2),
                'reconnection_fee' => round($m->reconnection_fee, 2),
                'mora_amount' => round($m->total_amount - $m->reconnection_fee, 2),
                'base_amount' => round($m->base_amount, 2),
                'percentage' => $m->percentage,
                'index' => $m->occurrence_index,
                'status' => $m->status,
                'has_pending_payment' => $m->payments->where('status', 'pending')->isNotEmpty(),
                'descripcion' => "Mora por retraso ({$m->percentage}% de cuota de " . round($m->base_amount, 2) . ") + Gasto de reconexión"
            ])
            ->toArray();
    }
}