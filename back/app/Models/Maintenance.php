<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'maintenances';

    protected $fillable = [
        'financing_id',
        'application_id',
        'responsable_id',
        'total',
        'status',
        'descripcion',
        'date',
        'type',
    ];

    protected $casts = [
        'total' => 'decimal:4',
        'date'  => 'date',
    ];

    public function financing()
    {
        return $this->belongsTo(Financing::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
