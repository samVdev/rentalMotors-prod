<?php

namespace App\Http\Services\AccountMethod;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AccountMethod;
use Illuminate\Support\Facades\Validator;

class StoreAccountMethodService
{
    public static function execute(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'type' => 'required|in:bank,wallet,crypto,other',
            'currency' => 'nullable|string|max:10',
            'holder_name' => 'required|string|max:255',
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

        $data = $validator->validated();

        // Default to active if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $accountMethod = AccountMethod::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Account method created successfully',
            'data' => $accountMethod
        ], 201);
    }
}