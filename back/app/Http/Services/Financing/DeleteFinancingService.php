<?php

namespace App\Http\Services\Financing;

use Illuminate\Http\JsonResponse;
use App\Models\Financing;
use Illuminate\Support\Facades\Storage;

class DeleteFinancingService
{
    static public function execute(string $id): JsonResponse
    {
        try {
            $financing = Financing::with('application')->find($id);

            if (!$financing) {
                return response()->json(['message' => 'Financiamiento no encontrado'], 404);
            }

            if ($financing->application) {
                $folderPath = 'applications/' . $financing->application->vehicle_id . '/' . $financing->application->folder;
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
            }
            else {
                $folderPath = 'financings/' . $financing->id;
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
            }

            $financing->delete();

            return response()->json([
                'message' => 'Financiamiento eliminado con éxito'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el financiamiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
