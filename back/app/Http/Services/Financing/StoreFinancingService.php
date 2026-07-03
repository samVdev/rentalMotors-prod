<?php

namespace App\Http\Services\Financing;

use App\Http\Requests\Financing\FinancingFormRequest;
use App\Models\Financing;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class StoreFinancingService
{
    public static function execute(FinancingFormRequest $request): JsonResponse
    {
        try {
            $cost_price = $request->precio;

            $valorLimpio = str_replace(['.', ','], ['', '.'], $request->inicial);
            $inicial = (float) $valorLimpio;

            $financing_price = $cost_price - $inicial;
            $interes_price = $financing_price * $request->interes;

            $total_services = 0;
            if ($request->has('services')) {
                foreach ($request->services as $service) {
                    $total_services += (float) ($service['price'] ?? 0);
                }
            }

            $final_price = $interes_price + $financing_price + $total_services;

            $mesesContrato = $request->meses;
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addMonths($mesesContrato);

            $totalCuotas = match ($request->plan) {
                'Diario' => (int) ($mesesContrato * 4 * 6),
                'Semanal' => (int) ($mesesContrato * 4),
                'Quincenal' => (int) ($mesesContrato * 2),
                'Mensual' => (int) $mesesContrato,
                default => (int) $mesesContrato,
            };


            $precioPorCuota = $final_price / $totalCuotas;

            $price_mensual = ($request->plan == 'Mensual') ? $precioPorCuota : ($final_price / $mesesContrato);
            $price_quincenal = ($request->plan == 'Quincenal') ? $precioPorCuota : ($price_mensual / 2);
            $price_semanal = ($request->plan == 'Semanal') ? $precioPorCuota : ($price_mensual / 4);
            $price_diario = ($request->plan == 'Diario') ? $precioPorCuota : ($price_semanal / 6);

            $code = strtoupper($request->code);

            $financing = Financing::create([
                'application_id' => null,
                'plan' => $request->plan,
                'vehicle_id' => $request->vehiculo ?? null,
                'user_id' => $request->cliente,
                'responsable_id' => auth()->user()->id,
                'type' => $request->tipo,
                'installments' => $request->plan != 'Mensual' ? $totalCuotas : $mesesContrato,
                'months' => $request->meses,
                'start_date' => $startDate,
                'observation' => $request->observacion,

                'lote_id' => $request->lote_id,
                'code' => $code,

                'status' => 'active',
                'interes_porcent' => $request->interes,
                'total_inicial' => $inicial,

                'cost_price' => $cost_price,
                'financing_price' => $financing_price,
                'interes_price' => $interes_price,
                'final_price' => $final_price,

                'price_mensual' => $price_mensual,
                'price_quincenal' => $price_quincenal,
                'price_semanal' => $price_semanal,
                'price_diario' => $price_diario,
            ]);

            if ($request->has('services')) {
                $servicesToSync = [];
                foreach ($request->services as $service) {
                    $servicesToSync[$service['id']] = ['price' => $service['price']];
                }
                $financing->services()->sync($servicesToSync);
            }

            if ($request->hasFile('upload-comprobante')) {
                $file = $request->file('upload-comprobante');
                $folderPath = 'financings/' . $financing->id . '/cuotas/';

                if (!Storage::disk('private')->exists($folderPath)) {
                    Storage::disk('private')->makeDirectory($folderPath, 0755, true);
                }

                $fileName = 'primer_pago_' . '.' . $file->getClientOriginalExtension();
                $filePath = $folderPath . $fileName;

                Storage::disk('private')->putFileAs($folderPath, $file, $fileName);

                $encryptedPath = Crypt::encryptString($filePath);

                $financing->update([
                    'payment_initial' => $encryptedPath,
                ]);

                $pathHash = Crypt::encryptString($filePath);

                Payment::create([
                    'financing_id' => $financing->id,
                    'installment_number' => 0,
                    'total' => $inicial ?? 0,
                    'total_capital' => $inicial ?? 0,
                    'total_interes' => 0,
                    'interes_porcent' => $request->interes,
                    'description' => 'Primer pago',
                    'status' => 'approved',
                    'file_path' => $pathHash,
                ]);
            }

            return response()->json([
                'message' => 'Financiamiento creado correctamente',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el financiamiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}