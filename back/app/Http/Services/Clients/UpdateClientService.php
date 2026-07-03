<?php

namespace App\Http\Services\Clients;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Financing;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;


class UpdateClientService
{
    static public function execute(UpdateClientRequest $request, String $id): JsonResponse
    {
        try {
            $user = User::where('id', $id)->first();
            $financing = null;
            $apply_id = $request->input("apply_id", 0);

            if (!$user) return response()->json(['message' => 'Cliente no encontrado'], 404);

            $persona = $user->persona;

            if ($persona) {
                $persona->fullName = $request->name;
                $persona->phone = $request->phone;
                $persona->cedula = $request->cedula;
                $persona->date = $request->dateN;
                $persona->direction = $request->dir;
                $persona->earnings_month = $request->earnings || '';

                if ($request->hasFile('file')) {
                    if ($persona->image) {
                        $decryptedPath = Crypt::decryptString($persona->image);
                        if (Storage::disk('private')->exists($decryptedPath)) {
                            Storage::disk('private')->delete($decryptedPath);
                        }
                    }
                    $file = $request->file('file');
                    $filename = 'client_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('clients', $filename, 'private');
                    $persona->image = Crypt::encryptString($path);
                }

                $persona->save();
            }

            $user->username = $request->usuario;

            if ($request->has('email') && is_string($request->email)) {
                $user->email = $request->email;
            }

            $user->role_id = 3;
            $user->is_admin = false;
            $user->suspend = false;
            $user->save();

            if ($apply_id) {
                $financing = Financing::where('application_id', $request->apply_id)->first();

                if ($financing) {

                    if (!$financing->payment_initial) {
                        return response()->json([
                            'message' => 'No se puede crear el cliente porque la financiación no tiene pago inicial registrado.'
                        ], 400);
                    }
                    $financing->status = 'active';
                    $financing->save();
                }
            }

            return response()->json(["message" => "Cliente actualizado con exito"], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el cliente'
            ], 500);
        }
    }
}
