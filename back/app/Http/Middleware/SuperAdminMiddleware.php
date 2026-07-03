<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (Auth::check() && $user->role_id <= 2 && $user->is_admin) {
            return $next($request);
        }

        return response()->json(['message' => 'Acceso no autorizado'], 403);
    }
}