<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Financing;
use App\Models\MoraRecord;
use App\Http\Services\Financing\MoraCalculationService;
use Carbon\Carbon;

class GenerateSaturdayMorasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mora:generate-saturday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar moras automáticamente según la frecuencia del financiamiento a las 17:00';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $this->info("Procesando moras. (Ahora: " . $now->toDateTimeString() . ")");

        $financings = Financing::where('status', 'active')
            ->where('moraStatus', true)
            ->with(['user.persona'])
            ->get();

        $count = 0;

        foreach ($financings as $f) {
            $lastCutoff = $this->calculateLastCutoff($f, $now);
            if (!$lastCutoff) {
                continue;
            }

            // Obtener el número de cuotas pagadas (excluyendo la inicial si es installment_number 0)
            $approvedCount = $f->payments()
                ->where('status', 'approved')
                ->where('installment_number', '>', 0)
                ->count();

            // Usar la lógica del modelo para obtener la fecha de vencimiento de la SIGUIENTE cuota
            $nextInstallmentNumber = $approvedCount + 1;

            // Verificar si hay un pago pendiente para esta cuota específica
            $hasPendingPayment = $f->payments()
                ->where('status', 'pending')
                ->where('installment_number', $nextInstallmentNumber)
                ->exists();

            if ($hasPendingPayment) {
                continue;
            }

            // Si ya pagó todo el saldo, no debería generar mora
            if (!$f->hasPendingBalance()) {
                continue;
            }

            $dueDate = $f->getNthDueDate($nextInstallmentNumber);
            $moraDeadline = $dueDate->copy()->setHour(17)->setMinute(0)->setSecond(0);

            // Si ya pasó la fecha de pago y el deadline de las 17:00
            if ($now->greaterThanOrEqualTo($moraDeadline)) {
                // Verificar si ya se creó una mora para este financiamiento en el ciclo "actual"
                // El ciclo depende de la frecuencia y empieza en el $lastCutoff
                $alreadyCreatedInThisCycle = MoraRecord::where('financing_id', $f->id)
                    ->where('created_at', '>=', $lastCutoff->toDateTimeString())
                    ->exists();

                if (!$alreadyCreatedInThisCycle) {
                    $installmentValue = $f->getInstallmentPrice($nextInstallmentNumber);

                    MoraCalculationService::createMoraRecord($f->id, $installmentValue);
                    $count++;

                    $userName = $f->user && $f->user->persona ? $f->user->persona->fullName : 'N/A';
                    $this->line("<info>[GEN] Mora generada:</info> Plan: {$f->plan} | ID {$f->id} | {$userName} | Cuota #{$nextInstallmentNumber} | Vencía: {$dueDate->toDateString()}");
                }
            }
        }

    }

    /**
     * Calcula el punto de inicio del ciclo actual de mora según el plan.
     */
    private function calculateLastCutoff(Financing $f, Carbon $now): ?Carbon
    {
        $plan = $f->plan;

        if ($plan === 'Diario') {
            $cutoff = $now->copy()->setHour(17)->setMinute(0)->setSecond(0);
            if ($now->lt($cutoff)) {
                $cutoff->subDay();
            }
            return $cutoff;
        }

        if ($plan === 'Semanal') {
            return $now->isSaturday() && $now->hour >= 17
                ? $now->copy()->setHour(17)->setMinute(0)->setSecond(0)
                : $now->copy()->previous(Carbon::SATURDAY)->setHour(17)->setMinute(0)->setSecond(0);
        }

        if ($plan === 'Quincenal') {
            $c15 = $now->copy()->day(15)->setHour(17)->setMinute(0)->setSecond(0);

            $c30 = $now->copy();
            $day30 = ($c30->month === 2) ? $c30->endOfMonth()->day : 30;
            $c30->day($day30)->setHour(17)->setMinute(0)->setSecond(0);

            if ($now->greaterThanOrEqualTo($c30)) {
                return $c30;
            }
            if ($now->greaterThanOrEqualTo($c15)) {
                return $c15;
            }

            // Si no llegamos al 15 todavía, el último cutoff fue el 30 del mes anterior
            $prev = $now->copy()->subMonthNoOverflow();
            $pDay30 = ($prev->month === 2) ? $prev->copy()->endOfMonth()->day : 30;
            return $prev->day($pDay30)->setHour(17)->setMinute(0)->setSecond(0);
        }

        if ($plan === 'Mensual') {
            $c30 = $now->copy();
            $day30 = ($c30->month === 2) ? $c30->endOfMonth()->day : 30;
            $c30->day($day30)->setHour(17)->setMinute(0)->setSecond(0);

            if ($now->greaterThanOrEqualTo($c30)) {
                return $c30;
            }

            $prev = $now->copy()->subMonthNoOverflow();
            $pDay30 = ($prev->month === 2) ? $prev->copy()->endOfMonth()->day : 30;
            return $prev->day($pDay30)->setHour(17)->setMinute(0)->setSecond(0);
        }

        return null;
    }
}


/*

* verificar cobros, si es que pas olo que vi

*/