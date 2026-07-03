<?php

namespace App\Http\Services\AccountMethod;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AccountMethod;
use Illuminate\Support\Facades\Validator;

class UpdateAccountMethodService
{
    public static function execute(Request $request, AccountMethod $accountMethod): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_name' => 'sometimes|required|string|max:255',
            'identifier' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:bank,wallet,crypto,other',
            'currency' => 'nullable|string|max:10',
            'holder_name' => 'sometimes|required|string|max:255',
            'holder_dni' => 'nullable|string|max:255',
            'network_or_type' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $accountMethod->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Account method updated successfully',
            'data' => $accountMethod
        ]);
    }
}