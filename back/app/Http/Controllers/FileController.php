<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class FileController extends Controller
{
    public function index($path)
    {
        try {
            $user = auth()->user();
            if (!$user || $user->role_id == 3) {
                abort(403, 'No autorizado');
            }

            try {
                $realPath = Crypt::decryptString($path);
            } catch (DecryptException $e) {
                return response()->json(['message' => 'Token de archivo inválido'], 400);
            }

            if (!Storage::disk('private')->exists($realPath)) {
                return response()->json([
                    'message' => 'El archivo no existe en el servidor'
                ], 404);
            }

            return Storage::disk('private')->download($realPath);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno al procesar la solicitud'
            ], 500);
        }
    }


    public function indexClient($path)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                abort(403, 'No autorizado');
            }

            $pathDB = $user->persona->image;

            if ($pathDB) {
                $pathDB = hash('sha256', $user->persona->image);
            }

            if ($path != $pathDB) {
                abort(403, 'No autorizado');
            }

            $realPath = Crypt::decryptString($user->persona->image);


            if (!Storage::disk('private')->exists($realPath)) {
                return response()->json([
                    'message' => 'no se pudo cargar'
                ], 404);
            }

            return Storage::disk('private')->download($realPath);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'no se pudo cargar'
            ], 500);
        }
    }
}
