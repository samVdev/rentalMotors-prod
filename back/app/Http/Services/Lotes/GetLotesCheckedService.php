<?php

namespace App\Http\Services\Lotes;

use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetLotesCheckedService
{
    public static function index(Request $request): JsonResponse
    {
        try {
            $lotes = Lote::withCount(['financings' => function ($query) {
                $query->whereIn('status', ['active', 'pending']);
            }])->get();

            $data = $lotes->map(function ($lote) {
                return [
                    'id' => $lote->id,
                    'nombre' => $lote->nombre,
                    'count' => $lote->financings_count,
                ];
            });

            return response()->json($data, 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener los lotes'
            ], 500);
        }
    }
}