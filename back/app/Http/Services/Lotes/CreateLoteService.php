<?php

namespace App\Http\Services\Lotes;

use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateLoteService
{
public static function index(Request $request): JsonResponse
{
    try {
        $messages = [
            'name.required' => 'El nombre del lote es obligatorio.',
            'name.string'   => 'El nombre debe ser una cadena de texto.',
            'name.max'      => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique'   => 'Este nombre de lote ya existe.',
        ];

        $request->validate([
            'name' => 'required|string|max:255|unique:lotes,nombre'
        ], $messages);

        Lote::create([
            'nombre' => $request->name,
        ]);

        return response()->json([
            'message' => 'Lote creado correctamente',
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Ocurrió un error al crear el lote'
        ], 500);
    }
}
}