<?php

namespace App\Http\Services\Application;

use App\Models\Application;
use App\Models\Financing;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Personas;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\Utils\WhatsAppService;

class StatusApplicationService
{
    public static function updateStatus(int $id, bool $value): array
    {
        try {
            DB::beginTransaction();

            $application = Application::findOrFail($id);
            $application->status = $value ? 'accept' : 'reject';

            if ($value) {
                $vehicle = $application->vehicle;
                $div_ci = explode('-', $application->cedula);
                $cedula_limpia = end($div_ci);

                $persona = Personas::firstOrCreate(
                    ['cedula' => $cedula_limpia],
                    [
                        'fullName'       => $application->full_name,
                        'phone'          => $application->phone,
                        'date'           => '1999-12-12',
                        'direction'      => $application->direccion ?? '',
                        'earnings_month' => ''
                    ]
                );

                $user = User::firstOrCreate(
                    ['username' => $cedula_limpia],
                    [
                        'email'      => $cedula_limpia . '@test.ext',
                        'password'   => Hash::make($cedula_limpia),
                        'role_id'    => 3,
                        'is_admin'   => false,
                        'persona_id' => $persona->id,
                        'suspend'    => false
                    ]
                );

                if ($application->type != 'cash') {
                    $financing = Financing::firstOrCreate(
                        ['application_id' => $application->id],
                        [
                            'plan'         => $application->plan,
                            'type'         => 'vehicle',
                            'status'       => 'pending',
                            'vehicle_id'   => $vehicle->id,
                            'cost_price'   => $vehicle->price,
                            'user_id'      => $user->id,
                            'mora'         => 0
                        ]
                    );
                }

                $application->user_id = $user->id;

                $wa = app(WhatsAppService::class);
                $wa->sendTemplate(
                    $application->phone,
                    "new_application",
                    [$application->full_name],
                    'es_CO'
                );
            } else {
                $financing = Financing::where('application_id', $application->id)->first();
                if ($financing) {
                    if ($financing->status != 'pending') {
                        return [
                            'message' => "La solicitud no se puede alterar, porque ya se acepto la financiación",
                            'success' => false,
                        ];
                    }

                    $decryptedPath = Crypt::decryptString($financing->payment_initial);

                    if ($financing->payment_initial && Storage::disk('private')->exists($decryptedPath)) {
                        Storage::disk('private')->delete($decryptedPath);
                    }

                    $financing->delete();

                    if ($application->user_id) {
                        $user = User::find($application->user_id);
                        if ($user) {
                            $persona = $user->persona;
                            $user->delete();
                            $persona?->delete();
                        }
                    }
                }
                $application->user_id = null;
            }

            $application->save();
            DB::commit();

            return [
                'success' => true,
                'message' => "Solicitud " . ($value ? 'aceptada' : 'rechazada') . " correctamente.",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => "No se pudo actualizar el estado.",
                'error' => $e->getMessage(),
            ];
        }
    }
}
