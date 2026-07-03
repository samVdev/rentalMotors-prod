<?php

namespace App\Http\Services\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Vehicle;

class IndexVehicleService
{
    static public function execute(Request $request): JsonResponse
    {
        try {
            $offset = $request->input("offset", 0);
            $limit = $request->input("limit", 10);
            $search = $request->input("search");
            $type = $request->input("type", 1) == 1 ? 'bike' : 'car';
            $sort = $request->input("sort");
            $direction = $request->input("direction") === "desc" ? "desc" : "asc";

            $query = Vehicle::query()
                ->select(
                    'id',
                    'brand',
                    'model',
                    'image',
                    'year',
                    'cc',
                    'color',
                    'price',
                    'mileage',
                )->where('type', $type);

            // Filtros de búsqueda
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where("brand", "ilike", "%$search%")
                      ->orWhere("model", "ilike", "%$search%")
                      ->orWhere("cc", "ilike", "%$search%")
                      ->orWhere("color", "ilike", "%$search%")
                      ->orWhere("year", "ilike", "%$search%");
                });
            }

            // Ordenamiento dinámico
            /*if ($sort) {
                $columnMap = [
                    'brand' => 'brand',
                    'model' => 'model',
                    'year' => 'year',
                    'price' => 'price',
                    'mileage' => 'mileage',
                    'type' => 'type',
                ];

                if (isset($columnMap[$sort])) {
                    $query->orderBy($columnMap[$sort], $direction);
                }
            }*/

            $vehicles = $query->skip($offset)->take($limit)->get();

            $vehicles = $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'marca' => $vehicle->brand,
                    'modelo' => $vehicle->model,
                    'year' => $vehicle->year,
                    'cc' => $vehicle->cc,
                    'color' => $vehicle->color,
                    'precio' => $vehicle->price,
                    'kilometraje' => $vehicle->mileage,
                    'image' => $vehicle->image,
                ];
            });

            return response()->json([
                "rows" => $vehicles,
                "offset" => $offset,
                "limit" => $limit,
                "sort" => $sort,
                "direction" => $direction,
                "search" => $search
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
