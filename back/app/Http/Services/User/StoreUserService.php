<?php

namespace App\Http\Services\User;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use App\Models\Personas;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreUserService
{
    static public function execute(StoreUserRequest $request): JsonResponse
    {
        try {
            // Usamos una transacción para asegurar que se cree todo o nada
            return DB::transaction(function () use ($request) {
                
                $persona = new Personas();
                $persona->fullName = $request->name;
                $persona->phone = $request->phone;
                $persona->cedula = $request->cedula;
                $persona->date = $request->dateN;
                $persona->direction = $request->dir;
                $persona->save();

                $role = Role::find($request->role_id);

                $isAdmin = false;

                if ($role && $role->created_admin) {
                    $isAdmin = $role->created_admin;
                }
            
                $user = new User();
                $user->email = $request->email;
                $user->username = $request->usuario;
                $user->password = Hash::make($request->password);
                $user->role_id = $request->role_id;
                $user->is_admin = $isAdmin ?? false;
                $user->persona_id = $persona->id;
                $user->suspend = false;
                $user->save();

                if ($request->has('lotes')) {
                    $user->lotes()->attach($request->lotes);
                }
            
                return response()->json(["message" => "Se ha creado con éxito"], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }        
    }
}