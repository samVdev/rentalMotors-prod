<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $hidden = [ 'created_at', 'updated_at' ];

    protected $fillable = [ 'name', 'menu_ids', 'description', 'created_admin'];

    protected $casts = [
        'menu_ids' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
}
