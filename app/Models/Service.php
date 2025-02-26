<?php

namespace App\Models;

use App\Models\Space;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = false;
    public function spaces()
    {
        return $this->belongsToMany(Space::class);
    }
}
