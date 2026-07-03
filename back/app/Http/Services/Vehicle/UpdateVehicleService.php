<?php

namespace App\Http\Services\Vehicle;

use App\Http\Requests\Vehicle\VehicleEditRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;

class UpdateVehicleService
{
    static public function execute(VehicleEditRequest $request, string $id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehículo no encontrado'], 404);
            }

            $vehicle->brand= $request->marca;
            $vehicle->model= $request->modelo;
            $vehicle->year= $request->year;
            $vehicle->cc= $request->cc;
            $vehicle->color= $request->color;
            $vehicle->price= $request->precio;
            $vehicle->mileage = $request->kilometraje;
            $vehicle->type= $request->type;
            $vehicle->show= $request->show ?? false;

            if ($request->hasFile('file')) {
                if ($vehicle->image && Storage::exists($vehicle->image)) {
                    Storage::delete($vehicle->image);
                }
    
                $filename = 'vehicle_' . time() . '.' . $request->file('file')->getClientOriginalExtension();
                $request->file('file')->storeAs('vehicles', $filename, 'public');
                $vehicle->image = "public/vehicles/" . $filename;
            }

            $vehicle->save();

            return response()->json(["message" => "Vehículo actualizado con éxito"], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el vehículo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
