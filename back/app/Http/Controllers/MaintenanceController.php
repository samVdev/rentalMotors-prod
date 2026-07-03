<?php

namespace App\Http\Controllers;

use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Http\Services\Maintenance\IndexMaintenanceService;
use App\Http\Services\Maintenance\MaintenanceCheckTypeService;
use App\Http\Services\Maintenance\MaintenanceDeleteService;
use App\Http\Services\Maintenance\MaintenanceShowService;
use App\Http\Services\Maintenance\MaintenanceStoreService;
use App\Http\Services\Maintenance\MaintenanceUpdateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaintenanceController extends Controller
{

    public function index(Request $request)
    {
        return IndexMaintenanceService::index($request);
    }

    public function show(string $id)
    {
        return MaintenanceShowService::execute($id);
    }

    public function getFinancingsOrApplys(string $id, string $cedula)
    {
        return MaintenanceCheckTypeService::execute($id, $cedula);
    }

    public function store(StoreMaintenanceRequest $request)
    {
        return MaintenanceStoreService::execute($request);
    }

    public function update(StoreMaintenanceRequest $request, string $id): JsonResponse
    {
        return MaintenanceUpdateService::execute($request, $id);
    }

    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        return MaintenanceUpdateService::toggleStatus($request, $id);
    }

    public function destroy(string $id)
    {
        return MaintenanceDeleteService::execute($id);
    }
}
