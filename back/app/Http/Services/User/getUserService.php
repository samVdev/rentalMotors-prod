<?php

namespace App\Http\Services\User;

use Illuminate\Http\JsonResponse;
use App\Models\User;

class getUserService
{
    static public function index(string $uuid): JsonResponse
    {
        try {
            $userAuth = auth()->user();

            $user = User::with([
                'persona:id,fullName,phone,date,direction,cedula',
                'lotes:id' 
            ])
            ->select('id', 'email', 'persona_id', 'role_id', 'username')
            ->where([
                ['uuid', $uuid], 
                ['id', '>', $userAuth->role_id == 1 ? 0 : 1]
            ])
            ->first();
        
            if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);
        
            $data = [
                "email" => $user->email ?? '',
                "usuario" => $user->username ?? '',
                'name' => $user->persona->fullName ?? '',
                'cedula' => $user->persona->cedula ?? '',
                'dateN' => $user->persona->date ?? '',
                'dir' => $user->persona->direction ?? '',
                'phone' => $user->persona->phone ?? '',
                'role_id' => $user->role_id ?? '0',
                'lotes' => $user->lotes->pluck('id')->toArray() 
            ];
        
            return response()->json($data, 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los datos del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}