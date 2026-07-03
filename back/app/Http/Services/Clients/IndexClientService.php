<?php

namespace App\Http\Services\Clients;

//use App\Http\Resources\UserCollection;
//use App\Http\Resources\UserResource;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IndexClientService
{

    static public function execute(Request $request): JsonResponse
    {
        try {
            $offset = $request->input("offset", 0);
            $limit = $request->input("limit", 10);
            $search = $request->input("search");
            $sort = $request->input("sort");
            $direction = $request->input("direction") == "desc" ? "desc" : "asc";

            $query = User::query()
                ->join('personas', 'users.persona_id', '=', 'personas.id')
                ->select(
                    'fullName',
                    'earnings_month',
                    'date',
                    'cedula',
                    'phone',
                    'users.id',
                    'users.uuid',
                    'users.username',
                )
                ->where([
                    ['role_id', '=', 3],
                    ['users.id', '>', 1],
                    ['fullName', '!=', ''],
                ])
                ->groupBy('fullName', 'phone', 'cedula', 'date', 'earnings_month', 'users.id', 'users.uuid', 'username');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where("personas.fullName", "ilike", "%$search%")
                        ->orWhere("personas.cedula", "ilike", "%$search%")
                        ->orWhere("personas.date", "ilike", "%$search%")
                        ->orWhere("users.username", "ilike", "%$search%")
                        ->orWhere("personas.phone", "ilike", "%$search%");
                });
            }

            if ($sort) {
                $columnMap = [
                    'name' => 'fullName',
                    'username' => 'usuario',
                    'phone' => 'phone',
                    'cedula' => 'cedula',
                    'dateN' => 'date',
                    'earnings' => 'earnings_month',
                ];
                if (isset($columnMap[$sort])) {
                    $query->orderBy($columnMap[$sort], $direction);
                }
            }

            $users = $query->skip($offset)->take($limit)->get();

            $users = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'usuario' => $user->username,
                    'nombre' => $user->fullName,
                    'email' => $user->email,
                    'cedula' => $user->cedula,
                    'dateN' => $user->date,
                    'phone' => $user->phone,
                    'earnings' => $user->earnings_month,
                ];
            });

            // Retornar la respuesta con paginación
            return response()->json([
                "rows" => $users,
                "offset" => $offset,
                "limit" => $limit,
                "sort" => $sort,
                "direction" => $direction,
                "search" => $search
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}
