<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // public function incharge()
    // {
    //     return $this->belongsTo(User::class, 'incharge_id');
    // }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
