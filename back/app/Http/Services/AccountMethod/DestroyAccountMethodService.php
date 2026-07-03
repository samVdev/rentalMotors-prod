<?php

namespace App\Http\Services\AccountMethod;

use Illuminate\Http\JsonResponse;
use App\Models\AccountMethod;

class DestroyAccountMethodService
{
    public static function execute(AccountMethod $accountMethod): JsonResponse
    {
        try {
            // Check if it has any related records before deleting if needed
            if ($accountMethod->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete. There are payments associated with this account method.'
                ], 403);
            }

            $accountMethod->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account method deleted successfully'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account method'
            ], 500);
        }
    }
}