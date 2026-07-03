<?php

namespace App\Http\Services\Application;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Application;

class IndexApplicationService
{
    static public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->input("offset", 0);
            $limit = $request->input("limit", 10);
            $status = $request->input("type", 10);
            $search = $request->input("search");
            $user = $request->input("user");

            if ($user) {
                $status = 'accept';
            }

            $query = Application::query()
                ->join('vehicles', 'applications.vehicle_id', '=', 'vehicles.id')
                ->leftjoin('financings', 'financings.application_id', '=', 'applications.id')
                ->select(
                'applications.id as application_id',
                'applications.full_name',
                'applications.phone',
                'applications.cedula',
                'applications.type',
                'applications.plan',
                'vehicles.model as vehicle_model',
                'vehicles.image',
                'vehicles.price',
                'financings.payment_initial',
                'applications.user_id',
                'financings.status',
                'financings.code'
            )
                ->where('applications.status', $status)
                ->orderBy('applications.id', 'desc');


            if ($user) {
                $query->where('applications.user_id', $user);
            }

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where("applications.cedula", "ilike", "%$search%")
                        ->orwhere("applications.code", "ilike", "%$search%");
                });
            }


            $applications = $query->skip($offset)->take($limit)->get();

            $applications = $applications->map(function ($app) {
                return [
                'id' => $app->application_id,
                'date' => $app->created_at->format('d/m/Y'),
                'phone' => $app->phone,
                'cedula' => $app->cedula,
                'full_name' => $app->full_name,
                'financiacion_type' => $app->type == 'cash' ? 'De contado' : 'financiación',
                'plan' => $app->plan,
                'document' => $app->document_pdf,
                'vehicle_brand' => $app->vehicle_name,
                'vehicle_model' => $app->vehicle_model,
                'precio' => (float)$app->price,
                'vehicle_image' => $app->image,
                'pay_one' => $app->payment_initial,
                'estatus' => $app->status == 'pending',
                'client_id' => $app->user_id,
                'codigo' => $app->code
                ];
            });

            return response()->json([
                "rows" => $applications,
                "offset" => $offset,
                "limit" => $limit,
                "sort" => '',
                "direction" => '',
                "search" => ''
            ]);
        }
        catch (\Throwable $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}