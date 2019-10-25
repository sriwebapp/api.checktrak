<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class)->where('active', 1);
    }

    public function groups()
    {
        return $this->hasMany(Group::class)->where('active', 1);
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
