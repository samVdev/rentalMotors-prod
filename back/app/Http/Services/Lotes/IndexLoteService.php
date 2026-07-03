<?php

namespace App\Http\Services\Lotes;

use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexLoteService
{
    public static function index(Request $request): JsonResponse
    {
        try {
            $search = $request->input('search');
            $direction = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
            
            $query = Lote::withCount('financings');

            if ($search) {
                $query->where('nombre', 'ilike', "%$search%");
            }

            $query->orderBy('financings_count', $direction);

            $lotes = $query->get(['id', 'nombre']);

            return response()->json([
                'rows' => $lotes,
                'offset' => 0,
                'limit' => 10,
                'sort' => 'financings_count',
                'direction' => $direction,
                'search' => $search,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los lotes: ' . $e->getMessage()
            ], 500);
        }
    }
}