<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Utils\WhatsAppService;
use App\Models\Payment;
use App\Models\Financing;
use Exception;
use Illuminate\Support\Facades\DB;

class StatusPaymentsService
{
    public static function updateStatus(int $id, bool $value): array
    {
        $user = auth()->user();
        $lotesIds = $user->lotes->pluck('id')->toArray();

        try {
            $payment = Payment::findOrFail($id);
            $payment->status = $value ? 'approved' : 'rejected';
            $payment->save();

            if ($value) {
                $mora = null;
                if ($payment->mora_id) {
                    $mora = \App\Models\MoraRecord::find($payment->mora_id);
                    if ($mora) {
                        $mora->update(['status' => 'approved']);
                    }
                }


                if ($financing) {
                    if ($financing->lote_id && !in_array($financing->lote_id, $lotesIds)) {
                        return [
                            'success' => false,
                            'message' => "No se pudo actualizar el estado. Financiamiento no te pertenece.",
                        ];
                    }

                    $approvedCount = $financing->payments()
                        ->where('status', 'approved')
                        ->where('installment_number', '>', '0')
                        ->count();

                    $totalInstallments = $financing->getTotalInstallmentsCount();

                    /*

                    AQUI ANTES SE CERRABA LA FINANCIACION AUTOMATICAMENTE

                    if ($approvedCount >= $totalInstallments) {
                        $financing->status = 'finished';
                        $financing->code = $financing->code . date('m/Y') . '(F)';
                        $financing->save();
                    }*/
                }
            } else {
                // If rejected and it's a mora payment, revert mora record to pending
                if ($payment->mora_id) {
                    \App\Models\MoraRecord::where('id', $payment->mora_id)->update(['status' => 'pending']);
                }
            }

            DB::commit();

            $phone = $payment->financing->user->persona->phone;
            $code = $payment->financing->code;
            $status = $value ? 'aprobado' : 'rechazado';
            $numCuota = $payment->installment_number > 0 ? $payment->installment_number : "Mora";

            if ($phone && $payment->financing->status == 'active') {
                $wa = app(WhatsAppService::class);

                if ($payment->mora_id) {
                    $wa->sendTemplate(
                        $phone,
                        "pay_mora_status",
                        [$mora->occurrence_index, $code, $status],
                        'es_CO'
                    );
                } else {
                    $wa->sendTemplate(
                        $phone,
                        "pay_status",
                        [$numCuota, $code, $status],
                        'es_CO'
                    );
                }
            }
            return [
                'success' => true,
                'message' => "Pago " . ($status) . " correctamente.",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => "No se pudo actualizar el estado.",

            ];
        }
    }
}