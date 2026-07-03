<?php

namespace App\Http\Services\User;

use Illuminate\Http\JsonResponse;
use App\Models\User;

class IndexUserMinService
{

    static public function execute(string $role): JsonResponse
    {
        try {
            $role_id = null;

            if ($role == 'admin')
                $role_id = 2;
            else if ($role == 'cliente')
                $role_id = 3;
            else if ($role == 'trabajador')
                $role_id = 4;
            else
                return response()->json(['message' => 'Rol no válido'], 400);

            $query = User::query()
                ->join('personas', 'users.persona_id', '=', 'personas.id')
                ->select(
                'fullName',
                'users.id',
            )
                ->where('users.id', '>', 1)
                ->where('users.role_id', $role_id == 1 ? '<=' : '=', $role_id)->get();

            $users = $query->map(function ($user) {
                return [
                'id' => $user->id,
                'name' => $user->fullName,
                ];
            });

            return response()->json($users);
        }
        catch (\Throwable $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}