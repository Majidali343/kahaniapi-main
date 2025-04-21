<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kahani_id',
        'comment',
        'review_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with Reply
    public function replies()
    {
        return $this->hasMany(Reply::class, 'comment_id');
    }
}
