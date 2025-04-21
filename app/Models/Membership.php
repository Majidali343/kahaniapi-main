<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_id',
        'user_id',
        'package_id',
        'status',
        'Permissions',
        'membershipvalidity',
        'purchase_date',
        'coupon',

    ];

    

}
