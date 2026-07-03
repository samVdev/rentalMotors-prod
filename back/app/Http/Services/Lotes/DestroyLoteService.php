<?php

namespace App\Http\Services\Lotes;

use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestroyLoteService
{
    public static function index(Request $request, int $id): JsonResponse
    {
        try {
            $lote = Lote::findOrFail($id);

            $activeFinancingsCount = $lote->financings()
                ->whereIn('status', ['active', 'pending'])
                ->count();

            if ($activeFinancingsCount > 0) {
                return response()->json([
                    'message' => "No se puede eliminar el lote porque tiene {$activeFinancingsCount} financiaciones activas o pendientes. Por favor, muévelas a otro lote antes de continuar.",
                ], 400); 
            }
            
            $lote->delete();

            return response()->json([
                'message' => 'Lote eliminado correctamente',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el lote'
            ], 500);
        }
    }
}