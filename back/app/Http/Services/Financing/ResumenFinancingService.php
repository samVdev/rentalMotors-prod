<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use App\Models\Financing;

class ResumenFinancingService
{
    public static function index(): JsonResponse
    {
        try {

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $financingPrice = Financing::where('status', '!=', 'pending')->whereIn('lote_id', $lotesIds)->sum('financing_price');

            $totalPayments = Payment::where('status', 'approved')
                ->whereHas('financing', function ($q) use ($lotesIds) {
                    $q->whereNotNull('user_id')
                        ->whereIn('lote_id', $lotesIds);
                });

            $totalPaidInstallments = (clone $totalPayments)->where('installment_number', '>', '0')->sum('total');
            $totalPaidMora = (clone $totalPayments)->whereNotNull('mora_id')->sum('total');
            $totalPaidCombined = (clone $totalPayments)->sum('total');

            $pending = $financingTotal - $totalPaidInstallments;

            return response()->json([
                'financing' => (float) $financingTotal,
                'pending' => (float) $pending,
                'total' => (float) $totalPaidCombined,
                'total_mora' => (float) $totalPaidMora,
                'capital' => (float) $financingPrice
            ]);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


/*

<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use App\Models\Financing;

class ResumenFinancingService
{
    public static function index(): JsonResponse
    {
        try {

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $financingTotal = Financing::where('status', '!=', 'pending')->whereIn('lote_id', $lotesIds)->sum('financing_price');

            $totalPayments = Payment::where('status', 'approved')
            ->where('installment_number', '>', '0')
            ->whereHas('financing', function ($q) use ($lotesIds) {
                $q->whereNotNull('user_id')
                ->whereIn('lote_id', $lotesIds);
            });

            $totalPaid = $totalPayments->sum('total');
            $totalPaidMora = $totalPayments->sum('mora_paymented');

            $pending = $financingTotal - $totalPaid;

                return response()->json([
                    'financing' => (float) $financingTotal,
                    'pending'   => (float) $pending,
                    'total'     => (float) $totalPaid,
                    'total_mora'     => (float) $totalPaidMora,
                    'capital' => 
                ]);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

*/