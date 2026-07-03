<?php

namespace App\Http\Services\Role;

use Illuminate\Http\{
  Request,
  JsonResponse
};
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class DestroyRoleService
{

  static public function execute(Request $request): \Illuminate\Http\JsonResponse
  {

    //$msg  = 'Invalid argument.';
    $authUser = Auth::user();

    if ($request->id== 1 && $authUser->role_id != 1) {
      return response()->json([
        "message" => "El rol solicitado no se encuentra.",
      ], 404);
    }
    $role = Role::findOrFail($request->id);
    $role->delete();
    //$msg  = 'Role remove.';

    return response()->json(204);
  }
}
