<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'financing_id',
        'installment_number',
        'total',
        'description',
        'status',
        'file_path',
        'account_method_id',
        'mora_id',
        'total_capital',
        'total_interes',
        'interes_porcent',
        'created_at'
    ];

    public function financing()
    {
        return $this->belongsTo(Financing::class);
    }

    public function mora()
    {
        return $this->belongsTo(MoraRecord::class, 'mora_id');
    }
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function accountMethod()
    {
        return $this->belongsTo(AccountMethod::class);
    }
}