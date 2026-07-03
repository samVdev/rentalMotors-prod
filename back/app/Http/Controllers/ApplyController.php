<?php

namespace App\Http\Controllers;

// services

use App\Http\Requests\Application\FormDocRequest;
use App\Http\Services\Financing\UpdateRequirementsService;
use App\Http\Services\Application\AttachPaymentDocumentService;
use App\Http\Services\Application\IndexApplicationService;
use App\Http\Services\Application\InvoiceService;
use App\Http\Services\Application\StatusApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApplyController extends Controller
{

     public function index(Request $request): JsonResponse
     {
          return IndexApplicationService::index($request);
     }

     public function action(Request $request, $id)
     {
         $validated = $request->validate([
             'value' => 'required|boolean',
         ]);
 
         $result = StatusApplicationService::updateStatus($id, $validated['value']);
 
         return response()->json($result, $result['success'] ? 200 : 409);
     }

     public function attach(FormDocRequest $request, $id): JsonResponse
     {
          return AttachPaymentDocumentService::execute($request, $id);
     }

     public function invoice($id): Response
     {
          return InvoiceService::generate((int) $id);
     }

     public function updateRequirements(Request $request, $id): JsonResponse
     {
          return UpdateRequirementsService::execute($request, (int) $id);
     }
}
