<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
    
       'coupon_code',
       'admin_id',
       'discount_percentage',
       'organization_stake',
       'status',
       'organization_request',
       'admin_paid'
    ];
}
