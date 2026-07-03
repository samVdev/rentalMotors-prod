<?php
namespace App\Repositories\Menu;

class RecursiveMenuRepository
{
   /**
   * Return an array of recursive.
   *
   * @return Array
   */    
    static public function recursive(Array $menuIds = []): Array
    {
        $menus = \App\Models\Menu::whereNull("menu_id")
            ->when(!empty($menuIds), function ($query) use ($menuIds) {
                $query->whereIn("id", $menuIds);
            })
            ->orderBy('id')
            ->with([
                "childrenMenus" => function ($query) use ($menuIds) {
                    if (!empty($menuIds)) {
                        $query->whereIn("id", $menuIds);
                    }
                }
            ])
            ->get();

        return json_decode($menus);
    }    
}