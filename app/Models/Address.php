<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\Municipality;
use App\Models\Space;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name', 'municipality_id', 'zone_id'
    ];

    public $timestamps = false;
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function space()
    {
        return $this->hasOne(Space::class);
    }
}
