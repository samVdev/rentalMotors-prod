<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use App\Models\Financing;

class showFinancingService
{
    static public function execute(int $id): JsonResponse
    {
        try {
            $financing = Financing::query()
                ->select(
                    'id',
                    'vehicle_id',
                    'user_id',
                    'type',
                    'installments',
                    'start_date',
                    'status',
                    'plan',
                    'final_price',
                    'payment_initial',
                    'code',
                    'deuda_adquirida',
                    'observation',
                    'cost_price',
                    'financing_price',
                    'interes_price',
                    'final_price',
                    'plate',
                    'months',
                    'price_diario',
                    'price_semanal',
                    'price_quincenal',
                    'price_mensual',

                    'moraStatus',
                    'interes_porcent',
                    'application_id',
                    'lote_id'
                )
                ->where('id', $id)
                ->with([
                    'vehicle:id,brand,model,image',
                    'user:id,persona_id',
                    'user.persona:id,fullName,phone,cedula,image',
                    'payments:id,financing_id,mora_id,installment_number,total,total_capital,total_interes,interes_porcent,description,status,file_path,created_at',
                    'lote:id,nombre',
                    'services:id,name',
                    'application:id,document_pdf',
                ])
                ->first();


            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();


            if (!$financing || !in_array($financing->lote_id, $lotesIds)) {

                return response()->json([
                    'message' => 'Financiación no encontrada',
                ], 404);
            }

            $all_services = \App\Models\Service::all();
            $financing_services = $financing->services->keyBy('id');

            $services_list = $all_services->map(function ($service) use ($financing_services) {
                $associated = $financing_services->get($service->id);
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'is_included' => !!$associated,
                    'price' => $associated ? (float) $associated->pivot->price : 0,
                ];
            });

            $statusTranslations = [
                'pending' => 'pendiente',
                'approved' => 'aprobado',
                'rejected' => 'rechazado',
                'finished' => 'finalizada',
                'active' => 'activa'
            ];

            $translateStatus = fn($status) =>
                $statusTranslations[$status] ?? ucfirst($status ?? 'Sin dato');

            $approvedPayments = $financing->payments->where('status', 'approved');

            $pagado = (clone $approvedPayments)->where('installment_number', '>', '0')->sum('total') ?? 0;
            $pagado_mora = (clone $approvedPayments)->whereNotNull('mora_id')->sum('total') ?? 0;
            $precioFinal = $financing->final_price ?? 0;
            $faltante = max($precioFinal - $pagado, 0);

            $deudaAdquirida = $financing->deuda_adquirida;


            $data = [
                'id' => $financing->id,
                'application_id' => $financing->application_id,
                'cliente' => $financing->user->persona->fullName ?? 'N/A',
                'cliente_ci' => $financing->user->persona->cedula ?? 'N/A',
                'cliente_contact' => $financing->user->persona->phone ?? 'N/A',
                'cliente_archivo' => $financing->user->persona->image ?? null,

                'meses' => $financing->months ?? 0,
                'lote' => $financing->lote?->nombre ?? null,
                'recaudos_pdf' => $financing->application->document_pdf ?? null,

                'vehiculo_marca' => $financing->vehicle->brand ?? 'N/A',
                'vehiculo_model' => $financing->vehicle->model ?? 'N/A',
                'vehiculo_placa' => $financing->plate ?? 'N/A',

                'image_vehiculo' => $financing->vehicle ? $financing->vehicle->image : '',

                'tipo' => match ($financing->type) {
                    'vehicle' => 'Vehículo',
                    'tax' => 'Impuesto',
                    'mantenence' => 'Mantenimiento',
                    default => 'Sin dato',
                },

                'observacion' => $financing->observation ?? null,

                'plan' => $financing->plan ?? 'Sin dato',
                'cuotas' => $financing->installments ?? 0,
                'fecha_inicio' => $financing->start_date ?? 'Sin dato',
                'estado' => $translateStatus($financing->status),
                'codigo' => $financing->code,

                'total_capital' => $financing->payments->sum('total_capital') ?? 0,
                'total_interes' => $financing->payments->sum('total_interes') ?? 0,

                'precio' => (float) $financing->cost_price,
                'cost_inicial' => (float) $financing->financing_price,
                'inicial' => (float) $financing->cost_price - $financing->financing_price,
                'intereses' => (float) $financing->interes_price,

                'pago_inicial' => $financing->payment_initial ?? 0,
                'precio_final' => (float) $precioFinal - $deudaAdquirida,
                'precio_mora' => (float) $pagado_mora,
                'pagado' => $pagado,
                'faltante' => $faltante,

                'mensual' => (float) $financing->price_mensual,
                'quincenal' => (float) $financing->price_quincenal,
                'semanal' => (float) $financing->price_semanal,
                'diario' => (float) $financing->price_diario,

                'moraEstatus' => $financing->moraStatus,
                'interes_porcent' => (float) $financing->interes_porcent,
                'services' => $services_list,

                'deudaAdquirida' => (float) $deudaAdquirida,
                'deudaPagar' => (float) ($precioFinal - $deudaAdquirida) - $pagado,


                'pagos' => $financing->payments->map(function ($p) use ($translateStatus) {
                    return [
                        'nro_cuota' => $p->installment_number,
                        'total' => (float) $p->total,
                        'mora_index' => $p->mora_id ? true : false,
                        'descripcion' => $p->description ?? 'Sin descripción',
                        'status' => $translateStatus($p->status),
                        'archivo' => $p->file_path,
                        'date' => $p->created_at,
                        'total_capital' => (float) $p->total_capital ?? 0,
                        'total_interes' => (float) $p->total_interes ?? 0,
                        'interes_porcent' => (float) $p->interes_porcent ?? 0,
                    ];
                }),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener la financiación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}