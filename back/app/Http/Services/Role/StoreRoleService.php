<?php
namespace App\Http\Services\Role;

//use App\Http\Validator\Role\StoreRoleValidator;
//use App\Http\Requests\Role\StoreRoleRequest;
use Illuminate\Http\{
    Request,
    JsonResponse
};
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class StoreRoleService
{

  static public function execute(Request $request): JsonResponse
  { 

      $msg  = 'Invalid data.';

      $authUser = Auth::user();

      if(!$authUser->is_admin ) {
          return response()->json(["message"=> "No puedes crear roles."], 403);
      }

      //if ( !StoreRoleValidator::rule( $request )->fails() ) {

          Role::create([              
              "name" => $request->name,
              "menu_ids" => $request->menu_ids,
              "description" => $request->description,
              'created_admin' => $authUser->is_admin
          ]);

          $msg  = 'Role stored.';

      //}

      return response()->json(["message"=> $msg], 201);

  }

}
