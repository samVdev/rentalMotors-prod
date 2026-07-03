<?php

namespace App\Http\Services\Statics;

use Illuminate\Http\JsonResponse;
use App\Models\Payment;

class IndexPaymentsDashService
{
    public static function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $payments = Payment::query()
                ->with([
                    'financing.user.persona:id,fullName,cedula',
                ])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereIn('lote_id', $lotesIds);
                })
                ->where('status', 'pending')
                ->orderBy('id', 'desc')->get();

            $rows = $payments->map(function ($payment) {
                $financing = $payment->financing;
                $user = $financing?->user;
                $persona = $user?->persona;

                return [
                    'financing_id' => $financing?->id,
                    'id'           => $payment->id,
                    'codigo'           => $payment->financing->code,
                    'monto'        => $payment->total ?? 0,
                    'lote'        => $financing?->lote?->nombre ?? 'N/A',
                    'fecha'        => $payment->created_at?->format('d/m/Y') ?? '',
                    'cuota'        => 'Cuota #' . ($payment->installment_number ?? '-'),
                    'cliente'      => $persona?->fullName ?? 'Desconocido',
                    'cedula'      => $persona?->cedula ?? 'Desconocido',
                    'status'       => $payment->status ?? 'pending',
                    'file'         => $payment->file_path
                ];
            });

            return response()->json($rows);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
