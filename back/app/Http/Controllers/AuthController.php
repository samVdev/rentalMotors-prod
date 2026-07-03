<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Auth\GetProfileService;
use App\Http\Services\Auth\UpdateProfileService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __invoke()
    {
        return new UserResource(Auth::user());
    }
    
    public function show()
    {
        return GetProfileService::index();
    }

    public function edit(UpdateProfileRequest $request)
    {
        return UpdateProfileService::index($request);
    }
}
