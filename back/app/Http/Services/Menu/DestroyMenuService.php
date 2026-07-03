<?php

namespace App\Http\Services\Menu;

use App\Http\Requests\Menu\DestroyMenuRequest;
use App\Models\Menu;

class DestroyMenuService
{

  static public function execute(DestroyMenuRequest $request): \Illuminate\Http\JsonResponse
  {
    try {
      $menu = Menu::findOrFail($request->id);
      $menu->delete();

      return response()->json(null, 204);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Ocurrió un error al eliminar el menú'
      ], 500);
    }
  }
}
