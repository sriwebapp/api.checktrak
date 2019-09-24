<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
