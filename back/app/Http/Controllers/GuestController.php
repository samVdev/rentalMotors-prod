<?php

namespace App\Http\Controllers;

use App\Http\Requests\guest\ApplicationRequest;
use App\Http\Requests\guest\FormPayRequest;
// services
use App\Http\Services\guest\DashboardCount;
use App\Http\Services\guest\getVehiclesMin;
use App\Http\Services\guest\GetActiveAccountMethods;
use App\Http\Services\guest\MantenimientStatusClient;
use App\Http\Services\guest\PaymentStoreService;
use App\Http\Services\guest\storeApplication;
use App\Http\Services\Vehicle\getVehicleService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{

     // rutas publicas

     public function vehicles(Request $request, string $type): JsonResponse
     {
          return getVehiclesMin::index($request, $type);
     }

     public function vehicle(string $id): JsonResponse
     {
          return getVehicleService::show($id);
     }

     public function apply(ApplicationRequest $request): JsonResponse
     {
          return storeApplication::store($request);
     }

     // rutas protegidas

     public function dashboard(): JsonResponse
     {
          return DashboardCount::index();
     }

     public function payment(FormPayRequest $request, $financing_id): JsonResponse
     {
          return PaymentStoreService::execute($request, $financing_id);
     }

     public function mantenimient(Request $request, $id): JsonResponse
     {
          return MantenimientStatusClient::execute($request, $id);
     }

     public function getAccountMethods(): JsonResponse
     {
          return GetActiveAccountMethods::execute();
     }
}