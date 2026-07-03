<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $hidden = [ 'created_at', 'updated_at' ];
    
    protected $fillable = [ 'title', 'menu_id', 'path', 'icon', 'sort' ];    
    
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
    
    public function childrenMenus()
    {
        return $this->hasMany(Menu::class)->with('childrenMenus');
    }
    
}
