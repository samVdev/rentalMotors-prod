<?php

namespace App\Http\Services\Vehicle;

use Illuminate\Http\JsonResponse;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;

class DeleteVehicleService
{
    static public function execute(string $id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehículo no encontrado'], 404);
            }

            if ($vehicle->image && Storage::exists($vehicle->image)) {
                Storage::delete($vehicle->image);
            }

            $vehicle->delete();

            return response()->json(["message" => "Vehículo eliminado con éxito"], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el vehículo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
