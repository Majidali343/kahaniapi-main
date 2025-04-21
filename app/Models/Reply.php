<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with Review
    public function review()
    {
        return $this->belongsTo(Review::class, 'comment_id');
    }
}
