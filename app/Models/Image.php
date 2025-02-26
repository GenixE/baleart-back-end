<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'url',
        'comment_id'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
