<?php

namespace App\Http\Services\guest;

use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class getVehiclesMin
{
    public static function index(Request $request, string $type): JsonResponse
    {
        try {
            $search = $request->input("search");

            $vehicles = Vehicle::select('id', 'brand', 'model', 'image', 'price')
                ->where('show', true)
                ->when($type, function ($query, $type) {
                    $query->where('type', $type === 'cars' ? 'car' : 'bike');
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('model', 'ilike', "%{$search}%")
                          ->orWhere('brand', 'ilike', "%{$search}%");
                    });
                })
                ->get();

            $data = $vehicles->map(fn($vehicle) => [
                'id' => $vehicle->id,
                'title' => $vehicle->brand,
                'description' => $vehicle->model,
                'image' => $vehicle->image,
                'price' => number_format($vehicle->price, 2, ',', '.'),
            ]);

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Ocurrió un error al obtener los vehículos",
                "error" => $e->getMessage(), // opcional para debug
            ], 500);
        }
    }
}
