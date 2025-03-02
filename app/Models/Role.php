<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->hasMany(User::class);
    }
}
