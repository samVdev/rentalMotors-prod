<?php

namespace App\Http\Services\guest;

use App\Models\AccountMethod;
use Illuminate\Http\JsonResponse;

class GetActiveAccountMethods
{
    public static function execute(): JsonResponse
    {
        $methods = AccountMethod::where('is_active', true)->get();
        return response()->json($methods);
    }
}