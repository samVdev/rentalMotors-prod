<?php

namespace App\Http\Services\Role;

//use Illuminate\Support\Facades\Redirect;
//use App\Http\Validator\Role\UpdateStoreRoleValidator;
//use App\Http\Requests\Role\StoreRoleRequest;
use Illuminate\Http\{
  Request,
  JsonResponse
};
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UpdateRoleService
{

  static public function execute(Request $request, Role $role): JsonResponse
  {

    $msg  = 'Información inválida.';

    $authUser = Auth::user();
    if ($role->id == 1 && $authUser->role_id != 1) {
      return response()->json([
        "message" => "El rol solicitado no se encuentra.",
      ], 404);
    }

    if (!$authUser->is_admin) {
      return response()->json(["message" => "No puedes editar roles."], 403);
    }

    //if ( !UpdateMenuValidator::rule( $request )->fails() ) {
    $role->update([
      "name" => $request->name,
      "menu_ids" => $request->menu_ids,
      "description" => $request->description,
      'created_admin' => $authUser->is_admin
    ]);
    $msg  = 'Rol actualizado.';
    //}

    return response()->json(["message" => $msg], 200);
  }
}
