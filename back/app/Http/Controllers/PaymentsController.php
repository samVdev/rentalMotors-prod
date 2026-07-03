<?php

namespace App\Http\Controllers;

// services

use App\Http\Services\Payments\IndexPaymentsService;
use App\Http\Services\Payments\ResumenMoneyService;
use App\Http\Services\Payments\StatusPaymentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

     public function index(Request $request): JsonResponse
     {
          return IndexPaymentsService::index($request);
     }

     public function action(Request $request, $id)
     {
         $validated = $request->validate([
             'value' => 'required|boolean',
         ]);
 
         $result = StatusPaymentsService::updateStatus($id, $validated['value']);
 
         return response()->json($result, $result['success'] ? 200 : 500);
     }

     public function resume(): JsonResponse
     {
          return ResumenMoneyService::index();
     }
}
