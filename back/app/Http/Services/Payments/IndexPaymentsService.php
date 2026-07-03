<?php

namespace App\Http\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Payment;

class IndexPaymentsService
{
    public static function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->input("offset", 0);
            $limit = $request->input("limit", 10);
            $status = $request->input("type", null);
            $search = $request->input("search");

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $query = Payment::query()
                ->with([
                    'financing.user.persona:id,fullName,cedula',
                    'accountMethod'
                ])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')
                        ->whereIn('lote_id', $lotesIds);
                })
                ->orderBy('id', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('financing', function ($q2) use ($search) {
                        $q2->where('code', 'ilike', "%$search%");
                    })
                        ->orWhereHas('financing.user.persona', function ($q2) use ($search) {
                            $q2->where('fullName', 'ilike', "%$search%")
                                ->orWhere('cedula', 'ilike', "%$search%");
                        });
                });
            }

            $payments = $query->skip($offset)->take($limit)->get();

            $rows = $payments->map(function ($payment) {
                $financing = $payment->financing;
                $user = $financing?->user;
                $persona = $user?->persona;

                return [
                    'financing_id' => $financing?->id,
                    'id' => $payment->id,
                    'codigo' => $payment->financing->code,
                    'lote' => $payment->financing->lote->nombre,
                    'monto' => $payment->total ?? 0,
                    'is_mora' => $payment->mora ? true : false,
                    'mora_percentage' => $payment->mora ? $payment->mora->percentage : null,
                    'mora_index' => $payment->mora ? $payment->mora->occurrence_index : null,
                    'fecha' => $payment->created_at?->format('d/m/Y') ?? '',
                    'cuota' => 'Cuota #' . ($payment->installment_number ?? '-'),
                    'cuenta_destino' => $payment->accountMethod ? $payment->accountMethod->provider_name . ' - ' . $payment->accountMethod->identifier : null,
                    'cliente' => $persona?->fullName ?? 'Desconocido',
                    'cedula' => $persona?->cedula ?? 'Desconocido',
                    'status' => $payment->status ?? 'pending',
                    'file' => $payment->file_path
                ];
            });

            return response()->json([
                'rows' => $rows,
                'offset' => $offset,
                'limit' => $limit,
                'sort' => '',
                'direction' => '',
                'search' => $search ?? '',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
