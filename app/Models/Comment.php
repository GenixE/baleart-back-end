<?php

namespace App\Models;

use App\Models\Image;
use App\Models\User;
use App\Models\Space;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'comment',
        'score',
        'status',
        'space_id',
        'user_id'
    ];

    public function images() {
        return $this->hasMany(Image::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function space() {
        return $this->belongsTo(Space::class);
    }
}
