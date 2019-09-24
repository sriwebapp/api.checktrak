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

    public function incharge()
    {
        return $this->belongsToMany(User::class, 'group_incharge', 'group_id');
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
