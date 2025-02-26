<?php

namespace App\Models;

use App\Models\Address;
use App\Models\SpaceType;
use App\Models\Modality;
use App\Models\Service;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = [
        'name',
        'reg_number',
        'observation_CA',
        'observation_ES',
        'observation_EN',
        'email',
        'phone',
        'website',
        'accessType',
        'totalScore',
        'countScore',
        'address_id',
        'space_type_id',
        'user_id'
    ];

    public function spaceType()
    {
        return $this->belongsTo(SpaceType::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function modalities()
    {
        return $this->belongsToMany(Modality::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images()
    {
        return $this->hasManyThrough(Image::class, Comment::class);
    }
}
