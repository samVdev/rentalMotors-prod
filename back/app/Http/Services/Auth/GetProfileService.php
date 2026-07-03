<?php

namespace App\Http\Services\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GetProfileService
{
    static public function index(): JsonResponse
    {
        $authUser = Auth::user();
        if (!$authUser)
            return response()->json(['message' => 'No permitido'], 403);

        $persona = Auth::user()->persona;

        $user = [
            "id" => $authUser->id,
            "email" => $authUser->email,
            "name" => $persona->fullName,
            "tel" => $persona->phone,
        ];

        return response()->json($user, 200);

    }
}