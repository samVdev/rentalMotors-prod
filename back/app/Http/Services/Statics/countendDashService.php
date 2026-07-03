<?php

namespace App\Http\Services\Statics;

use App\Models\Financing;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class countendDashService
{
    static public function execute(Request $request): JsonResponse
    {
        try {

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $clientsCount = User::where('role_id', 3)->count();
            $bikesCount = Financing::where('status', 'active')
                ->whereIn('lote_id', $lotesIds)
                ->whereHas('vehicle', function($q) {
                    $q->where('type', 'bike');
                })->count();
            $financingCount = Financing::where('status', 'active')->whereIn('lote_id', $lotesIds)->count();

            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $earnings = Payment::where('status', 'approved')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereIn('lote_id', $lotesIds);
                })
                ->sum('total');

            $maintenanceCount = Maintenance::where('status', 'pending')->count();

            return response()->json([
                "clients" => $clientsCount,
                "bikes" => $bikesCount,
                "earnings" => $earnings ? (float) $earnings : 0,
                "activesPayment" => $financingCount,
                "manteniment" => $maintenanceCount,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}
