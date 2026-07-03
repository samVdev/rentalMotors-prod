<?php

namespace App\Http\Controllers;

use App\Http\Requests\Financing\FinancingFormEditRequest;
use App\Http\Requests\Financing\FinancingFormRequest;
use App\Models\Financing;

use App\Http\Services\Financing\DeleteFinancingService;
use App\Http\Services\Financing\EditFinancingService;
use App\Http\Services\Financing\IndexFinancingService;
use App\Http\Services\Financing\ResumenFinancingService;
use App\Http\Services\Financing\showFinancingService;
use App\Http\Services\Financing\getFinancingService;
use App\Http\Services\Financing\MoraStatusFinancingService;
use App\Http\Services\Financing\StoreFinancingService;
use App\Http\Services\Financing\UpdatePlacaFinancingService;
use App\Http\Services\Financing\UpdateRequirementsService;
use App\Http\Services\Financing\UpdateFinanceDetailsService;
use App\Http\Requests\Financing\UpdateFinanceDetailsRequest;
use App\Http\Services\Application\InvoiceService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FinancingController extends Controller
{

    public function index(Request $request)
    {
        return IndexFinancingService::execute($request);
    }

    public function resume(): JsonResponse
    {
        return ResumenFinancingService::index();
    }

    public function show(string $id)
    {
        return showFinancingService::execute($id);
    }

    public function getOne(string $id)
    {
        return getFinancingService::execute($id);
    }

    public function store(FinancingFormRequest $request)
    {
        return StoreFinancingService::execute($request);
    }

    public function update(FinancingFormEditRequest $request, string $id): JsonResponse
    {
        return EditFinancingService::execute($request, $id);
    }

    public function moraStatus(string $id): JsonResponse
    {
        return MoraStatusFinancingService::execute($id);
    }

    public function destroy(string $id)
    {
        return DeleteFinancingService::execute($id);
    }

    public function updatePlaca(Request $request, string $id): JsonResponse
    {
        return UpdatePlacaFinancingService::execute($request, $id);
    }

    public function invoice(string $id): Response
    {
        return InvoiceService::generate((int) $id);
    }

    public function updateRequirements(Request $request, string $id): JsonResponse
    {
        $financing = Financing::findOrFail($id);
        return UpdateRequirementsService::execute($request, (int) $financing->application_id);
    }

    public function updateFinanceDetails(UpdateFinanceDetailsRequest $request, string $id): JsonResponse
    {
        return UpdateFinanceDetailsService::execute($request, $id);
    }
}
