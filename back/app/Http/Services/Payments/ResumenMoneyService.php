<?php

namespace App\Http\Services\Payments;

use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use Carbon\Carbon;

class ResumenMoneyService
{
    public static function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $today = Carbon::today();
            $startWeek = Carbon::now()->startOfWeek();
            $startQuince = Carbon::now()->subDays(15);


            $startMonth = Carbon::now()->startOfMonth();

            $daily = Payment::where('status', 'approved')
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')->whereIn('lote_id', $lotesIds);
                })
                ->whereDate('created_at', $today)
                ->sum('total');

            $week = Payment::where('status', 'approved')
                ->whereBetween('created_at', [$startWeek, now()])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')->whereIn('lote_id', $lotesIds);
                })
                ->sum('total');

            $quince = Payment::where('status', 'approved')
                ->whereBetween('created_at', [$startQuince, now()])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')->whereIn('lote_id', $lotesIds);
                })
                ->sum('total');

            $month = Payment::where('status', 'approved')
                ->whereBetween('created_at', [$startMonth, now()])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')->whereIn('lote_id', $lotesIds);
                })
                ->sum('total');


            return response()->json([
                'daily'     => (float) $daily,
                'week'      => (float) $week,
                'quince'    => (float) $quince,
                'month'     => (float) $month,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
