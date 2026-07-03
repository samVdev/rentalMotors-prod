<?php

namespace App\Http\Services\Statics;

use App\Models\Financing;
use App\Models\Payment;
use App\Models\MoraRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CobrosStaticsService
{
    public static function getSummary(Request $request)
    {
        $user = auth()->user();
        $lotesIds = $user->lotes->pluck('id')->toArray();
        $now = Carbon::now();

        $financings = Financing::with('payments')
            ->where('status', 'active')
            ->whereIn('lote_id', $lotesIds)
            ->get();

        $plans = ['Diario', 'Semanal', 'Quincenal', 'Mensual'];
        $summary = [];

        foreach ($plans as $plan) {
            $planFinancings = $financings->where('plan', $plan);

            $dueFinancings = $planFinancings->filter(function ($f) use ($now) {
                if (!$f->hasPendingBalance())
                    return false;

                $approvedCount = $f->payments->where('status', 'approved')->where('installment_number', '>', 0)->count();
                $dueDate = $f->getNextCalendarDueDate();

                return $now->gte($dueDate->startOfDay());
            });

            $paidClientsCount = 0;

            $pendingCount = $dueFinancings->count();

            $totalPlanCount = $planFinancings->count();

            $summary[strtolower($plan)] = [
                'title' => $plan,
                'paid' => $paidClientsCount,
                'total' => $totalPlanCount,
                'pending' => $totalPlanCount - $paidClientsCount
            ];
        }

        $totalAll = array_reduce($summary, function ($carry, $item) {
            $carry['paid'] += $item['paid'];
            $carry['total'] += $item['total'];
            $carry['pending'] += $item['pending'];
            return $carry;
        }, ['title' => 'Total General', 'paid' => 0, 'total' => 0, 'pending' => 0]);

        $summary['total'] = $totalAll;

        return $summary;
    }

    public static function getAllFinancings(Request $request)
    {
        $user = auth()->user();
        $lotesIds = $user->lotes->pluck('id')->toArray();
        $now = Carbon::now();

        $financings = Financing::with(['user.persona', 'payments'])
            ->where('status', 'active')
            ->whereIn('lote_id', $lotesIds)
            ->get();

        return $financings->map(function ($f) use ($now) {
            $plan = $f->plan;
            $approvedCount = $f->payments->where('status', 'approved')->where('installment_number', '>', 0)->count();
            $dueDate = $f->getNextCalendarDueDate();
            $planPrice = $f->getInstallmentPrice($approvedCount + 1);

            $hasPaid = $f->payments
                ->where('status', 'approved')
                ->where('installment_number', '>', 0)
                ->filter(function ($payment) use ($plan, $now) {
                    $paymentDate = Carbon::parse($payment->created_at);
                    if ($plan === 'Diario')
                        return $paymentDate->isSameDay($now);
                    if ($plan === 'Semanal')
                        return $paymentDate->between($now->copy()->startOfWeek(Carbon::MONDAY), $now->copy()->endOfWeek(Carbon::SUNDAY));
                    if ($plan === 'Quincenal') {
                        $day = $now->day;
                        if ($day <= 15)
                            return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->day(15)->endOfDay());
                        return $paymentDate->between($now->copy()->day(16)->startOfDay(), $now->copy()->endOfMonth());
                    }
                    if ($plan === 'Mensual')
                        return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
                    return false;
                })->isNotEmpty();

            return [
                'id' => $f->id,
                'code' => $f->code ?? ('MOVIL ' . $f->id),
                'client_name' => $f->user?->persona?->fullName ?? 'N/A',
                'user_id' => $f->user_id,
                'moto_status' => $f->turned_off_at === null,
                'turned_off_at' => $f->turned_off_at ? $f->turned_off_at->format('d/m/Y H:i') : null,
                'plan' => $f->plan,
                'plan_price' => (float) $planPrice,
                'installments' => (int) $f->getTotalInstallmentsCount(),
                'due_date' => $dueDate->format('d/m/Y'),
                'is_paid' => $hasPaid
            ];
        })->values();
    }

    public static function getPendingPayments(Request $request)
    {
        $user = auth()->user();
        $lotesIds = $user->lotes->pluck('id')->toArray();

        $now = Carbon::now();

        $financings = Financing::with(['user.persona', 'payments', 'moraRecords.payments'])
            ->where('status', 'active')
            ->whereIn('lote_id', $lotesIds)
            ->get();

        $pendingList = $financings->filter(function ($f) use ($now) {
            $plan = $f->plan;

            $hasPaid = $f->payments
                ->where('status', 'approved')
                ->where('installment_number', '>', 0)
                ->filter(function ($payment) use ($plan, $now) {
                    $paymentDate = Carbon::parse($payment->created_at);
                    if ($plan === 'Diario') {
                        return $paymentDate->isSameDay($now);
                    } elseif ($plan === 'Semanal') {
                        return $paymentDate->between($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
                    } elseif ($plan === 'Quincenal') {
                        $day = $now->day;
                        if ($day <= 15) {
                            return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->day(15)->endOfDay());
                        } else {
                            return $paymentDate->between($now->copy()->day(16)->startOfDay(), $now->copy()->endOfMonth());
                        }
                    } elseif ($plan === 'Mensual') {
                        return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
                    }
                    return false;
                })->isNotEmpty();

            if ($hasPaid)
                return false;

            if (!$f->hasPendingBalance())
                return false;

            $approvedCount = $f->payments->where('status', 'approved')->where('installment_number', '>', 0)->count();
            $dueDate = $f->getNextCalendarDueDate();

            return $now->gte($dueDate->startOfDay());
        });

        return $pendingList->map(function ($f) use ($now) {
            $approvedCount = $f->payments->where('status', 'approved')->where('installment_number', '>', 0)->count();
            $dueDate = $f->getNextCalendarDueDate();
            $planPrice = $f->getInstallmentPrice($approvedCount + 1);

            $activeMora = $f->moraRecords->where('status', 'pending')->last();

            $hasMoraPayment = false;
            $moraPaymentDate = '--';
            $moraDaysLate = 0;

            if ($activeMora) {
                $lastMoraPayment = $activeMora->payments->whereIn('status', ['approved', 'pending'])->last();
                if ($lastMoraPayment) {
                    $hasMoraPayment = true;
                    $moraPaymentDate = $lastMoraPayment->created_at->format('d/m/Y');
                }

                $moraDaysLate = $now->diffInDays($activeMora->created_at);
            }

            return [
                'id' => $f->id,
                'type' => 'Cuota',
                'code' => $f->code ?? ('MOVIL ' . $f->id),
                'client_name' => $f->user?->persona?->fullName ?? 'N/A',
                'gps_status' => true,
                'moto_status' => $f->turned_off_at === null,
                'turned_off_at' => $f->turned_off_at ? $f->turned_off_at->format('d/m/Y H:i') : null,
                'user_id' => $f->user_id,
                'client_phone' => $f->user?->persona?->phone ?? '',
                'plan' => $f->plan,
                'installments' => (int) $f->getTotalInstallmentsCount(),
                'plan_price' => (float) $planPrice,
                'start_date' => $f->start_date,
                'due_date' => $dueDate->format('d/m/Y'),
                'mora_paid' => $hasMoraPayment,
                'mora_date' => $moraPaymentDate,
                'days_late' => (int) $moraDaysLate
            ];
        })->values();

        return $pendingList;
    }

    public static function getCompletedPayments(Request $request)
    {
        $user = auth()->user();
        $lotesIds = $user->lotes->pluck('id')->toArray();

        $now = Carbon::now();

        $financings = Financing::with(['user.persona', 'payments', 'moraRecords.payments'])
            ->where('status', 'active')
            ->whereIn('lote_id', $lotesIds)
            ->get();

        $completedList = $financings->filter(function ($financing) use ($now) {
            $plan = $financing->plan;

            return $financing->payments
                ->where('status', 'approved')
                ->where('installment_number', '>', 0)
                ->filter(function ($payment) use ($plan, $now) {
                    $paymentDate = Carbon::parse($payment->created_at);
                    if ($plan === 'Diario') {
                        return $paymentDate->isSameDay($now);
                    } elseif ($plan === 'Semanal') {
                        return $paymentDate->between($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
                    } elseif ($plan === 'Quincenal') {
                        $day = $now->day;
                        if ($day <= 15) {
                            return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->day(15)->endOfDay());
                        } else {
                            return $paymentDate->between($now->copy()->day(16)->startOfDay(), $now->copy()->endOfMonth());
                        }
                    } elseif ($plan === 'Mensual') {
                        return $paymentDate->between($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
                    }
                    return false;
                })->isNotEmpty();
        });

        $completedList = $completedList->map(function ($f) {
            $moraPaidRecord = $f->moraRecords
                ->filter(fn($m) => $m->payments->isNotEmpty())
                ->last();
            $hasMoraPayment = $moraPaidRecord !== null;
            $moraPaymentDate = $moraPaidRecord?->payments->last()?->created_at?->format('d/m/Y') ?? '--';

            return [
                'id' => $f->id,
                'type' => 'Cuota',
                'code' => $f->code ?? ('MOVIL ' . $f->id),
                'client_name' => $f->user?->persona?->fullName ?? 'N/A',
                'moto_status' => $f->turned_off_at === null,
                'turned_off_at' => $f->turned_off_at ? $f->turned_off_at->format('d/m/Y H:i') : null,
                'client_phone' => $f->user?->persona?->phone ?? '',
                'user_id' => $f->user_id,
                'plan' => $f->plan,
                'mora_paid' => $hasMoraPayment,
                'mora_date' => $moraPaymentDate,
                'days_late' => 0
            ];
        });

        return $completedList->values();
    }
}