<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'cc',
        'color',
        'price',
        'mileage',
        'type',
        'show',
        'image',
        'user_id'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
