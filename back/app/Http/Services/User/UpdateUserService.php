<?php

namespace App\Http\Services\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Personas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    static public function execute(UpdateUserRequest $request, String $uuid): JsonResponse
    {
        try {
            $userAuth = auth()->user();

            return DB::transaction(function () use ($request, $uuid, $userAuth) {
                $data = $request->all();

                $user = User::where([
                    ['uuid', $uuid], 
                    ['id', '>', $userAuth->role_id == 1 ? 0 : 1]
                ])->first();
                
                if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

                if (isset($data["password"]) && $data["password"]) {
                    $user->password = Hash::make($data["password"]);
                }

                $persona = $user->persona;
                if ($persona) {
                    $persona->fullName = $request->name;
                    $persona->phone = $request->phone;
                    $persona->cedula = $request->cedula;
                    $persona->date = $request->dateN;
                    $persona->direction = $request->dir;
                    $persona->save();
                }

                $role = Role::find($request->role_id);

                $isAdmin = false;

                if ($role && $role->created_admin) {
                    $isAdmin = $role->created_admin;
                }

                $user->email = $request->email;
                $user->role_id = $request->role_id;
                $user->is_admin = $isAdmin;
                $user->username = $request->usuario;
                $user->save();

                if ($request->has('lotes')) {
                    $user->lotes()->sync($request->lotes);
                }

                return response()->json(["message" => "Usuario actualizado con éxito"], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}