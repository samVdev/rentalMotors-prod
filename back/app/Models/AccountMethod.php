<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class AccountMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_name',
        'identifier',
        'type',
        'currency',
        'holder_name',
        'holder_dni',
        'network_or_type',
        'is_active',
        'notes',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}