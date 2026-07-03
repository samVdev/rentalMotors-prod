<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};

use App\Http\Services\Statics\{
    countendDashService,
    IndexFinancingDashService,
    IndexPaymentsDashService
};

class StaticsController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        return countendDashService::execute($request);
    }


    public function payments(): JsonResponse
    {
        return IndexPaymentsDashService::index();
    }

    public function financing(): JsonResponse
    {
        return IndexFinancingDashService::execute();
    }
}
