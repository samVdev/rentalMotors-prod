<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehicle\VehicleEditRequest;
use App\Http\Requests\Vehicle\VehicleRequest;
use App\Http\Services\Vehicle\DeleteVehicleService;
use App\Http\Services\Vehicle\getVehicleService;
use App\Http\Services\Vehicle\IndexVehicleMinService;
use App\Http\Services\Vehicle\IndexVehicleService;
use App\Http\Services\Vehicle\StoreVehicleService;
use App\Http\Services\Vehicle\UpdateVehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    public function index(Request $request)
    {
        return IndexVehicleService::execute($request);
    }

    public function indexMin()
    {
        return IndexVehicleMinService::execute();
    }

    public function store(VehicleRequest $request)
    {
        return StoreVehicleService::execute($request);
    }
    public function show(string $id)
    {
        return getVehicleService::show($id);
    }

    public function update(VehicleEditRequest $request, string $id)
    {
        return UpdateVehicleService::execute($request, $id);
    }

    public function destroy(string $id)
    {
        return DeleteVehicleService::execute($id);
    }
}
