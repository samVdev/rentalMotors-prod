<?php

namespace App\Http\Services\Vehicle;

use Illuminate\Http\JsonResponse;
use App\Models\Vehicle;

class IndexVehicleMinService
{
    static public function execute(): JsonResponse
    {
        try {
            $vehicles = Vehicle::select('id', 'model', 'brand', 'image', 'price')->get();

            $vehicles = $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'marca' => $vehicle->brand . ' - ' . $vehicle->model,
                    'img' => $vehicle->image,
                    'precio' => (float) $vehicle->price,
                ];
            });

            return response()->json($vehicles, 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los vehículos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
