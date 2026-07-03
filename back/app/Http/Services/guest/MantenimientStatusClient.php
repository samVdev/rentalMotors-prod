<?php

namespace App\Http\Services\guest;

use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MantenimientStatusClient
{
    public static function execute(Request $request, $id): JsonResponse
    {
        $userId = Auth::id(); 

        $maintenance = Maintenance::with(['application', 'financing'])->find($id);

        if (!$maintenance) {
            return response()->json(['message' => 'No se encontró el mantenimiento solicitado.'], 404);
        }

        $isOwner = false;

        if ($maintenance->type == 1) {
            $isOwner = ($maintenance->financing && $maintenance->financing->user_id === $userId);
        } elseif ($maintenance->type == 2) {
            $isOwner = ($maintenance->application && $maintenance->application->user_id === $userId);
        }

        if (!$isOwner) {
            return response()->json(['message' => 'No se encontró el mantenimiento solicitado.'], 404);
        }

        $maintenance->update([
            'status' => 'checking'
        ]);

        return response()->json([
            'message' => 'El estado del mantenimiento ha sido actualizado a revisión.',
        ], 200);
    }
}