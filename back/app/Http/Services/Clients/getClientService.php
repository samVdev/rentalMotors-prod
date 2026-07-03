<?php

namespace App\Http\Services\Clients;

use App\Models\Financing;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;


class getClientService
{
    static public function index(Request $request, string $id): JsonResponse
    {
        try {
            $hasApply = $request->apply_id ? true : false;

            $user = User::with(['persona' => function ($query) {
                $query->select('id', 'fullName', 'phone', 'date', 'direction', 'cedula', 'earnings_month', 'image');
            }])
                ->select('email', 'persona_id', 'role_id', 'username')
                ->where('id', $id)
                ->first();

            if (!$user)
                return response()->json(['message' => 'Cliente no encontrado'], 404);

            $data = [
                'name' => $user->persona->fullName ?? '',
                'cedula' => $user->persona->cedula ?? '',
                'dateN' => $user->persona->date ?? '',
                'dir' => $user->persona->direction ?? '',
                'phone' => $user->persona->phone ?? '',
                'email' => $user->email ?? '',
                'usuario' => $user->username ?? '',
                'earnings' => $user->persona->earnings_month ?? '',
                'imageApp' => $user->persona->image ?? null,
            ];

            if ($hasApply) {
                $financing = Financing::where('application_id', $request->apply_id)->first();

                if ($financing) {
                    $data['email'] = '';
                    $data['usuario'] = $financing->code;
                }
            }


            return response()->json($data, 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al obtener los datos del cliente'
            ], 500);
        }
    }
}