<?php

namespace App\Http\Services\Role;

use App\Models\Role;
use App\Repositories\Menu\{
  ListMenuRepository,
  RecursiveMenuRepository
};

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class ShowRoleService
{

  static public function execute(Role $role): JsonResponse
  {
    $authUser = Auth::user();
    
    if ($role->id == 1 && $authUser->role_id != 1) {
      return response()->json([
        "message" => "El rol solicitado no se encuentra.",
      ], 404); 
    }

    $menus = ListMenuRepository::list(
      RecursiveMenuRepository::recursive()
    );

    return response()->json([
      "role"  => (object)$role->toArray(),
      "menus" => $menus,
    ]);
  }
}
