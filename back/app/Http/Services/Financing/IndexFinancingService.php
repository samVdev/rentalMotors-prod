<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Financing;

class IndexFinancingService
{
    static public function execute(Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $search = $request->input('search');
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'desc') === 'desc' ? 'desc' : 'asc';

            $user = auth()->user();
            $lotesIds = $user->lotes->pluck('id')->toArray();

            $query = Financing::query()
                ->select(
                    'id',
                    'vehicle_id',
                    'user_id',
                    'lote_id',
                    'plate',
                    'responsable_id',
                    'type',
                    'installments',
                    'start_date',
                    'status',
                    'code'
                )
                ->whereIn('lote_id', $lotesIds)
                ->whereNotNull('user_id')
                ->with([
                    'vehicle:id,brand,model',
                    'user:id,persona_id',
                    'user.persona:id,fullName',
                    'responsable:id,persona_id',
                    'responsable.persona:id,fullName',
                    'lote:id,nombre'
                ]);

            if ($search) {
                $query->whereHas('user.persona', function ($q) use ($search) {
                    $q->where('fullName', 'ilike', "%$search%");
                })->orWhereHas('vehicle', function ($q) use ($search) {
                    $q->where('brand', 'ilike', "%$search%")
                      ->orWhere('model', 'ilike', "%$search%");
                })
                ->orWhereHas('lote', function ($q) use ($search) {
                    $q->where('nombre', 'ilike', "%$search%");
                })
                ->orWhere('plate', 'ilike', "%$search%")
                ->orWhere('code', 'ilike', "%$search%");
            }

            $validSorts = ['id', 'type', 'installments', 'start_date'];
            if (in_array($sort, $validSorts)) {
                $query->orderBy($sort, $direction);
            }

            $financings = $query->skip($offset)->take($limit)->get();

            $financings = $financings->map(function ($f) {
                return [
                    'id' => $f->id,
                    'cliente' => $f->user->persona->fullName ?? 'N/A',
                    'codigo' => $f->code ?? 'N/A',
                    'tipo' => $f->type ?? 'Sin data',
                    'cuotas' => $f->installments ?? 0,
                    'fecha_inicio' => $f->start_date ?? 'Sin data',
                    'status' => $f->status ?? 'Sin data',
                    'responsable' => $f->responsable->persona->fullName ?? 'N/A',
                    'lote' => $f->lote->nombre ?? 'N/A',
                ];
            });

            return response()->json([
                'rows' => $financings,
                'offset' => $offset,
                'limit' => $limit,
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar los financiamientos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
