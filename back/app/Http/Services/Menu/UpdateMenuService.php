<?php

namespace App\Http\Services\Menu;

use App\Http\Requests\Menu\FormMenuRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class UpdateMenuService
{

  static public function execute(FormMenuRequest $request, Menu $menu): JsonResponse
  {
    try {
      $menu->update($request->except('_method', 'id'));
      return response()->json(["message" => 'Se ha actualizado correctamente el menú.'], 200);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Ocurrió un error al actualizar el menú'
      ], 500);
    }
  }
}
