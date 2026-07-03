<?php

namespace App\Http\Services\Vehicle;

use App\Http\Requests\Vehicle\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class StoreVehicleService
{
    public static function execute(VehicleRequest $request): JsonResponse
    {
        try {
            $vehicle = new Vehicle();
            $vehicle->brand = $request->marca;
            $vehicle->model = $request->modelo;
            $vehicle->year = $request->year;
            $vehicle->cc = $request->cc;
            $vehicle->color = $request->color;
            $vehicle->price = $request->precio;
            $vehicle->mileage = $request->kilometraje;
            $vehicle->type = $request->type;
            $vehicle->show = $request->show ?? false;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = 'vehicle_' . time() . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('vehicles', $filename, 'public');

                $exists = \Storage::disk('public')->exists('vehicles/' . $filename);

                if (!$exists) {
                    return response()->json(['message' => 'Error: El archivo no se pudo confirmar en disco'], 500);
                }

                $vehicle->image = "public/vehicles/" . $filename;
            }

            $vehicle->save();
            return response()->json([
                'message' => 'El vehículo se ha creado con éxito',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el vehículo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
