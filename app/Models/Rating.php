<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kahani_id',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kahani()
    {
        return $this->belongsTo(Kahani::class);
    }
}
