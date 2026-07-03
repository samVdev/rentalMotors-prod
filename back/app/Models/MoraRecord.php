<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MoraRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mora_records';

    protected $fillable = [
        'financing_id',
        'base_amount',
        'reconnection_fee',
        'total_amount',
        'percentage',
        'occurrence_index',
        'status',
    ];

    public function financing()
    {
        return $this->belongsTo(Financing::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'mora_id');
    }
}
