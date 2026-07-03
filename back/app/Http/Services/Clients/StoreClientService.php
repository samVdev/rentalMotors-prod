<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Client\StoreClientRequest;
use App\Models\Financing;
use App\Models\User;
use App\Models\Personas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class StoreClientService
{
    static public function execute(StoreClientRequest $request): JsonResponse
    {

        // INUTILIZADO (YA NO SE USA ESTO EN EL SISTEMA)
        try {
            $financing = null;
            if ($request->apply_id) {
                $financing = Financing::where('application_id', $request->apply_id)->first();
            }

            if (!$financing || !$financing->payment_initial) {
                return response()->json([
                    'message' => 'No se puede crear el cliente porque la financiación no tiene pago inicial registrado.'
                ], 400);
            }


            $persona = new Personas();
            $persona->fullName = $request->name;
            $persona->phone = $request->phone;
            $persona->cedula = $request->cedula;
            $persona->date = $request->dateN;
            $persona->direction = $request->dir;
            $persona->earnings_month = $request->earnings;


            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = 'per_' . $request->cedula . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('clients', $filename, 'private');

                $exists = Storage::disk('private')->exists('clients/' . $filename);

                if (!$exists) {
                    return response()->json(['message' => 'Error: El archivo no se pudo confirmar en disco'], 500);
                }

                $pathFile = "private/clients/" . $filename;

                $encryptedPath = Crypt::encryptString($pathFile);

                $persona->image = $encryptedPath;
            }

            $persona->save();

            $user = new User();
            $user->username = $financing->code;
            if ($request->email) {
                $user->email = $request->email;
            }
            $user->password = Hash::make($request->cedula);
            $user->role_id = 3;
            $user->is_admin = false;
            $user->persona_id = $persona->id;
            $user->suspend = false;
            $user->save();

            $financing->user_id = $user->id;
            $financing->status = 'active';
            $financing->save();

            return response()->json([
                "message" => "Cliente creado con éxito y vinculado a la financiación."
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el cliente.'
            ], 500);
        }
    }
}
