<?php

namespace App\Http\Services\guest;

use App\Http\Requests\guest\FormPayRequest;
use App\Models\Financing;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PaymentStoreService
{
    public static function execute(FormPayRequest $request, $financing_id): JsonResponse
    {
        $data = $request;
        $userId = Auth::id();

        $financing = Financing::with('application', 'payments')->find($financing_id);

        if (!$financing || $financing->user_id !== $userId) {
            return response()->json([
                'message' => 'No se encontró un financiamiento asociado a esta solicitud.'
            ], 404);
        }

        try {
            if ($request->has('mora_id')) {
                $mora = \App\Models\MoraRecord::find($request->mora_id);
                if (!$mora || $mora->financing_id !== $financing->id) {
                    return response()->json(['message' => 'No se encontró el registro de mora.'], 404);
                }

                if ($mora->status === 'paid' || $mora->status === 'approved') {
                    return response()->json(['message' => 'Esta mora ya ha sido pagada.'], 400);
                }

                $expectedAmount = round($mora->total_amount, 2);
                $description = "Pago de mora (Cupón {$mora->occurrence_index})";
                $moraId = $mora->id;
                $cuota = 0; // Mora payments don't advance the installment count

                // We keep it as 'pending' but with a linked payment.
                // The status will only change to 'approved' once the admin approves the payment.

            } else {
                $lastPayment = Payment::where('financing_id', $financing->id)
                    ->where('status', 'approved')
                    ->where('installment_number', '>', '0')
                    ->orderByDesc('installment_number')
                    ->first();

                $cuota = $lastPayment ? $lastPayment->installment_number + 1 : 1;

                if (!$financing->hasPendingBalance()) {
                    return response()->json(['message' => 'Este financiamiento ya ha sido completado.'], 400);
                }

                $pendingPayment = Payment::where('financing_id', $financing->id)
                    ->where('installment_number', $cuota)
                    ->where('status', 'pending')
                    ->exists();

                if ($pendingPayment) {
                    return response()->json([
                        'message' => "Ya existe un pago pendiente de revisión para la cuota N° {$cuota}."
                    ], 400);
                }

                $expectedAmount = $financing->getInstallmentPrice($cuota);
                $expectedAmount = round($expectedAmount, 2);

                $description = "Pago número $cuota";
                $moraId = null;
            }

            if ($data['total'] < $expectedAmount) {
                $expectedFormatted = number_format($expectedAmount, 2, '.', ',');
                return response()->json([
                    'message' => "Se esperaba un pago de $$expectedFormatted para completar esta transacción.",
                    'data' => [$data['total'], $expectedAmount]
                ], 400);
            }

            $folderPath = $financing->application
                ? 'applications/' . $financing->application->vehicle_id . '/' . $financing->application->folder . "/cuotas/"
                : 'financings/' . $financing->id . "/cuotas/";

            if (!Storage::disk('private')->exists($folderPath)) {
                Storage::disk('private')->makeDirectory($folderPath, 0755, true);
            }

            $file = $data['upload-comprobante'];
            $fileName = ($moraId ? "pago_mora_{$moraId}_" : "pago_{$cuota}_") . Carbon::now()->timestamp . "." . $file->getClientOriginalExtension();

            Storage::disk('private')->putFileAs($folderPath, $file, $fileName);

            $filePath = $folderPath . $fileName;

            $pathHash = Crypt::encryptString($filePath);

            $capital = 0;
            $interes = 0;

            if ($moraId) {
                $interes = $expectedAmount; // Mora is pure interest
            } else {
                $finalPrice = $financing->final_price ?: 1;
                $totalPagado = $financing->payments()->whereNull('mora_id')->where('status', 'approved')->sum('total');
                $totalPrice = $financing->final_price - $totalPagado;
                $ratioInteres = $financing->interes_porcent / 100;
                $finalPrice = $totalPrice * $ratioInteres;
                $interes = round($finalPrice, 2);
                $capital = $expectedAmount - $interes;
            }

            Payment::create([
                'financing_id' => $financing->id,
                'account_method_id' => $data['account_method_id'],
                'installment_number' => $cuota,
                'mora_id' => $moraId,
                'total' => $expectedAmount,
                'total_capital' => $capital,
                'total_interes' => $interes,
                'interes_porcent' => $financing->interes_porcent ?? 0,
                'description' => $description,
                'file_path' => $pathHash,
            ]);

            return response()->json([
                'message' => 'Comprobante guardado exitosamente. El pago está pendiente de revisión.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al guardar el comprobante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}