<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kahani extends Model
{
    use HasFactory;
    protected $primaryKey = 'kahani_id';
            
    protected $fillable = [
           'title',
           'description',
           'Duration',
           'free',
           'views',
           'audio',
           'video',
           'image',
           'thumbnail',
           'pg',
    ];
}
