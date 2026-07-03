<?php

namespace App\Http\Services\Service;

use App\Models\Service;
use Illuminate\Http\JsonResponse;

class IndexService
{
    /**
     * Get all services available.
     */
    static public function execute(): JsonResponse
    {
        try {
            $services = Service::select('id', 'name', 'description')->get();

            return response()->json($services, 200);
        }
        catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los servicios',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}