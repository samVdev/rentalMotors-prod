<?php

namespace App\Http\Services\Application;

use App\Http\Requests\Application\FormDocRequest;
use App\Models\Application;
use App\Models\Financing;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class AttachPaymentDocumentService
{
    public static function execute(FormDocRequest $request, $applicationId): JsonResponse
    {
        $data = $request;

        $application = Application::find($applicationId);
        $valorLimpio = str_replace(['.', ','], ['', '.'], $request->inicial);
        $inicial = (float) $valorLimpio;

        if (!$application) {
            return response()->json([
                'message' => 'Solicitud no encontrada.'
            ], 404);
        }

        $financing = Financing::where('application_id', $application->id)->first();

        if (!$financing) {
            return response()->json([
                'message' => 'No se encontró un financiamiento asociado a esta solicitud.'
            ], 404);
        }

        if ($financing->payment_initial) {
            return response()->json([
                'message' => 'Ya tiene asignado un pago inicial'
            ], 200);
        }

        try {
            $folderPath = 'applications/' . $application->vehicle_id . '/' . $application->folder . "/cuotas/";

            if (!Storage::disk('private')->exists($folderPath)) {
                Storage::disk('private')->makeDirectory($folderPath, 0755, true);
            }

            $file = $data['upload-comprobante'];
            $fileName = 'primer_pago.' . $file->getClientOriginalExtension();
            $filePath = $folderPath . $fileName;

            Storage::disk('private')->putFileAs($folderPath, $file, $fileName);

            $mesesContrato = $data['meses'];
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addMonths($mesesContrato);

            $financing_price = $financing->cost_price - $inicial;

            // tasa de interes extrana
            $interes_total = $financing_price * $data['interes'];

            // Sumar servicios al precio final
            $total_services = 0;
            if ($request->has('services')) {
                foreach ($request->services as $service) {
                    $total_services += (float) ($service['price'] ?? 0);
                }
            }

            $final_price = $financing_price + $interes_total + $total_services;

            $totalCuotas = match ($financing->plan) {
                'Diario' => (int) ($mesesContrato * 4 * 6),
                'Semanal' => (int) ($mesesContrato * 4),
                'Quincenal' => (int) ($mesesContrato * 2),
                'Mensual' => (int) $mesesContrato,
                default => (int) $mesesContrato,
            };

            $precioPorCuota = $final_price / $totalCuotas;

            $price_mensual = ($financing->plan == 'Mensual') ? $precioPorCuota : ($final_price / $mesesContrato);
            $price_quincenal = ($financing->plan == 'Quincenal') ? $precioPorCuota : ($price_mensual / 2);
            $price_semanal = ($financing->plan == 'Semanal') ? $precioPorCuota : ($price_mensual / 4);
            $price_diario = ($financing->plan == 'Diario') ? $precioPorCuota : ($price_semanal / 6);

            $encryptedPath = Crypt::encryptString($filePath);

            $financing->update([
                'plan' => $data['plan'],

                'payment_initial' => $encryptedPath,
                'installments' => $financing->plan != 'Mensual' ? $totalCuotas : $mesesContrato,
                'interes_porcent' => $data['interes'],
                'lote_id' => $data['lote_id'],
                'months' => $data['meses'],
                'total_inicial' => $inicial,

                'code' => strtoupper($data['code']),

                'financing_price' => $financing_price,
                'interes_price' => $interes_total,
                'final_price' => $final_price,

                'price_mensual' => $price_mensual,
                'price_quincenal' => $price_quincenal,
                'price_semanal' => $price_semanal,
                'price_diario' => $price_diario,

                'responsable_id' => auth()->user()->id,
                'start_date' => $startDate,
            ]);

            if ($request->has('services')) {
                $servicesToSync = [];
                foreach ($request->services as $service) {
                    $servicesToSync[$service['id']] = ['price' => $service['price']];
                }
                $financing->services()->sync($servicesToSync);
            }

            Payment::create([
                'financing_id' => $financing->id,
                'installment_number' => 0,
                'total' => $inicial,
                'total_capital' => $inicial,
                'total_interes' => 0,
                'description' => 'Primer pago',
                'status' => 'approved',
                'file_path' => $encryptedPath,
            ]);

            return response()->json([
                'message' => 'Comprobante de pago subido exitosamente.',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Ocurrió un error al guardar el comprobante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}