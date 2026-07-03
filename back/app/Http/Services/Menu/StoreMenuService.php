<?php
namespace App\Http\Services\Menu;

use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Menu\FormMenuRequest;
use App\Models\Menu;

class StoreMenuService
{
 
  static public function execute(FormMenuRequest $request): \Illuminate\Http\JsonResponse
  { 

    try {
      Menu::create([
          "title" => $request->title,
          "menu_id" => null,
          "path" => $request->path,
          "icon" => $request->icon,
          "sort" => '0'
      ]);        
  
      return response()->json(["message"=> 'Se ha creado correctamente el menu.'], 201);
  } catch (\Exception $e) {
      return response()->json([
          'message' => 'Ocurrió un error al crear el menú'
      ], 500);
  }
  
  }
    
}
