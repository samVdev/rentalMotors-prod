<?php

namespace App\Http\Services\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Maintenance;

class IndexMaintenanceService
{
    public static function index(Request $request): JsonResponse
    {
        $offset = $request->input('offset', 0);
        $limit  = $request->input('limit', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Maintenance::query()
            ->leftJoin('financings', 'maintenances.financing_id', '=', 'financings.id')
            ->leftJoin('applications', 'maintenances.application_id', '=', 'applications.id')
            ->leftJoin('vehicles', 'vehicles.id', '=', \DB::raw(
                'COALESCE(applications.vehicle_id, financings.vehicle_id)'
            ))
            ->leftJoin('users', 'users.id', '=', \DB::raw(
                'COALESCE(applications.user_id, financings.user_id)'
            ))
            ->leftJoin('personas', 'users.persona_id', '=', 'personas.id')
            ->select(
                'maintenances.id',
                'maintenances.total',
                'maintenances.status',
                'maintenances.type',
                'maintenances.descripcion',
                'maintenances.date',

                'vehicles.brand as vehicle_brand',
                'vehicles.model as vehicle_model',

                'personas.fullName as client_name',
                'personas.cedula',
                'personas.phone'
            )
            ->orderBy('maintenances.id', 'desc');


        if ($status) {
            $query->where('maintenances.status', $status);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('applications.cedula', 'ilike', "%$search%")
                    ->orWhere('vehicles.brand', 'ilike', "%$search%")
                    ->orWhere('vehicles.model', 'ilike', "%$search%");
            });
        }

        $maintenances = $query
            ->skip($offset)
            ->take($limit)
            ->get();

        $maintenances = $maintenances->map(function ($m) {
            return [
                'id'           => $m->id,
                'vehicle'      => trim($m->vehicle_brand . ' ' . $m->vehicle_model),
                'date'         => $m->date ? $m->date->format('d/m/Y') : null,
                'cost'         => (float) $m->total,
                'client'       => $m->client_name,
                'type'         => $m->type,
                'descripcion'  => $m->descripcion,
                'status'       => $m->status,
                'phone'        => $m->phone,
                'cedula'       => $m->cedula,
            ];
        });

        return response()->json([
            'rows'      => $maintenances,
            'offset'    => $offset,
            'limit'     => $limit,
            'sort'      => '',
            'direction' => '',
            'search'    => $search
        ]);
    }
}
