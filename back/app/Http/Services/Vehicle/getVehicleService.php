<?php

namespace App\Http\Services\Vehicle;

use Illuminate\Http\JsonResponse;
use App\Models\Vehicle;

class GetVehicleService
{
    static public function show(string $id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehículo no encontrado'], 404);
            }

            $data = [
                'marca' => $vehicle->brand,
                'modelo' => $vehicle->model,
                'image' => $vehicle->image,
                'year' => $vehicle->year,
                'cc' => $vehicle->cc,
                'color' => $vehicle->color,
                'precio' => number_format($vehicle->price, 2, ',', '.'),
                'kilometraje' => $vehicle->mileage,
                'type' => $vehicle->type,
                'show' => (bool)$vehicle->show,
            ];

            return response()->json($data, 200);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los datos del vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}