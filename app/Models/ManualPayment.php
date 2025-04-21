<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'bank_name',
        'payment_image',
        'paidamount',
        'type',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
